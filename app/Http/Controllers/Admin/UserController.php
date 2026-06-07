<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): Response
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $users = User::orderBy('id_usuario', 'desc')->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombre_usuario' => 'required|string|max:100',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'clave' => 'required|string|min:6',
            'rol' => 'required|in:doctor,paciente,admin',
            'foto' => 'nullable|image|max:2048',
        ], [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.unique' => 'Este correo ya está registrado en el sistema.',
            'clave.required' => 'La contraseña es obligatoria.',
            'clave.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'rol.required' => 'El rol es obligatorio.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La foto no debe pesar más de 2MB.',
        ]);

        $userData = [
            'nombre_usuario' => $validated['nombre_usuario'],
            'correo' => $validated['correo'],
            'clave' => Hash::make($validated['clave']),
            'rol' => $validated['rol'],
        ];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $userData['foto'] = 'uploads/avatars/' . $filename;
        }

        User::create($userData);

        return redirect()->back()->with('success', 'Usuario registrado exitosamente.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        // Prevent admin from deleting or demoting themselves by accident (optional validation but helpful)
        if ($usuario->id_usuario === auth()->user()->id_usuario && $request->rol !== 'admin') {
            return redirect()->back()->withErrors(['rol' => 'No puedes cambiar tu propio rol de administrador.']);
        }

        $validated = $request->validate([
            'nombre_usuario' => 'required|string|max:100',
            'correo' => 'required|email|max:100|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'rol' => 'required|in:doctor,paciente,admin',
            'clave' => 'nullable|string|min:6',
            'foto' => 'nullable|image|max:2048',
        ], [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.unique' => 'Este correo ya está registrado.',
            'rol.required' => 'El rol es obligatorio.',
            'clave.min' => 'La contraseña nueva debe tener al menos 6 caracteres.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La foto no debe pesar más de 2MB.',
        ]);

        $data = [
            'nombre_usuario' => $validated['nombre_usuario'],
            'correo' => $validated['correo'],
            'rol' => $validated['rol'],
        ];

        if (!empty($validated['clave'])) {
            $data['clave'] = Hash::make($validated['clave']);
        }

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($usuario->foto && file_exists(public_path($usuario->foto))) {
                @unlink(public_path($usuario->foto));
            }
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['foto'] = 'uploads/avatars/' . $filename;
        }

        $usuario->update($data);

        return redirect()->back()->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $usuario): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        // Prevent self deletion
        if ($usuario->id_usuario === auth()->user()->id_usuario) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $usuario->delete();

        return redirect()->back()->with('success', 'Usuario eliminado del sistema.');
    }
}
