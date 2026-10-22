<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sede extends Model
{
    protected $table = 'sedes';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'estado',
        'url_ubicacion',
    ];

    // Congresos que se realizan en esta sede (muchos a muchos)
    public function congresos(): BelongsToMany
    {
        return $this->belongsToMany(Congreso::class, 'congresos_sedes', 'sede_id', 'congreso_id');
    }
}

