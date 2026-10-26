<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    protected $table = 'Carrera';
    public $timestamps = false;

    protected $fillable = [
        'nombre_carrera',
        'unidad_academica',
        'area',
        'institucion',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'Carrera_id');
    }
}
