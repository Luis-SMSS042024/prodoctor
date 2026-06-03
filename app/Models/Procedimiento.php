<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    use HasFactory;

    protected $table = 'procedimientos';
    protected $primaryKey = 'id_procedimiento';

    protected $fillable = [
        'id_paciente',
        'id_doctor',
        'nombre_procedimiento',
        'descripcion',
        'fecha_procedimiento',
        'estado',
    ];

    /**
     * Get the patient associated with the procedure.
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    /**
     * Get the doctor associated with the procedure.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
}
