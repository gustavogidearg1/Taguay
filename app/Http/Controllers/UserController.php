<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // 🔒 ABM Usuarios: solo admin
        // (requiere que esté registrado el middleware 'role' de Spatie en bootstrap/app.php)
        $this->middleware('role:admin');
    }

    /* =========================
       INDEX
    ========================== */
    public function index(Request $request)
    {
        $sort  = $request->get('sort', 'name');   // name | email | role
        $order = $request->get('order', 'asc');   // asc | desc

        $q = User::query()->with('roles');

        if ($sort === 'role') {
            // Ordenar por el primer rol asignado (model_has_roles + roles)
            $q->leftJoin('model_has_roles as mhr', function ($join) {
                    $join->on('mhr.model_id', '=', 'users.id')
                         ->where('mhr.model_type', '=', User::class);
                })
                ->leftJoin('roles', 'roles.id', '=', 'mhr.role_id')
                ->orderBy('roles.name', $order)
                ->select('users.*');
        } else {
            $allowed = ['name','email','id','created_at'];
            if (!in_array($sort, $allowed, true)) $sort = 'name';
            $q->orderBy($sort, $order);
        }

        $users = $q->paginate(10)->appends([
            'sort' => $sort,
            'order' => $order,
        ]);

        return view('users.index', compact('users', 'sort', 'order'));
    }

    /* =========================
       CREATE
    ========================== */
    public function create()
    {
        $roles = Role::query()->orderBy('name')->get();
        $perms = Permission::query()->orderBy('name')->get();

        return view('users.create', compact('roles', 'perms'));
    }

    /* =========================
       STORE
    ========================== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required','confirmed', Rules\Password::defaults()],

            // Spatie
            'roles'          => ['nullable','array'],
            'roles.*'        => ['string', 'exists:roles,name'],
            'permissions'    => ['nullable','array'],
            'permissions.*'  => ['string', 'exists:permissions,name'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Rol por defecto si no eligió
        $user->syncRoles($data['roles'] ?? ['cliente']);
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    /* =========================
       EDIT
    ========================== */
    public function edit(User $user)
    {
        $roles = Role::query()->orderBy('name')->get();
        $perms = Permission::query()->orderBy('name')->get();

        $currentRoles = $user->roles->pluck('name')->values()->all();
        $currentPerms = $user->getDirectPermissions()->pluck('name')->values()->all();
        // si querés mostrar permisos heredados por roles:
        // $allPerms = $user->getAllPermissions()->pluck('name')->all();

        return view('users.edit', compact('user','roles','perms','currentRoles','currentPerms'));
    }

    /* =========================
       UPDATE
    ========================== */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','confirmed', Rules\Password::defaults()],

            // Spatie
            'roles'          => ['nullable','array'],
            'roles.*'        => ['string', 'exists:roles,name'],
            'permissions'    => ['nullable','array'],
            'permissions.*'  => ['string', 'exists:permissions,name'],
        ]);

        $update = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        // Si querés forzar 1 rol base, está bien con syncRoles.
        // Si querés permitir varios roles, syncRoles igual funciona con array.
        $user->syncRoles($data['roles'] ?? ['cliente']);
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    /* =========================
       DESTROY
    ========================== */
    public function destroy(User $user)
    {
        // Opcional: no permitir borrarse a sí mismo
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
}
