<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Procedimiento;
use App\Models\SeguimientoClinico;
use App\Models\DisponibilidadDoctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the Patient Portal Dashboard.
     */
    public function index(): Response
    {
        if (auth()->user()->rol !== 'paciente') {
            abort(403, 'Acceso no autorizado.');
        }

        $user = auth()->user();
        
        // Eagerly find or create the patient record linked to this user account
        $paciente = Paciente::where('id_usuario', $user->id_usuario)->first();
        if (!$paciente) {
            $paciente = Paciente::create([
                'id_usuario' => $user->id_usuario,
                'nombres' => $user->nombre_usuario,
                'apellidos' => '',
                'fecha_nacimiento' => '1990-01-01',
                'sexo' => 'Otro',
                'telefono' => 'Sin registrar',
                'direccion' => 'Sin registrar',
            ]);
        }

        $hoy = Carbon::today()->toDateString();

        // 1. Next Appointment
        $proximaCita = Cita::with('doctor')
            ->where('id_paciente', $paciente->id_paciente)
            ->where('fecha_cita', '>=', $hoy)
            ->whereIn('estado', ['Pendiente', 'Confirmada'])
            ->orderBy('fecha_cita', 'asc')
            ->orderBy('hora_cita', 'asc')
            ->first();

        // 2. Active follow-ups (recetas y observaciones)
        $seguimientos = SeguimientoClinico::whereHas('cita', function($q) use ($paciente) {
                $q->where('id_paciente', $paciente->id_paciente);
            })
            ->with('cita.doctor')
            ->orderBy('fecha_seguimiento', 'desc')
            ->get();

        // 3. Assigned Procedures
        $procedimientos = Procedimiento::with('doctor')
            ->where('id_paciente', $paciente->id_paciente)
            ->orderBy('fecha_procedimiento', 'desc')
            ->get();

        // 4. Past Appointments / Appointment History
        $historialCitas = Cita::with('doctor')
            ->where('id_paciente', $paciente->id_paciente)
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->get();

        // 5. Available Doctors with Specialties for Booking appointments
        $doctores = Doctor::with('especialidad')
            ->get()
            ->map(function ($doc) {
                return [
                    'id_doctor' => $doc->id_doctor,
                    'nombre_completo' => 'Dr. ' . $doc->nombres . ' ' . $doc->apellidos,
                    'especialidad' => $doc->especialidad ? $doc->especialidad->nombre_especialidad : 'Médico General',
                ];
            });

        return Inertia::render('Paciente/Dashboard', [
            'patient' => $paciente,
            'proximaCita' => $proximaCita ? [
                'id_cita' => $proximaCita->id_cita,
                'doctor_nombre' => 'Dr. ' . $proximaCita->doctor->nombres . ' ' . $proximaCita->doctor->apellidos,
                'fecha' => $proximaCita->fecha_cita,
                'hora' => Carbon::parse($proximaCita->hora_cita)->format('H:i'),
                'motivo' => $proximaCita->motivo,
                'estado' => $proximaCita->estado,
            ] : null,
            'seguimientos' => $seguimientos->map(function ($seg) {
                return [
                    'id_seguimiento' => $seg->id_seguimiento,
                    'fecha' => $seg->fecha_seguimiento,
                    'observaciones' => $seg->observaciones,
                    'tratamiento' => $seg->tratamiento,
                    'proxima_revision' => $seg->proxima_revision,
                    'doctor_nombre' => $seg->cita && $seg->cita->doctor ? ('Dr. ' . $seg->cita->doctor->nombres . ' ' . $seg->cita->doctor->apellidos) : 'Roberto Méndez',
                ];
            }),
            'procedimientos' => $procedimientos->map(function ($proc) {
                return [
                    'id_procedimiento' => $proc->id_procedimiento,
                    'nombre' => $proc->nombre_procedimiento,
                    'descripcion' => $proc->descripcion,
                    'fecha' => $proc->fecha_procedimiento,
                    'estado' => $proc->estado,
                    'doctor_nombre' => $proc->doctor ? ('Dr. ' . $proc->doctor->nombres . ' ' . $proc->doctor->apellidos) : 'Roberto Méndez',
                ];
            }),
            'historialCitas' => $historialCitas->map(function ($cita) {
                return [
                    'id_cita' => $cita->id_cita,
                    'doctor_nombre' => 'Dr. ' . $cita->doctor->nombres . ' ' . $cita->doctor->apellidos,
                    'fecha' => $cita->fecha_cita,
                    'hora' => Carbon::parse($cita->hora_cita)->format('H:i'),
                    'motivo' => $cita->motivo,
                    'estado' => $cita->estado,
                ];
            }),
            'doctores' => $doctores,
            'mustVerifyEmail' => auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Request a new appointment as a patient.
     */
    public function requestAppointment(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'paciente') {
            abort(403, 'Acceso no autorizado.');
        }

        $paciente = Paciente::where('id_usuario', auth()->user()->id_usuario)->firstOrFail();

        $request->validate([
            'id_doctor' => 'required|exists:doctores,id_doctor',
            'id_disponibilidad' => 'required|exists:disponibilidades_doctores,id_disponibilidad',
            'sintomas' => 'nullable|array',
            'sintomas.*' => 'string',
            'motivo' => 'required|string|max:255',
        ], [
            'id_doctor.required' => 'El doctor es obligatorio.',
            'id_disponibilidad.required' => 'El horario de la cita es obligatorio.',
            'motivo.required' => 'La descripción de los detalles es obligatoria.',
        ]);

        // Find the slot and ensure it is not already reserved
        $slot = DisponibilidadDoctor::where('id_disponibilidad', $request->id_disponibilidad)
            ->where('id_doctor', $request->id_doctor)
            ->where('reservado', false)
            ->firstOrFail();

        // Combine symptoms and description
        $sintomasStr = !empty($request->sintomas) ? 'Síntomas: ' . implode(', ', $request->sintomas) . '. ' : '';
        $motivoCompleto = $sintomasStr . 'Detalles: ' . $request->motivo;

        Cita::create([
            'id_paciente' => $paciente->id_paciente,
            'id_doctor' => $request->id_doctor,
            'fecha_cita' => $slot->fecha,
            'hora_cita' => $slot->hora,
            'motivo' => substr($motivoCompleto, 0, 255),
            'estado' => 'Pendiente',
        ]);

        // Mark slot as reserved
        $slot->update(['reservado' => true]);

        return redirect()->back()->with('success', 'Tu cita médica ha sido agendada con éxito.');
    }

    /**
     * Get available dates and times for a doctor.
     */
    public function getDoctorAvailability(Doctor $doctor): \Illuminate\Http\JsonResponse
    {
        $availabilities = DisponibilidadDoctor::where('id_doctor', $doctor->id_doctor)
            ->where('reservado', false)
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get()
            ->map(function ($slot) {
                return [
                    'id_disponibilidad' => $slot->id_disponibilidad,
                    'fecha' => $slot->fecha,
                    'hora' => Carbon::parse($slot->hora)->format('H:i'),
                ];
            });

        return response()->json($availabilities);
    }

    /**
     * Update patient profile from the portal.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'paciente') {
            abort(403, 'Acceso no autorizado.');
        }

        $paciente = Paciente::where('id_usuario', auth()->user()->id_usuario)->firstOrFail();

        $validated = $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'dui' => 'nullable|string|max:20|unique:pacientes,dui,' . $paciente->id_paciente . ',id_paciente',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:Masculino,Femenino,Otro',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255',
        ]);

        $paciente->update($validated);

        return redirect()->back()->with('success', 'Tus datos de perfil han sido actualizados exitosamente.');
    }
}
