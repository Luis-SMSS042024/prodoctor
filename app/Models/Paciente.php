<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'id_usuario',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'direccion',
        'tipo_sangre',
    ];

    /**
     * Get the user account associated with the patient, if any.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the appointments of the patient.
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    /**
     * Get the procedures of the patient.
     */
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class, 'id_paciente', 'id_paciente');
    }
}
