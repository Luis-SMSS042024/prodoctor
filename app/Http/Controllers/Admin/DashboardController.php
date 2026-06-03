<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Especialidad;
use App\Models\Paciente;
use App\Models\Cita;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(): Response
    {
        if (auth()->user()->rol !== 'admin') {
            abort(403, 'Acceso no autorizado.');
        }

        $especialidadesCount = Especialidad::count();
        $doctoresCount = Doctor::count();
        $usuariosCount = User::count();
        $pacientesCount = Paciente::count();

        // Fetch recent appointments for activity feed
        $recentAppointments = Cita::with(['paciente', 'doctor'])
            ->orderBy('fecha_cita', 'desc')
            ->orderBy('hora_cita', 'desc')
            ->take(5)
            ->get()
            ->map(function ($cita) {
                return [
                    'id_cita' => $cita->id_cita,
                    'paciente' => $cita->paciente ? ($cita->paciente->nombres . ' ' . $cita->paciente->apellidos) : 'Paciente no registrado',
                    'doctor' => $cita->doctor ? ('Dr. ' . $cita->doctor->nombres . ' ' . $cita->doctor->apellidos) : 'Sin asignar',
                    'fecha' => $cita->fecha_cita,
                    'estado' => $cita->estado,
                ];
            });

        // Fetch recent registered users
        $recentUsers = User::orderBy('id_usuario', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'especialidades' => $especialidadesCount,
                'doctores' => $doctoresCount,
                'usuarios' => $usuariosCount,
                'pacientes' => $pacientesCount,
            ],
            'recentAppointments' => $recentAppointments,
            'recentUsers' => $recentUsers,
        ]);
    }
}
