<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    /**
     * Display a listing of the patients.
     */
    public function index(Request $request): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $query = Paciente::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('dui', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Doctor/Patients/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create(): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $doctores = Doctor::all()->map(function ($doc) {
            return [
                'id_doctor' => $doc->id_doctor,
                'nombre_completo' => 'Dr. ' . $doc->nombres . ' ' . $doc->apellidos,
            ];
        });

        return Inertia::render('Doctor/Patients/Create', [
            'doctores' => $doctores,
        ]);
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dui' => 'nullable|string|max:20|unique:pacientes,dui',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'tipo_sangre' => 'nullable|string|max:5',
            'alergias' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'contacto_emergencia_nombre' => 'nullable|string|max:100',
            'contacto_emergencia_parentesco' => 'nullable|string|max:50',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
        ]);

        Paciente::create($validated);

        return redirect()->route('doctor.patients.index')
            ->with('success', 'Paciente registrado exitosamente.');
    }

    /**
     * Display the specified patient medical record.
     */
    public function show(Paciente $paciente): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        // Cargar citas (con doctor y seguimientos), procedimientos e historial clínico ordenados
        $paciente->load([
            'citas' => function ($query) {
                $query->with(['doctor', 'seguimientos'])->orderBy('fecha_cita', 'desc')->orderBy('hora_cita', 'desc');
            },
            'procedimientos' => function ($query) {
                $query->with('doctor')->orderBy('fecha_procedimiento', 'desc');
            }
        ]);

        return Inertia::render('Doctor/Patients/Show', [
            'patient' => $paciente,
        ]);
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Paciente $paciente): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $doctores = Doctor::all()->map(function ($doc) {
            return [
                'id_doctor' => $doc->id_doctor,
                'nombre_completo' => 'Dr. ' . $doc->nombres . ' ' . $doc->apellidos,
            ];
        });

        return Inertia::render('Doctor/Patients/Edit', [
            'patient' => $paciente,
            'doctores' => $doctores,
        ]);
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dui' => 'nullable|string|max:20|unique:pacientes,dui,' . $paciente->id_paciente . ',id_paciente',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
            'tipo_sangre' => 'nullable|string|max:5',
            'alergias' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'contacto_emergencia_nombre' => 'nullable|string|max:100',
            'contacto_emergencia_parentesco' => 'nullable|string|max:50',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
        ]);

        $paciente->update($validated);

        return redirect()->route('doctor.patients.show', $paciente->id_paciente)
            ->with('success', 'Paciente actualizado exitosamente.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Paciente $paciente): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $paciente->delete();

        return redirect()->route('doctor.patients.index')
            ->with('success', 'Paciente eliminado del sistema.');
    }
}
