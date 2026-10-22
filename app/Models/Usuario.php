<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    public $timestamps = false; // la tabla solo tiene creado_en (lo llena MySQL)

    protected $fillable = [
        'correo_electronico',
        'contrasena',
        'nombres',
        'apellidos',
        'rol',
        'institucion',
        'matricula_numtrabajador',
        'unidad_academica',
        'nombre_carrera',
        'area',
        'activo',
    ];

    protected $hidden = ['contrasena'];

    protected $casts = [
        'activo'    => 'boolean',
        'creado_en' => 'datetime',
    ];

    // Para que Auth use tu columna "contrasena"
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // La tabla no tiene columna remember_token
    public function getRememberTokenName()
    {
        return '';
    }

    public function getEmailForPasswordReset()
    {
        return $this->correo_electronico;
    }

    // ── Relaciones ────────────────────────────────────────────

    // Congresos a los que está inscrito (muchos a muchos)
    public function congresos(): BelongsToMany
    {
        return $this->belongsToMany(Congreso::class, 'inscripciones_congresos', 'usuario_id', 'congreso_id')
                    ->withPivot('fecha_hora_inscripcion', 'aceptado', 'fecha_hora_respuesta', 'rol_congreso');
    }

    // Inscripciones a congresos (acceso directo a la tabla pivote)
    public function inscripcionesCongresos(): HasMany
    {
        return $this->hasMany(InscripcionCongreso::class, 'usuario_id');
    }

    // Conferencias/talleres a los que está inscrito (muchos a muchos)
    public function conferenciasTalleres(): BelongsToMany
    {
        return $this->belongsToMany(ConferenciaTaller::class, 'inscripciones_conferencias_talleres', 'usuario_id', 'conferencia_taller_id')
                    ->withPivot('fecha_hora_inscripcion', 'aceptado', 'fecha_hora_respuesta', 'tipo_participacion');
    }

    // Inscripciones a conferencias/talleres (acceso directo a la tabla pivote)
    public function inscripcionesConferenciasTalleres(): HasMany
    {
        return $this->hasMany(InscripcionConferenciaTaller::class, 'usuario_id');
    }

    // Asistencias registradas
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'usuario_id');
    }

    // Constancias emitidas
    public function constancias(): HasMany
    {
        return $this->hasMany(Constancia::class, 'usuario_id');
    }

    // Perfil de evaluador (uno a uno, puede no existir)
    public function evaluador(): HasOne
    {
        return $this->hasOne(Evaluador::class, 'usuario_id');
    }

    // Propuestas enviadas
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class, 'usuario_id');
    }

    // Nombre completo (accesor de conveniencia)
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
