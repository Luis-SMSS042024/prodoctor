<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index(Request $request): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        // Fetch all appointments with patients and doctors
        $appointments = Cita::with(['paciente', 'doctor'])
            ->orderBy('fecha_cita', 'asc')
            ->orderBy('hora_cita', 'asc')
            ->get()
            ->map(function ($cita) {
                return [
                    'id_cita' => $cita->id_cita,
                    'id_paciente' => $cita->id_paciente,
                    'paciente_nombre' => $cita->paciente ? ($cita->paciente->nombres . ' ' . $cita->paciente->apellidos) : 'Paciente no registrado',
                    'id_doctor' => $cita->id_doctor,
                    'doctor_nombre' => $cita->doctor ? ('Dr. ' . $cita->doctor->nombres . ' ' . $cita->doctor->apellidos) : 'Sin asignar',
                    'fecha' => $cita->fecha_cita,
                    'hora' => Carbon::parse($cita->hora_cita)->format('H:i'),
                    'motivo' => $cita->motivo,
                    'estado' => $cita->estado,
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

        return Inertia::render('Doctor/Appointments/Index', [
            'appointments' => $appointments,
            'patients' => $patients,
        ]);
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        // Prefill doctor from authenticated user
        $doctor = Doctor::where('id_usuario', auth()->user()->id_usuario)->first();
        if (!$doctor) {
            // Fallback to first doctor if current user doesn't have doctor profile
            $doctor = Doctor::first();
        }

        $request->merge(['id_doctor' => $doctor ? $doctor->id_doctor : 1]);

        $validated = $request->validate([
            'id_paciente' => 'required|exists:pacientes,id_paciente',
            'id_doctor' => 'required|exists:doctores,id_doctor',
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required|string',
            'motivo' => 'required|string|max:255',
            'estado' => 'required|in:Pendiente,Confirmada,Atendida,Cancelada',
        ]);

        Cita::create($validated);

        return redirect()->back()->with('success', 'Cita agendada exitosamente.');
    }

    /**
     * Update the specified appointment status.
     */
    public function updateStatus(Request $request, Cita $cita): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'estado' => 'required|in:Pendiente,Confirmada,Atendida,Cancelada',
        ]);

        $cita->update(['estado' => $validated['estado']]);

        $mensajes = [
            'Confirmada' => 'Cita confirmada exitosamente.',
            'Atendida' => 'Cita marcada como atendida.',
            'Cancelada' => 'Cita cancelada.',
            'Pendiente' => 'Cita colocada en estado pendiente.',
        ];

        return redirect()->back()->with('success', $mensajes[$validated['estado']] ?? 'Estado de cita actualizado.');
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Cita $cita): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'fecha_cita' => 'required|date',
            'hora_cita' => 'required|string',
            'motivo' => 'required|string|max:255',
        ]);

        $cita->update($validated);

        return redirect()->back()->with('success', 'Cita modificada exitosamente.');
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Cita $cita): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $cita->delete();

        return redirect()->back()->with('success', 'Cita eliminada de la agenda.');
    }
}
