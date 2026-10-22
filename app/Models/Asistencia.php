<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $table = 'asistencias';
    public $timestamps = false;

    // Llave primaria compuesta
    protected $primaryKey = ['usuario_id', 'conferencia_taller_id'];
    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'conferencia_taller_id',
        'fecha_hora',
        'ip',
        'Activo',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'Activo'     => 'boolean',
    ];

    // Soporte para llave primaria compuesta en Eloquent
    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('usuario_id', $this->getAttribute('usuario_id'))
            ->where('conferencia_taller_id', $this->getAttribute('conferencia_taller_id'));
    }

    // Usuario que registró asistencia
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Conferencia o taller al que asistió
    public function conferenciaTaller(): BelongsTo
    {
        return $this->belongsTo(ConferenciaTaller::class, 'conferencia_taller_id');
    }
}

