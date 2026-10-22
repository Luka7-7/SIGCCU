<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Constancia extends Model
{
    protected $table = 'constancias';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'congreso_id',
        'conferencia_taller_id',
        'url_constancia',
    ];

    // Usuario dueño de la constancia
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Congreso al que corresponde la constancia (puede ser null)
    public function congreso(): BelongsTo
    {
        return $this->belongsTo(Congreso::class, 'congreso_id');
    }

    // Conferencia/taller al que corresponde la constancia (puede ser null)
    public function conferenciaTaller(): BelongsTo
    {
        return $this->belongsTo(ConferenciaTaller::class, 'conferencia_taller_id');
    }
}

