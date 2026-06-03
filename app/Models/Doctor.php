<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctores';
    protected $primaryKey = 'id_doctor';

    protected $fillable = [
        'id_usuario',
        'id_especialidad',
        'nombres',
        'apellidos',
        'telefono',
        'junta_vigilancia',
    ];

    /**
     * Get the user account associated with the doctor.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the specialty of the doctor.
     */
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id_especialidad');
    }

    /**
     * Get the appointments for the doctor.
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_doctor', 'id_doctor');
    }

    /**
     * Get the procedures conducted by the doctor.
     */
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class, 'id_doctor', 'id_doctor');
    }
}
