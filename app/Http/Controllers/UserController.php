<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Permisos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // ABM Usuarios: solo admin
        $this->middleware('role:admin');
    }

    /* =========================
       INDEX
    ========================== */
    public function index(Request $request)
    {
        $search  = trim((string) $request->get('q', ''));
        $rol     = (string) $request->get('role');
        $reporte = (string) $request->get('reporte');
        $sort    = in_array($request->get('sort'), ['name', 'email'], true) ? $request->get('sort') : 'name';
        $order   = $request->get('order') === 'desc' ? 'desc' : 'asc';
        $perPage = (int) $request->get('per_page', 10);
        if (! in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 10;
        }

        $users = User::query()
            ->with(['roles', 'permissions'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(array_key_exists($rol, Permisos::ROLES), fn ($q) => $q->role($rol))
            // Un reporte se ve si está tildado o si el usuario es admin
            ->when(array_key_exists($reporte, Permisos::REPORTES), function ($q) use ($reporte) {
                $q->where(function ($w) use ($reporte) {
                    $w->whereHas('permissions', fn ($p) => $p->where('name', $reporte))
                      ->orWhereHas('roles', fn ($r) => $r->where('name', 'admin'));
                });
            })
            ->orderBy($sort, $order)
            ->paginate($perPage)
            ->withQueryString();

        return view('users.index', [
            'users'    => $users,
            'search'   => $search,
            'sort'     => $sort,
            'order'    => $order,
            'perPage'  => $perPage,
            'roles'    => Permisos::ROLES,
            'reportes' => Permisos::REPORTES,
        ]);
    }

    /* =========================
       CREATE
    ========================== */
    public function create()
    {
        return view('users.create', $this->datosFormulario());
    }

    /* =========================
       STORE
    ========================== */
    public function store(Request $request)
    {
        $this->normalizarTelefono($request);

        $data = $request->validate(array_merge([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], $this->reglasAccesos(), $this->reglasTelefono()), $this->mensajesTelefono());

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'telefono_whatsapp' => $data['telefono_whatsapp'] ?? null,
                'password' => Hash::make($data['password']),
            ]);

            $this->sincronizarAccesos($user, $data);
        });

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    /* =========================
       EDIT
    ========================== */
    public function edit(User $user)
    {
        return view('users.edit', array_merge($this->datosFormulario(), [
            'user'            => $user,
            'currentRoles'    => $user->roles->pluck('name')->all(),
            'currentReportes' => $user->getDirectPermissions()->pluck('name')->all(),
        ]));
    }

    /* =========================
       UPDATE
    ========================== */
    public function update(Request $request, User $user)
    {
        // La contraseña solo se cambia si se tildó "Cambiar contraseña".
        // Así el autocompletado del navegador no obliga a cambiar la clave.
        $cambiarPassword = $request->boolean('cambiar_password');

        $this->normalizarTelefono($request);

        $rules = array_merge([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], $this->reglasAccesos(), $this->reglasTelefono($user));

        if ($cambiarPassword) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $data = $request->validate($rules, $this->mensajesTelefono());

        // Evita que un admin se quite a sí mismo el rol de Administrador
        if ($user->is(auth()->user()) && ! in_array('admin', $data['roles'] ?? [], true)) {
            return back()->withInput()->with('error', 'No podés quitarte el rol de Administrador a vos mismo.');
        }

        DB::transaction(function () use ($user, $data, $cambiarPassword) {
            $update = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'telefono_whatsapp' => $data['telefono_whatsapp'] ?? null,
            ];

            if ($cambiarPassword) {
                $update['password'] = Hash::make($data['password']);
            }

            $user->update($update);

            $this->sincronizarAccesos($user, $data);
        });

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    /* =========================
       DESTROY
    ========================== */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No podés eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

    /* =========================
       RESET PASSWORD
    ========================== */
    public function resetPassword(User $user)
    {
        $nuevaClave = 'usuario123';
        $user->password = Hash::make($nuevaClave);
        $user->save();

        return redirect()->back()->with('success', 'Contraseña restablecida. Nueva clave: ' . $nuevaClave);
    }

    /* =========================
       HELPERS
    ========================== */

    /** Datos comunes para los formularios de alta y edición. */
    private function datosFormulario(): array
    {
        return [
            'roles'            => Permisos::ROLES,
            'rolesDescripcion' => Permisos::ROLES_DESCRIPCION,
            'reportes'         => Permisos::REPORTES,
        ];
    }

    /**
     * El teléfono se guarda como lo identifica WhatsApp: solo dígitos con código de país.
     * Se normaliza ANTES de validar, así la regla "unique" compara el mismo formato.
     */
    private function normalizarTelefono(Request $request): void
    {
        $request->merge(['telefono_whatsapp' => User::normalizarTelefono($request->input('telefono_whatsapp'))]);
    }

    private function reglasTelefono(?User $user = null): array
    {
        $unico = Rule::unique('users', 'telefono_whatsapp');
        if ($user) {
            $unico->ignore($user->id);
        }

        return ['telefono_whatsapp' => ['nullable', 'regex:/^\d{11,15}$/', $unico]];
    }

    private function mensajesTelefono(): array
    {
        return [
            'telefono_whatsapp.regex'  => 'El teléfono debe tener código de país y área, por ejemplo +54 9 3534 191741.',
            'telefono_whatsapp.unique' => 'Ese teléfono ya está asignado a otro usuario.',
        ];
    }

    /** Reglas de validación de roles y reportes. */
    private function reglasAccesos(): array
    {
        return [
            'roles'      => ['nullable', 'array'],
            'roles.*'    => ['string', Rule::in(array_keys(Permisos::ROLES))],
            'reportes'   => ['nullable', 'array'],
            'reportes.*' => ['string', Rule::in(array_keys(Permisos::REPORTES))],
        ];
    }

    /**
     * Asigna roles y reportes al usuario.
     * Los permisos de área (ver_agricola, etc.) los otorgan los roles: no se asignan directo.
     */
    private function sincronizarAccesos(User $user, array $data): void
    {
        $user->syncRoles($data['roles'] ?? []);
        $user->syncPermissions($data['reportes'] ?? []);
    }
}
