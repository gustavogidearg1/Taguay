<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Aplica el middleware solo a las acciones espec铆ficas
        $this->middleware('can:viewAny,App\Models\User')->only('index');
    }

public function index(Request $request)
{
    $sort = $request->get('sort', 'name');
    $order = $request->get('order', 'asc');

    // Si el orden es por 'role', necesitamos una relaci贸n con join
    if ($sort === 'role') {
        $users = User::with('role')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->orderBy('roles.name', $order)
            ->select('users.*') // importante para evitar conflicto en columnas
            ->paginate(10)
            ->appends(['sort' => $sort, 'order' => $order]);
    } else {
        $users = User::with('role')
            ->orderBy($sort, $order)
            ->paginate(10)
            ->appends(['sort' => $sort, 'order' => $order]);
    }

    return view('users.index', compact('users', 'sort', 'order'));
}


    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente');
    }

public function resetPassword(User $user)
{
    $nuevaClave = 'usuario123';
    $user->password = Hash::make($nuevaClave);
    $user->save();

    return redirect()->back()->with('success', 'Contraseña restablecida. Nueva clave: ' . $nuevaClave);
}



}
