<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InscripcionConferenciaTaller extends Model
{
    protected $table = 'inscripciones_conferencias_talleres';
    public $timestamps = false;

    // Llave primaria compuesta
    protected $primaryKey = ['usuario_id', 'conferencia_taller_id'];
    public $incrementing = false;

    protected $fillable = [
        'usuario_id',
        'conferencia_taller_id',
        'fecha_hora_inscripcion',
        'aceptado',
        'fecha_hora_respuesta',
        'tipo_participacion',
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
            ->where('conferencia_taller_id', $this->getAttribute('conferencia_taller_id'));
    }

    // Usuario inscrito
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Conferencia o taller al que se inscribió
    public function conferenciaTaller(): BelongsTo
    {
        return $this->belongsTo(ConferenciaTaller::class, 'conferencia_taller_id');
    }

    // Asistencia registrada para esta inscripción
    public function asistencia(): HasOne
    {
        return $this->hasOne(Asistencia::class, 'usuario_id', 'usuario_id')
                    ->where('conferencia_taller_id', $this->conferencia_taller_id);
    }
}

