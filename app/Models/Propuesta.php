<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Propuesta extends Model
{
    protected $table = 'propuestas';
    public $timestamps = false;

    protected $fillable = [
        'resumen_url',
        'extenso_url',
        'resumen_estado',
        'usuario_id',
        'congreso_id',
        'evaluador_id',
        'propuestascol',
    ];

    // Usuario que envió la propuesta
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Congreso al que pertenece la propuesta
    public function congreso(): BelongsTo
    {
        return $this->belongsTo(Congreso::class, 'congreso_id');
    }

    // Evaluador asignado a esta propuesta (puede ser null)
    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(Evaluador::class, 'evaluador_id');
    }

    // Comentarios de revisión de esta propuesta
    public function comentarios(): HasMany
    {
        return $this->hasMany(ComentarioPropuesta::class, 'propuestas_id');
    }
}

