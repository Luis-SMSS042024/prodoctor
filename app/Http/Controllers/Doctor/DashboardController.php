<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Procedimiento;
use App\Models\SeguimientoClinico;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the Doctor's Dashboard.
     */
    public function index(): Response
    {
        // Enforce doctor role
        if (auth()->user()->rol !== 'doctor') {
            abort(403, 'Acceso no autorizado.');
        }

        $doctor = Doctor::where('id_usuario', auth()->user()->id_usuario)->first();
        $doctorId = $doctor ? $doctor->id_doctor : null;

        $hoy = Carbon::today()->toDateString();

        // 1. Fetch Stats
        $pacientesTotales = Paciente::count();
        
        $citasHoyQuery = Cita::query();
        $citasPendientesQuery = Cita::query();
        $procedimientosQuery = Procedimiento::query();
        $seguimientosQuery = SeguimientoClinico::query();

        if ($doctorId) {
            $citasHoyQuery->where('id_doctor', $doctorId);
            $citasPendientesQuery->where('id_doctor', $doctorId);
            $procedimientosQuery->where('id_doctor', $doctorId);
            $seguimientosQuery->whereHas('cita', function($q) use ($doctorId) {
                $q->where('id_doctor', $doctorId);
            });
        }

        $citasHoy = (clone $citasHoyQuery)->where('fecha_cita', $hoy)->count();
        $citasPendientesCount = (clone $citasPendientesQuery)->where('estado', 'Pendiente')->count();
        $procedimientosCount = $procedimientosQuery->count();
        $seguimientosCount = $seguimientosQuery->count();

        // 2. Fetch Today's Appointments with patient relationship
        $citas = (clone $citasHoyQuery)->with('paciente')
            ->where('fecha_cita', $hoy)
            ->orderBy('hora_cita', 'asc')
            ->get()
            ->map(function ($cita) {
                return [
                    'id_cita' => $cita->id_cita,
                    'paciente' => $cita->paciente ? ($cita->paciente->nombres . ' ' . $cita->paciente->apellidos) : 'Paciente no registrado',
                    'hora' => Carbon::parse($cita->hora_cita)->format('H:i'),
                    'tipo' => $cita->motivo,
                    'estado' => $cita->estado,
                ];
            });

        // 3. Fetch All Pending Appointments for Confirmation (Today and Future)
        $citasPendientes = (clone $citasPendientesQuery)->with('paciente')
            ->where('estado', 'Pendiente')
            ->orderBy('fecha_cita', 'asc')
            ->orderBy('hora_cita', 'asc')
            ->get()
            ->map(function ($cita) {
                return [
                    'id_cita' => $cita->id_cita,
                    'paciente' => $cita->paciente ? ($cita->paciente->nombres . ' ' . $cita->paciente->apellidos) : 'Paciente no registrado',
                    'fecha' => Carbon::parse($cita->fecha_cita)->format('d/m/Y'),
                    'hora' => Carbon::parse($cita->hora_cita)->format('H:i'),
                    'motivo' => $cita->motivo,
                    'estado' => $cita->estado,
                ];
            });

        return Inertia::render('Doctor/Dashboard', [
            'stats' => [
                'pacientes_totales' => $pacientesTotales,
                'citas_hoy' => $citasHoy,
                'citas_hoy_pendientes' => $citasPendientesCount, // Now reflects all pending appointments for the doctor
                'procedimientos' => $procedimientosCount,
                'seguimientos' => $seguimientosCount,
            ],
            'citas' => $citas,
            'citasPendientes' => $citasPendientes,
        ]);
    }
}
