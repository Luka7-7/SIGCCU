<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionCongreso extends Model
{
    protected $table = 'inscripciones_congresos';
    public $timestamps = false;

    // Llave primaria compuesta
    protected $primaryKey = ['usuario_id', 'congreso_id'];
    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'congreso_id',
        'fecha_hora_inscripcion',
        'aceptado',
        'fecha_hora_respuesta',
        'rol_congreso',
    ];

    protected $casts = [
        'fecha_hora_inscripcion' => 'datetime',
        'fecha_hora_respuesta'   => 'datetime',
        'aceptado'               => 'boolean',
    ];

    // Soporte para llave primaria compuesta en Eloquent
    protected function setKeysForSaveQuery($query)
    {
        return $query
            ->where('usuario_id', $this->getAttribute('usuario_id'))
            ->where('congreso_id', $this->getAttribute('congreso_id'));
    }

    // Usuario inscrito
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Congreso al que se inscribió
    public function congreso(): BelongsTo
    {
        return $this->belongsTo(Congreso::class, 'congreso_id');
    }
}

