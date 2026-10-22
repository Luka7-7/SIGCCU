<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Congreso extends Model
{
    protected $table = 'congresos';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'siglas',
        'edicion',
        'fecha_inicio',
        'fecha_fin',
        'registro_abre',
        'registro_cierre',
        'descripcion',
        'logo_url',
        'minimo_talleres',
        'minimo_conferencias',
        'eje_tematico',
        'dictamen',
    ];

    protected $casts = [
        'fecha_inicio'   => 'date',
        'fecha_fin'      => 'date',
        'registro_abre'  => 'datetime',
        'registro_cierre'=> 'datetime',
    ];

    // Sedes asociadas a este congreso (muchos a muchos)
    public function sedes(): BelongsToMany
    {
        return $this->belongsToMany(Sede::class, 'congresos_sedes', 'congreso_id', 'sede_id');
    }

    // Conferencias y talleres de este congreso
    public function conferenciasTalleres(): HasMany
    {
        return $this->hasMany(ConferenciaTaller::class, 'congreso_id');
    }

    // Inscripciones de usuarios a este congreso
    public function inscripciones(): HasMany
    {
        return $this->hasMany(InscripcionCongreso::class, 'congreso_id');
    }

    // Usuarios inscritos a este congreso (muchos a muchos a través de inscripciones_congresos)
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'inscripciones_congresos', 'congreso_id', 'usuario_id')
                    ->withPivot('fecha_hora_inscripcion', 'aceptado', 'fecha_hora_respuesta', 'rol_congreso');
    }

    // Propuestas enviadas a este congreso
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class, 'congreso_id');
    }

    // Constancias emitidas para este congreso
    public function constancias(): HasMany
    {
        return $this->hasMany(Constancia::class, 'congreso_id');
    }
}

