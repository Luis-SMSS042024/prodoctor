<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';
    protected $primaryKey = 'id_especialidad';

    protected $fillable = [
        'nombre_especialidad',
        'descripcion',
    ];

    /**
     * Get the doctors for this specialty.
     */
    public function doctores()
    {
        return $this->hasMany(Doctor::class, 'id_especialidad', 'id_especialidad');
    }
}
