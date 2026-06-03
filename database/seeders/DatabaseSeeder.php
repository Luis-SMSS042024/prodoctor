<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Especialidad;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Procedimiento;
use App\Models\SeguimientoClinico;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Usuarios
        $admin = User::create([
            'nombre_usuario' => 'Administrador ProDoctor',
            'correo' => 'admin@prodoctor.com',
            'clave' => Hash::make('admin123'),
            'rol' => 'admin',
        ]);

        $userDoc1 = User::create([
            'nombre_usuario' => 'Dr. Roberto Méndez',
            'correo' => 'doctor1@prodoctor.com',
            'clave' => Hash::make('doctor123'),
            'rol' => 'doctor',
        ]);

        $userDoc2 = User::create([
            'nombre_usuario' => 'Dra. Elena Rostova',
            'correo' => 'doctor2@prodoctor.com',
            'clave' => Hash::make('doctor123'),
            'rol' => 'doctor',
        ]);

        $userPac1 = User::create([
            'nombre_usuario' => 'Ana María Martínez',
            'correo' => 'paciente1@prodoctor.com',
            'clave' => Hash::make('paciente123'),
            'rol' => 'paciente',
        ]);

        $userPac2 = User::create([
            'nombre_usuario' => 'José López Gomez',
            'correo' => 'paciente2@prodoctor.com',
            'clave' => Hash::make('paciente123'),
            'rol' => 'paciente',
        ]);

        $userPac3 = User::create([
            'nombre_usuario' => 'Carmen Rivas',
            'correo' => 'paciente3@prodoctor.com',
            'clave' => Hash::make('paciente123'),
            'rol' => 'paciente',
        ]);

        // 2. Seed Especialidades
        $espMedGeneral = Especialidad::create([
            'nombre_especialidad' => 'Medicina General',
            'descripcion' => 'Atención médica primaria para adultos y niños.',
        ]);

        $espCardiologia = Especialidad::create([
            'nombre_especialidad' => 'Cardiología',
            'descripcion' => 'Prevención, diagnóstico y tratamiento de enfermedades cardiovasculares.',
        ]);

        $espPediatria = Especialidad::create([
            'nombre_especialidad' => 'Pediatría',
            'descripcion' => 'Atención médica y cuidado de la salud para niños y adolescentes.',
        ]);

        $espDermatologia = Especialidad::create([
            'nombre_especialidad' => 'Dermatología',
            'descripcion' => 'Cuidado de la piel, cabello y uñas.',
        ]);

        // 3. Seed Doctores
        $docMendez = Doctor::create([
            'id_usuario' => $userDoc1->id_usuario,
            'id_especialidad' => $espMedGeneral->id_especialidad,
            'nombres' => 'Roberto',
            'apellidos' => 'Méndez',
            'telefono' => '7000-1111',
            'junta_vigilancia' => 'JV-12345',
        ]);

        $docRostova = Doctor::create([
            'id_usuario' => $userDoc2->id_usuario,
            'id_especialidad' => $espCardiologia->id_especialidad,
            'nombres' => 'Elena',
            'apellidos' => 'Rostova',
            'telefono' => '7000-2222',
            'junta_vigilancia' => 'JV-54321',
        ]);

        // 4. Seed Pacientes
        $pacAna = Paciente::create([
            'id_usuario' => $userPac1->id_usuario,
            'dui' => '02345678-9',
            'nombres' => 'Ana María',
            'apellidos' => 'Martínez',
            'fecha_nacimiento' => '1992-05-15',
            'sexo' => 'Femenino',
            'telefono' => '7000-0041',
            'direccion' => 'Colonia San Benito, Pasaje A #123, San Salvador',
            'tipo_sangre' => 'O+',
            'alergias' => 'Penicilina, látex',
            'observaciones' => 'Paciente femenina con antecedentes de migraña frecuente.',
            'contacto_emergencia_nombre' => 'Carlos Martínez',
            'contacto_emergencia_parentesco' => 'Familiar (Hermano)',
            'contacto_emergencia_telefono' => '7111-2222',
        ]);

        $pacJose = Paciente::create([
            'id_usuario' => $userPac2->id_usuario,
            'dui' => '08765432-1',
            'nombres' => 'José',
            'apellidos' => 'López Gomez',
            'fecha_nacimiento' => '1988-11-20',
            'sexo' => 'Masculino',
            'telefono' => '7000-0037',
            'direccion' => 'Santa Tecla, 4a Calle Poniente #5, La Libertad',
            'tipo_sangre' => 'A+',
            'alergias' => 'Ninguna conocida',
            'observaciones' => 'Paciente masculino hipertenso controlado.',
            'contacto_emergencia_nombre' => 'Sofía de López',
            'contacto_emergencia_parentesco' => 'Cónyuge',
            'contacto_emergencia_telefono' => '7222-3333',
        ]);

        $pacCarmen = Paciente::create([
            'id_usuario' => $userPac3->id_usuario,
            'dui' => '01234567-8',
            'nombres' => 'Carmen',
            'apellidos' => 'Rivas',
            'fecha_nacimiento' => '2001-02-28',
            'sexo' => 'Femenino',
            'telefono' => '7000-0055',
            'direccion' => 'Urbanización Prados de Venecia, Soyapango',
            'tipo_sangre' => 'O-',
            'alergias' => 'Polen, polvo de ácaros',
            'observaciones' => 'Paciente con escoliosis y dolores lumbares frecuentes.',
            'contacto_emergencia_nombre' => 'María Rivas',
            'contacto_emergencia_parentesco' => 'Familiar (Madre)',
            'contacto_emergencia_telefono' => '7333-4444',
        ]);

        // 5. Seed Citas
        $hoy = Carbon::today()->toDateString();
        $manana = Carbon::tomorrow()->toDateString();

        $cita1 = Cita::create([
            'id_paciente' => $pacAna->id_paciente,
            'id_doctor' => $docMendez->id_doctor,
            'fecha_cita' => $hoy,
            'hora_cita' => '08:00:00',
            'motivo' => 'Consulta de control general y chequeo anual.',
            'estado' => 'Confirmada',
        ]);

        $cita2 = Cita::create([
            'id_paciente' => $pacJose->id_paciente,
            'id_doctor' => $docMendez->id_doctor,
            'fecha_cita' => $hoy,
            'hora_cita' => '09:30:00',
            'motivo' => 'Seguimiento de presión arterial y ajuste de dosis.',
            'estado' => 'Confirmada',
        ]);

        $cita3 = Cita::create([
            'id_paciente' => $pacCarmen->id_paciente,
            'id_doctor' => $docMendez->id_doctor,
            'fecha_cita' => $hoy,
            'hora_cita' => '11:00:00',
            'motivo' => 'Revisión por dolor lumbar severo.',
            'estado' => 'Pendiente',
        ]);

        $cita4 = Cita::create([
            'id_paciente' => $pacAna->id_paciente,
            'id_doctor' => $docRostova->id_doctor,
            'fecha_cita' => $manana,
            'hora_cita' => '10:00:00',
            'motivo' => 'Evaluación cardiológica e interpretación de electrocardiograma.',
            'estado' => 'Confirmada',
        ]);

        // 6. Seed Procedimientos
        Procedimiento::create([
            'id_paciente' => $pacAna->id_paciente,
            'id_doctor' => $docMendez->id_doctor,
            'nombre_procedimiento' => 'Control de Hipertensión Arterial',
            'descripcion' => 'Monitoreo continuo de PA durante 24 horas y ajuste de dosis de Enalapril.',
            'fecha_procedimiento' => $hoy,
            'estado' => 'En proceso',
        ]);

        Procedimiento::create([
            'id_paciente' => $pacJose->id_paciente,
            'id_doctor' => $docRostova->id_doctor,
            'nombre_procedimiento' => 'Electrocardiograma de Reposo',
            'descripcion' => 'Realización de electrocardiograma (ECG) de 12 derivaciones en reposo.',
            'fecha_procedimiento' => $hoy,
            'estado' => 'Finalizado',
        ]);

        // 7. Seed Seguimiento Clínico
        SeguimientoClinico::create([
            'id_cita' => $cita2->id_cita,
            'observaciones' => 'El paciente José López presenta lecturas de presión arterial más estables (130/85). Refiere buen apego al tratamiento y cambios leves en hábitos de dieta.',
            'tratamiento' => 'Continuar con Losartán 50mg diario por la mañana. Realizar caminatas de 30 minutos.',
            'fecha_seguimiento' => $hoy,
            'proxima_revision' => Carbon::today()->addDays(15)->toDateString(),
        ]);
    }
}
