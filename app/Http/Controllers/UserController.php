<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:administrador,empleado',
        ], [
            'name.required'      => 'Ingrese el nombre.',
            'email.required'     => 'Ingrese el correo.',
            'email.email'        => 'Ingrese un correo válido.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.required'  => 'Ingrese la contraseña.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'role.required'      => 'Seleccione un rol.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        // Evitar que el admin se cambie su propio rol
        if ($usuario->id === auth()->id() && $request->role !== 'administrador') {
            return back()->withErrors(['role' => 'No puedes cambiar tu propio rol de administrador.'])->withInput();
        }

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'role'  => 'required|in:administrador,empleado',
        ];

        $messages = [
            'name.required'  => 'Ingrese el nombre.',
            'email.required' => 'Ingrese el correo.',
            'email.email'    => 'Ingrese un correo válido.',
            'email.unique'   => 'Este correo ya está en uso por otro usuario.',
            'role.required'  => 'Seleccione un rol.',
        ];

        if ($request->filled('password')) {
            $rules['password']              = 'min:6|confirmed';
            $messages['password.min']       = 'La contraseña debe tener al menos 6 caracteres.';
            $messages['password.confirmed'] = 'Las contraseñas no coinciden.';
        }

        $request->validate($rules, $messages);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado.');
    }
}
