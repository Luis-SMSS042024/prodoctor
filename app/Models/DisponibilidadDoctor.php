<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisponibilidadDoctor extends Model
{
    protected $table = 'disponibilidades_doctores';
    protected $primaryKey = 'id_disponibilidad';

    protected $fillable = [
        'id_doctor',
        'fecha',
        'hora',
        'reservado',
    ];

    protected $casts = [
        'reservado' => 'boolean',
    ];

    /**
     * Get the doctor associated with this availability slot.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'id_doctor', 'id_doctor');
    }
}
