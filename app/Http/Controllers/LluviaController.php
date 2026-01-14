<?php

namespace App\Http\Controllers;

use App\Mail\LluviaCreadaMail;
use App\Models\Establecimiento;
use App\Models\Lluvia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class LluviaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // (opcional) si querés reforzar acá también:
        // $this->middleware('permission:ver_agricola');

        // Esto protege show/edit/update/destroy con policy automáticamente
        // (Necesitás el LluviaPolicy abajo, te lo dejo también)
        $this->authorizeResource(Lluvia::class, 'lluvia');
    }

    /**
     * Admin ve todo. No-admin ve solo sus registros.
     */
    private function applyOwnershipScope($query)
    {
        $u = Auth::user();

        // Admin ve todo
        if ($u && $u->hasRole('admin')) {
            return $query;
        }

        // No-admin: solo lo suyo
        return $query->where('user_id', Auth::id());
    }

    /**
     * Copia el archivo desde storage/app/public/... hacia public/storage/...
     * para hosting donde el symlink (storage:link) está bloqueado.
     */
    private function publishToPublicStorage(string $relativePath): void
    {
        $source = Storage::disk('public')->path($relativePath);
        $dest   = public_path('storage/' . $relativePath);

        @mkdir(dirname($dest), 0755, true);

        if (is_file($source)) {
            @copy($source, $dest);
            @chmod($dest, 0644);
        }
    }

    private function deletePublicCopy(?string $relativePath): void
    {
        if (!$relativePath) return;

        $dest = public_path('storage/' . $relativePath);
        if (is_file($dest)) {
            @unlink($dest);
        }
    }

    public function index(Request $request)
    {
        // Para filtros (admin ve todos, no-admin igual puede filtrar sus datos)
        $establecimientos = Establecimiento::orderBy('nombre')->get();

        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        if (!$desde && !$hasta) {
            $desde = now()->startOfYear()->toDateString();
            $hasta = now()->endOfYear()->toDateString();
        }

        $q = Lluvia::query()->with(['establecimiento', 'user']);

        // 👇 SOLO LO SUYO si no es admin
        $q = $this->applyOwnershipScope($q);

        if ($request->filled('establecimiento_id')) {
            $q->where('establecimiento_id', $request->establecimiento_id);
        }

        if ($desde) $q->whereDate('fecha', '>=', $desde);
        if ($hasta) $q->whereDate('fecha', '<=', $hasta);

        $rows = (clone $q)
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(20)
            ->withQueryString();

        // Gráficos
        $base = clone $q;

        $porEstablecimiento = (clone $base)
            ->reorder()
            ->selectRaw('establecimiento_id, SUM(mm) as total_mm')
            ->groupBy('establecimiento_id')
            ->with('establecimiento:id,nombre')
            ->get()
            ->map(fn($x) => [
                'establecimiento' => $x->establecimiento->nombre ?? '—',
                'mm' => round((float)$x->total_mm, 1),
            ])
            ->sortByDesc('mm')
            ->values();

        $porMes = (clone $base)
            ->reorder()
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(mm) as total_mm")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($x) => [
                'mes' => $x->mes,
                'mm'  => round((float)$x->total_mm, 1),
            ]);

        return view('abm.lluvias.index', compact(
            'establecimientos',
            'rows',
            'porEstablecimiento',
            'porMes',
            'desde',
            'hasta'
        ));
    }

    public function create()
    {
        $establecimientos = Establecimiento::orderBy('nombre')->get(['id', 'nombre']);
        return view('abm.lluvias.create', compact('establecimientos'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'establecimiento_id' => ['required', 'exists:establecimientos,id'],
            'fecha'              => ['required', 'date'],
            'hora'               => ['nullable', 'date_format:H:i'],
            'mm'                 => ['required', 'numeric', 'between:0,1000'],
            'fuente'             => ['required', 'in:manual,automatico'],
            'observador'         => ['nullable', 'string', 'max:120'],
            'comentario'         => ['nullable', 'string'],
            'estacion_nombre'    => ['nullable', 'string', 'max:120'],
            'lat'                => ['nullable', 'numeric', 'between:-90,90'],
            'lng'                => ['nullable', 'numeric', 'between:-180,180'],
            'archivo'            => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        if ($r->hasFile('archivo')) {
            $data['archivo_path'] = $r->file('archivo')->store('lluvias', 'public');
            $this->publishToPublicStorage($data['archivo_path']);
        }

        $data['user_id'] = Auth::id();

        if (Schema::hasColumn('lluvias', 'company_id')) {
            $data['company_id'] = Auth::user()?->company_id;
        }

        $lluvia = Lluvia::create($data);

        // Email: To creador, CC fijo
        try {
            $creator = Auth::user();
            $toEmail = $creator?->email;
            $ccEmail = 'gustavog@live.com.ar';

            $mailable = new LluviaCreadaMail($lluvia);

            if ($toEmail) {
                $mailer = Mail::to($toEmail);
                if (strcasecmp($toEmail, $ccEmail) !== 0) {
                    $mailer->cc($ccEmail);
                }
                $mailer->send($mailable);
            } else {
                Mail::to($ccEmail)->send($mailable);
            }

            \Log::info('Lluvia mail enviado OK');
        } catch (\Throwable $e) {
            \Log::error('Lluvia mail error: ' . $e->getMessage());
        }

        return redirect()->route('lluvias.show', $lluvia)->with('ok', 'Registro de lluvia creado.');
    }

    public function show(Lluvia $lluvia)
    {
        $lluvia->load('establecimiento', 'user');
        return view('abm.lluvias.show', compact('lluvia'));
    }

    public function edit(Lluvia $lluvia)
    {
        $establecimientos = Establecimiento::orderBy('nombre')->get(['id', 'nombre']);
        return view('abm.lluvias.edit', compact('lluvia', 'establecimientos'));
    }

    public function update(Request $r, Lluvia $lluvia)
    {
        $data = $r->validate([
            'establecimiento_id' => ['required', 'exists:establecimientos,id'],
            'fecha'              => ['required', 'date'],
            'hora'               => ['nullable', 'date_format:H:i'],
            'mm'                 => ['required', 'numeric', 'between:0,1000'],
            'fuente'             => ['required', 'in:manual,automatico'],
            'observador'         => ['nullable', 'string', 'max:120'],
            'comentario'         => ['nullable', 'string'],
            'estacion_nombre'    => ['nullable', 'string', 'max:120'],
            'lat'                => ['nullable', 'numeric', 'between:-90,90'],
            'lng'                => ['nullable', 'numeric', 'between:-180,180'],
            'archivo'            => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        if ($r->hasFile('archivo')) {
            if ($lluvia->archivo_path) {
                Storage::disk('public')->delete($lluvia->archivo_path);
                $this->deletePublicCopy($lluvia->archivo_path);
            }

            $data['archivo_path'] = $r->file('archivo')->store('lluvias', 'public');
            $this->publishToPublicStorage($data['archivo_path']);
        }

        $lluvia->update($data);

        return redirect()->route('lluvias.show', $lluvia)->with('ok', 'Registro de lluvia actualizado.');
    }

    public function destroy(Lluvia $lluvia)
    {
        if ($lluvia->archivo_path) {
            Storage::disk('public')->delete($lluvia->archivo_path);
            $this->deletePublicCopy($lluvia->archivo_path);
        }

        $lluvia->delete();

        return redirect()->route('lluvias.index')->with('ok', 'Registro de lluvia eliminado.');
    }

    public function resendMail(Lluvia $lluvia)
    {
        try {
            $lluvia->load('establecimiento', 'user');

            $toEmail = $lluvia->user?->email;
            $ccEmail = 'gustavog@live.com.ar';

            $mailable = new LluviaCreadaMail($lluvia);

            if ($toEmail) {
                $mailer = Mail::to($toEmail);
                if (strcasecmp($toEmail, $ccEmail) !== 0) {
                    $mailer->cc($ccEmail);
                }

                if (config('queue.default') !== 'sync') {
                    $mailer->queue($mailable);
                } else {
                    $mailer->send($mailable);
                }
            } else {
                if (config('queue.default') !== 'sync') {
                    Mail::to($ccEmail)->queue($mailable);
                } else {
                    Mail::to($ccEmail)->send($mailable);
                }
            }

            return back()->with('ok', 'Correo reenviado correctamente.');
        } catch (\Throwable $e) {
            \Log::error('Reenvío Lluvia mail error: ' . $e->getMessage());
            return back()->with('error', 'No se pudo reenviar el correo. Revisá el log.');
        }
    }
}
