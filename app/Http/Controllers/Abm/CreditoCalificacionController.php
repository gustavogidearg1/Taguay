<?php

namespace App\Http\Controllers\Abm;

use App\Http\Controllers\Controller;
use App\Models\CreditoCalificacion;
use App\Models\Financiera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreditoCalificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    private function rules(): array
    {
        // Para decimales: aceptamos "2.500.000.000,00" o "2500000000.00"
        // Normalizamos antes de validar numérico (ver normalizeMoney()).
        return [
            'tipo_credito'   => ['nullable','string','max:50'],
            'financiera_id'  => ['required','exists:financieras,id'],
            'fecha'          => ['nullable','date'],
            'vencimiento'    => ['nullable','date'],

            'calif_total_pesos' => ['nullable','numeric'],
            'calif_total_usd'   => ['nullable','numeric'],
            'usado_total_pesos' => ['nullable','numeric'],
            'usado_total_usd'   => ['nullable','numeric'],
            'disp_total_pesos'  => ['nullable','numeric'],
            'disp_total_usd'    => ['nullable','numeric'],

            'sola_firma'     => ['nullable','numeric'],
            'obs_firma'      => ['nullable','string','max:100'],

            'prest_inm_pesos_lp_usado' => ['nullable','numeric'],
            'prest_inm_pesos_lp_disp'  => ['nullable','numeric'],
            'prest_inm_pesos_cp_usado' => ['nullable','numeric'],
            'prest_inm_pesos_cp_disp'  => ['nullable','numeric'],

            'acuerdo_descubierto_ctacte_usado' => ['nullable','numeric'],
            'acuerdo_descubierto_ctacte_disp'  => ['nullable','numeric'],

            'prest_inm_usd_lp_usado' => ['nullable','numeric'],
            'prest_inm_usd_lp_disp'  => ['nullable','numeric'],
            'prest_inm_usd_cp_usado' => ['nullable','numeric'],
            'prest_inm_usd_cp_disp'  => ['nullable','numeric'],

            'fin_galicia_nera_usd_cp_usado'   => ['nullable','numeric'],
            'fin_galicia_nera_usd_cp_disp'    => ['nullable','numeric'],
            'fin_galicia_nera_pesos_cp_usado' => ['nullable','numeric'],
            'fin_galicia_nera_pesos_cp_disp'  => ['nullable','numeric'],

            'prendarios_pesos_usado' => ['nullable','numeric'],
            'prendarios_pesos_disp'  => ['nullable','numeric'],
            'prendarios_usd_usado'   => ['nullable','numeric'],
            'prendarios_usd_disp'    => ['nullable','numeric'],

            'garant_sgr_pesos_usado' => ['nullable','numeric'],
            'garant_sgr_pesos_disp'  => ['nullable','numeric'],
            'garant_sgr_usd_usado'   => ['nullable','numeric'],
            'garant_sgr_usd_disp'    => ['nullable','numeric'],

            'imagen'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'observacion'   => ['nullable','string'],
        ];
    }

    private function normalizeMoney(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        if ($v === '') return null;

        // Caso AR: 2.500.000.000,00 -> 2500000000.00
        $v = str_replace('.', '', $v);
        $v = str_replace(',', '.', $v);

        return $v;
    }

    private function normalizeMoneyFields(array $data): array
    {
        $moneyFields = [
            'calif_total_pesos','calif_total_usd',
            'usado_total_pesos','usado_total_usd',
            'disp_total_pesos','disp_total_usd',
            'sola_firma',

            'prest_inm_pesos_lp_usado','prest_inm_pesos_lp_disp',
            'prest_inm_pesos_cp_usado','prest_inm_pesos_cp_disp',

            'acuerdo_descubierto_ctacte_usado','acuerdo_descubierto_ctacte_disp',

            'prest_inm_usd_lp_usado','prest_inm_usd_lp_disp',
            'prest_inm_usd_cp_usado','prest_inm_usd_cp_disp',

            'fin_galicia_nera_usd_cp_usado','fin_galicia_nera_usd_cp_disp',
            'fin_galicia_nera_pesos_cp_usado','fin_galicia_nera_pesos_cp_disp',

            'prendarios_pesos_usado','prendarios_pesos_disp',
            'prendarios_usd_usado','prendarios_usd_disp',

            'garant_sgr_pesos_usado','garant_sgr_pesos_disp',
            'garant_sgr_usd_usado','garant_sgr_usd_disp',
        ];

        foreach ($moneyFields as $f) {
            if (array_key_exists($f, $data)) {
                $data[$f] = $this->normalizeMoney($data[$f]);
            }
        }

        return $data;
    }

    public function index(Request $request)
    {
        $q = $request->query('q', '');

        $items = CreditoCalificacion::with('financiera')
            ->when($q, function ($query) use ($q) {
                $query->where('tipo_credito', 'like', "%{$q}%")
                      ->orWhereHas('financiera', fn($qq) => $qq->where('name','like',"%{$q}%"));
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('abm.creditos_calificaciones.index', compact('items', 'q'));
    }

    public function create()
    {
        $financieras = Financiera::orderBy('name')->get();
        return view('abm.creditos_calificaciones.create', compact('financieras'));
    }

    public function store(Request $request)
    {
        $input = $this->normalizeMoneyFields($request->all());
        $data = validator($input, $this->rules())->validate();

        // Imagen
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('creditos', 'public');
        }

        $item = CreditoCalificacion::create($data);

        return redirect()->route('creditos-calificaciones.show', $item)
            ->with('success', 'Calificación creada.');
    }

    public function show(CreditoCalificacion $creditos_calificacione)
    {
        $item = $creditos_calificacione->load('financiera');
        return view('abm.creditos_calificaciones.show', compact('item'));
    }

    public function edit(CreditoCalificacion $creditos_calificacione)
    {
        $item = $creditos_calificacione;
        $financieras = Financiera::orderBy('name')->get();

        return view('abm.creditos_calificaciones.edit', compact('item','financieras'));
    }

    public function update(Request $request, CreditoCalificacion $creditos_calificacione)
    {
        $item = $creditos_calificacione;

        $input = $this->normalizeMoneyFields($request->all());
        $data = validator($input, $this->rules())->validate();

        if ($request->hasFile('imagen')) {
            if ($item->imagen) Storage::disk('public')->delete($item->imagen);
            $data['imagen'] = $request->file('imagen')->store('creditos', 'public');
        }

        $item->update($data);

        return redirect()->route('creditos-calificaciones.show', $item)
            ->with('success', 'Calificación actualizada.');
    }

    public function destroy(CreditoCalificacion $creditos_calificacione)
    {
        $item = $creditos_calificacione;

        if ($item->imagen) Storage::disk('public')->delete($item->imagen);

        $item->delete();

        return redirect()->route('creditos-calificaciones.index')->with('success', 'Calificación eliminada.');
    }
}
