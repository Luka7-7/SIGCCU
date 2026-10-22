<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluador extends Model
{
    protected $table = 'evaluadores';
    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'licenciatura',
        'maestria',
        'doctorado',
    ];

    // Usuario asociado a este perfil de evaluador
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Propuestas asignadas a este evaluador
    public function propuestas(): HasMany
    {
        return $this->hasMany(Propuesta::class, 'evaluador_id');
    }
}

