<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Cita;
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

        $hoy = Carbon::today()->toDateString();

        // 1. Fetch Stats
        $pacientesTotales = Paciente::count();
        $citasHoy = Cita::where('fecha_cita', $hoy)->count();
        $citasHoyPendientes = Cita::where('fecha_cita', $hoy)->where('estado', 'Pendiente')->count();
        $procedimientosCount = Procedimiento::count();
        $seguimientosCount = SeguimientoClinico::count();

        // 2. Fetch Today's Appointments with patient relationship
        $citas = Cita::with('paciente')
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

        return Inertia::render('Doctor/Dashboard', [
            'stats' => [
                'pacientes_totales' => $pacientesTotales,
                'citas_hoy' => $citasHoy,
                'citas_hoy_pendientes' => $citasHoyPendientes,
                'procedimientos' => $procedimientosCount,
                'seguimientos' => $seguimientosCount,
            ],
            'citas' => $citas,
        ]);
    }
}
