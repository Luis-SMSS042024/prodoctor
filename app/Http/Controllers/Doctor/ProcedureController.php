<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Procedimiento;
use App\Models\Paciente;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ProcedureController extends Controller
{
    /**
     * Display a listing of procedures.
     */
    public function index(Request $request): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        // Fetch procedures with patient and doctor relationship
        $procedures = Procedimiento::with(['paciente', 'doctor'])
            ->orderBy('fecha_procedimiento', 'desc')
            ->get()
            ->map(function ($proc) {
                return [
                    'id_procedimiento' => $proc->id_procedimiento,
                    'id_paciente' => $proc->id_paciente,
                    'paciente_nombre' => $proc->paciente ? ($proc->paciente->nombres . ' ' . $proc->paciente->apellidos) : 'Paciente no registrado',
                    'id_doctor' => $proc->id_doctor,
                    'doctor_nombre' => $proc->doctor ? ('Dr. ' . $proc->doctor->nombres . ' ' . $proc->doctor->apellidos) : 'Sin asignar',
                    'nombre_procedimiento' => $proc->nombre_procedimiento,
                    'descripcion' => $proc->descripcion,
                    'fecha' => $proc->fecha_procedimiento,
                    'estado' => $proc->estado,
                ];
            });

        // Fetch all patients for creation dropdown
        $patients = Paciente::orderBy('apellidos', 'asc')
            ->orderBy('nombres', 'asc')
            ->get()
            ->map(function ($pac) {
                return [
                    'id_paciente' => $pac->id_paciente,
                    'nombre_completo' => $pac->nombres . ' ' . $pac->apellidos . ' (' . ($pac->dui ?: 'Sin DUI') . ')',
                ];
            });

        return Inertia::render('Doctor/Procedures/Index', [
            'procedures' => $procedures,
            'patients' => $patients,
        ]);
    }

    /**
     * Store a newly created procedure.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        // Prefill doctor from authenticated user
        $doctor = Doctor::where('id_usuario', auth()->user()->id_usuario)->first();
        if (!$doctor) {
            $doctor = Doctor::first();
        }

        $request->merge(['id_doctor' => $doctor ? $doctor->id_doctor : 1]);

        $validated = $request->validate([
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_doctor' => 'required|exists:doctores,id_doctor',
            'nombre_procedimiento' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'fecha_procedimiento' => 'required|date',
            'estado' => 'required|in:Pendiente,En proceso,Finalizado',
        ]);

        Procedimiento::create($validated);

        return redirect()->back()->with('success', 'Procedimiento registrado exitosamente.');
    }

    /**
     * Update the specified procedure.
     */
    public function update(Request $request, Procedimiento $procedimiento): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'nombre_procedimiento' => 'required|string|max:100',
            'descripcion' => 'required|string',
            'fecha_procedimiento' => 'required|date',
            'estado' => 'required|in:Pendiente,En proceso,Finalizado',
        ]);

        $procedimiento->update($validated);

        return redirect()->back()->with('success', 'Procedimiento modificado exitosamente.');
    }

    /**
     * Update the specified procedure status.
     */
    public function updateStatus(Request $request, Procedimiento $procedimiento): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'estado' => 'required|in:Pendiente,En proceso,Finalizado',
        ]);

        $procedimiento->update(['estado' => $validated['estado']]);

        $mensajes = [
            'Pendiente' => 'Procedimiento marcado como pendiente.',
            'En proceso' => 'Procedimiento marcado en proceso.',
            'Finalizado' => 'Procedimiento finalizado exitosamente.',
        ];

        return redirect()->back()->with('success', $mensajes[$validated['estado']] ?? 'Estado de procedimiento actualizado.');
    }

    /**
     * Remove the specified procedure.
     */
    public function destroy(Procedimiento $procedimiento): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $procedimiento->delete();

        return redirect()->back()->with('success', 'Procedimiento eliminado del expediente.');
    }
}
