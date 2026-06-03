<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\SeguimientoClinico;
use App\Models\Cita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class ClinicalFollowUpController extends Controller
{
    /**
     * Display a listing of clinical follow-ups.
     */
    public function index(): Response
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        // Fetch follow-ups with nested relations
        $followups = SeguimientoClinico::with(['cita.paciente', 'cita.doctor'])
            ->orderBy('fecha_seguimiento', 'desc')
            ->get()
            ->map(function ($follow) {
                $cita = $follow->cita;
                $paciente = $cita ? $cita->paciente : null;
                $doctor = $cita ? $cita->doctor : null;
                return [
                    'id_seguimiento' => $follow->id_seguimiento,
                    'id_cita' => $follow->id_cita,
                    'paciente_nombre' => $paciente ? ($paciente->nombres . ' ' . $paciente->apellidos) : 'Paciente no registrado',
                    'doctor_nombre' => $doctor ? ('Dr. ' . $doctor->nombres . ' ' . $doctor->apellidos) : 'Sin asignar',
                    'fecha_cita' => $cita ? $cita->fecha_cita : '',
                    'hora_cita' => $cita ? Carbon::parse($cita->hora_cita)->format('H:i') : '',
                    'motivo_cita' => $cita ? $cita->motivo : '',
                    'observaciones' => $follow->observaciones,
                    'tratamiento' => $follow->tratamiento,
                    'fecha_seguimiento' => $follow->fecha_seguimiento,
                    'proxima_revision' => $follow->proxima_revision,
                ];
            });

        // Fetch appointments for dropdown selector
        $appointments = Cita::with('paciente')
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->get()
            ->map(function ($cita) {
                $fechaStr = Carbon::parse($cita->fecha_cita)->format('d/m/Y');
                $horaStr = Carbon::parse($cita->hora_cita)->format('H:i');
                $pacienteStr = $cita->paciente ? ($cita->paciente->nombres . ' ' . $cita->paciente->apellidos) : 'Paciente no registrado';
                return [
                    'id_cita' => $cita->id_cita,
                    'label' => "{$fechaStr} {$horaStr} - {$pacienteStr} (Motivo: {$cita->motivo})",
                ];
            });

        return Inertia::render('Doctor/FollowUps/Index', [
            'followups' => $followups,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Store a newly created clinical follow-up.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'id_cita' => 'required|exists:citas,id_cita',
            'observaciones' => 'required|string',
            'tratamiento' => 'required|string',
            'fecha_seguimiento' => 'required|date',
            'proxima_revision' => 'nullable|date|after_or_equal:fecha_seguimiento',
        ]);

        SeguimientoClinico::create($validated);

        // Optional: Update the appointment state to 'Atendida' automatically when follow-up is saved!
        $cita = Cita::find($validated['id_cita']);
        if ($cita && $cita->estado !== 'Atendida') {
            $cita->update(['estado' => 'Atendida']);
        }

        return redirect()->back()->with('success', 'Seguimiento clínico registrado y cita marcada como Atendida.');
    }

    /**
     * Update the specified clinical follow-up.
     */
    public function update(Request $request, SeguimientoClinico $seguimiento): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $validated = $request->validate([
            'observaciones' => 'required|string',
            'tratamiento' => 'required|string',
            'fecha_seguimiento' => 'required|date',
            'proxima_revision' => 'nullable|date|after_or_equal:fecha_seguimiento',
        ]);

        $seguimiento->update($validated);

        return redirect()->back()->with('success', 'Seguimiento clínico modificado exitosamente.');
    }

    /**
     * Remove the specified clinical follow-up.
     */
    public function destroy(SeguimientoClinico $seguimiento): RedirectResponse
    {
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $seguimiento->delete();

        return redirect()->back()->with('success', 'Seguimiento clínico eliminado.');
    }
}
