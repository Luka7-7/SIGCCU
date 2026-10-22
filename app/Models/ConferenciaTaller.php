<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenciaTaller extends Model
{
    protected $table = 'conferencias_talleres';
    public $timestamps = false;

    protected $fillable = [
        'descripcion',
        'tipo',
        'cupo',
        'ubicacion',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'congreso_id',
        'qr_link',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // Congreso al que pertenece esta conferencia/taller
    public function congreso(): BelongsTo
    {
        return $this->belongsTo(Congreso::class, 'congreso_id');
    }

    // Inscripciones a esta conferencia/taller
    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionConferenciaTaller::class, 'conferencia_taller_id');
    }

    // Usuarios inscritos (muchos a muchos a través de inscripciones_conferencias_talleres)
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'inscripciones_conferencias_talleres', 'conferencia_taller_id', 'usuario_id')
                    ->withPivot('fecha_hora_inscripcion', 'aceptado', 'fecha_hora_respuesta', 'tipo_participacion');
    }

    // Asistencias registradas para esta conferencia/taller
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'conferencia_taller_id');
    }

    // Constancias emitidas para esta conferencia/taller
    public function constancias(): HasMany
    {
        return $this->hasMany(Constancia::class, 'conferencia_taller_id');
    }
}

