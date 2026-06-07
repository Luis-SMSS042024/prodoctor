<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Especialidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors.
     */
    public function index(): Response
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $doctors = Doctor::with(['usuario', 'especialidad'])
            ->get()
            ->map(function ($doc) {
                return [
                    'id_doctor' => $doc->id_doctor,
                    'id_usuario' => $doc->id_usuario,
                    'nombre_usuario' => $doc->usuario ? $doc->usuario->nombre_usuario : '',
                    'correo' => $doc->usuario ? $doc->usuario->correo : '',
                    'foto' => $doc->usuario ? $doc->usuario->foto : null,
                    'id_especialidad' => $doc->id_especialidad,
                    'nombre_especialidad' => $doc->especialidad ? $doc->especialidad->nombre_especialidad : 'Sin especialidad',
                    'nombres' => $doc->nombres,
                    'apellidos' => $doc->apellidos,
                    'telefono' => $doc->telefono,
                    'junta_vigilancia' => $doc->junta_vigilancia,
                ];
            });

        $specialties = Especialidad::orderBy('nombre_especialidad', 'asc')
            ->get()
            ->map(function ($esp) {
                return [
                    'id_especialidad' => $esp->id_especialidad,
                    'nombre_especialidad' => $esp->nombre_especialidad,
                ];
            });

        return Inertia::render('Admin/Doctors/Index', [
            'doctors' => $doctors,
            'specialties' => $specialties,
        ]);
    }

    /**
     * Store a newly created doctor.
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
            'id_especialidad' => 'required|exists:especialidades,id_especialidad',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'junta_vigilancia' => 'required|string|max:50|unique:doctores,junta_vigilancia',
            'foto' => 'nullable|image|max:2048',
        ], [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.unique' => 'Este correo ya está registrado por otro usuario.',
            'clave.required' => 'La contraseña es obligatoria.',
            'clave.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'id_especialidad.required' => 'Debes seleccionar una especialidad.',
            'nombres.required' => 'Los nombres del doctor son obligatorios.',
            'apellidos.required' => 'Los apellidos del doctor son obligatorios.',
            'telefono.required' => 'El teléfono de contacto es obligatorio.',
            'junta_vigilancia.required' => 'El código de Junta de Vigilancia es obligatorio.',
            'junta_vigilancia.unique' => 'Este código de Junta de Vigilancia ya está registrado.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La foto no debe pesar más de 2MB.',
        ]);

        // 1. Create associated user data
        $userData = [
            'nombre_usuario' => $validated['nombre_usuario'],
            'correo' => $validated['correo'],
            'clave' => Hash::make($validated['clave']),
            'rol' => 'doctor',
        ];

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $userData['foto'] = 'uploads/avatars/' . $filename;
        }

        $user = User::create($userData);

        // 2. Create doctor profile
        Doctor::create([
            'id_usuario' => $user->id_usuario,
            'id_especialidad' => $validated['id_especialidad'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'],
            'junta_vigilancia' => $validated['junta_vigilancia'],
        ]);

        return redirect()->back()->with('success', 'Médico registrado exitosamente con su cuenta de acceso.');
    }

    /**
     * Update the specified doctor.
     */
    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombre_usuario' => 'required|string|max:100',
            'correo' => 'required|email|max:100|unique:usuarios,correo,' . $doctor->id_usuario . ',id_usuario',
            'id_especialidad' => 'required|exists:especialidades,id_especialidad',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'required|string|max:20',
            'junta_vigilancia' => 'required|string|max:50|unique:doctores,junta_vigilancia,' . $doctor->id_doctor . ',id_doctor',
            'foto' => 'nullable|image|max:2048',
        ], [
            'nombre_usuario.required' => 'El nombre de usuario es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.unique' => 'Este correo ya está registrado.',
            'id_especialidad.required' => 'Debes seleccionar una especialidad.',
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'junta_vigilancia.required' => 'El código de Junta de Vigilancia es obligatorio.',
            'junta_vigilancia.unique' => 'Este código de Junta de Vigilancia ya está registrado.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La foto no debe pesar más de 2MB.',
        ]);

        // 1. Update associated user
        $user = $doctor->usuario;
        if ($user) {
            $userData = [
                'nombre_usuario' => $validated['nombre_usuario'],
                'correo' => $validated['correo'],
            ];

            if ($request->hasFile('foto')) {
                if ($user->foto && file_exists(public_path($user->foto))) {
                    @unlink(public_path($user->foto));
                }
                $file = $request->file('foto');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/avatars'), $filename);
                $userData['foto'] = 'uploads/avatars/' . $filename;
            }

            $user->update($userData);
        }

        // 2. Update doctor profile
        $doctor->update([
            'id_especialidad' => $validated['id_especialidad'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'telefono' => $validated['telefono'],
            'junta_vigilancia' => $validated['junta_vigilancia'],
        ]);

        return redirect()->back()->with('success', 'Información del doctor y cuenta de acceso actualizadas.');
    }

    /**
     * Remove the specified doctor.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        // Remove user account (cascades or deleted manually)
        $user = $doctor->usuario;
        
        $doctor->delete();
        
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', 'Médico y su cuenta de acceso eliminados del sistema.');
    }
}
