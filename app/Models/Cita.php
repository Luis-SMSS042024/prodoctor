<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';
    protected $primaryKey = 'id_cita';

    protected $fillable = [
        'id_paciente',
        'id_doctor',
        'fecha_cita',
        'hora_cita',
        'motivo',
        'estado',
    ];

    /**
     * Get the patient associated with the appointment.
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    /**
     * Get the doctor associated with the appointment.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }

    /**
     * Get the clinical follow-ups generated from this appointment.
     */
    public function seguimientos()
    {
        return $this->hasMany(SeguimientoClinico::class, 'id_cita', 'id_cita');
    }
}
