<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\PatientController as DoctorPatientController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\ProcedureController as DoctorProcedureController;
use App\Http\Controllers\Doctor\ClinicalFollowUpController as DoctorFollowUpController;
use App\Http\Controllers\Paciente\DashboardController as PatientDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SpecialtyController as AdminSpecialtyController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

Route::get('/dashboard', function () {
    $rol = auth()->user()->rol;
    if ($rol === 'doctor') {
        return redirect()->route('doctor.dashboard');
    } elseif ($rol === 'paciente') {
        return redirect()->route('paciente.dashboard');
    } elseif ($rol === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorDashboardController::class, 'index'])->name('doctor.dashboard');
    
    // CRUD Pacientes del Doctor
    Route::resource('/doctor/patients', DoctorPatientController::class)->names([
        'index' => 'doctor.patients.index',
        'create' => 'doctor.patients.create',
        'store' => 'doctor.patients.store',
        'show' => 'doctor.patients.show',
        'edit' => 'doctor.patients.edit',
        'update' => 'doctor.patients.update',
        'destroy' => 'doctor.patients.destroy',
    ]);

    // Agenda de Citas del Doctor
    Route::resource('/doctor/appointments', DoctorAppointmentController::class)->only([
        'index', 'store', 'update', 'destroy'
    ])->names([
        'index' => 'doctor.appointments.index',
        'store' => 'doctor.appointments.store',
        'update' => 'doctor.appointments.update',
        'destroy' => 'doctor.appointments.destroy',
    ])->parameters([
        'appointments' => 'cita'
    ]);
    Route::patch('/doctor/appointments/{cita}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('doctor.appointments.status');
    
    // Procedimientos
    Route::resource('/doctor/procedures', DoctorProcedureController::class)->only([
        'index', 'store', 'update', 'destroy'
    ])->names([
        'index' => 'doctor.procedures.index',
        'store' => 'doctor.procedures.store',
        'update' => 'doctor.procedures.update',
        'destroy' => 'doctor.procedures.destroy',
    ])->parameters([
        'procedures' => 'procedimiento'
    ]);
    Route::patch('/doctor/procedures/{procedimiento}/status', [DoctorProcedureController::class, 'updateStatus'])->name('doctor.procedures.status');

    // Seguimiento Clínico
    Route::resource('/doctor/followups', DoctorFollowUpController::class)->only([
        'index', 'store', 'update', 'destroy'
    ])->names([
        'index' => 'doctor.followups.index',
        'store' => 'doctor.followups.store',
        'update' => 'doctor.followups.update',
        'destroy' => 'doctor.followups.destroy',
    ])->parameters([
        'followups' => 'seguimiento'
    ]);
    
    // Portal de Paciente
    Route::get('/paciente/dashboard', [PatientDashboardController::class, 'index'])->name('paciente.dashboard');
    Route::post('/paciente/appointments', [PatientDashboardController::class, 'requestAppointment'])->name('paciente.appointments.store');
    Route::put('/paciente/profile', [PatientDashboardController::class, 'updateProfile'])->name('paciente.profile.update');

    // Portal de Administrador
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // CRUD Especialidades
    Route::resource('/admin/specialties', AdminSpecialtyController::class)->names([
        'index' => 'admin.specialties.index',
        'store' => 'admin.specialties.store',
        'update' => 'admin.specialties.update',
        'destroy' => 'admin.specialties.destroy',
    ])->parameters([
        'specialties' => 'especialidad'
    ])->only(['index', 'store', 'update', 'destroy']);

    // CRUD Doctores
    Route::resource('/admin/doctors', AdminDoctorController::class)->names([
        'index' => 'admin.doctors.index',
        'store' => 'admin.doctors.store',
        'update' => 'admin.doctors.update',
        'destroy' => 'admin.doctors.destroy',
    ])->parameters([
        'doctors' => 'doctor'
    ])->only(['index', 'store', 'update', 'destroy']);

    // CRUD Usuarios
    Route::resource('/admin/users', AdminUserController::class)->names([
        'index' => 'admin.users.index',
        'store' => 'admin.users.store',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ])->parameters([
        'users' => 'usuario'
    ])->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
