<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComentarioPropuesta extends Model
{
    protected $table = 'Comentarios_propuestas';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Comentario',
        'propuestas_id',
    ];

    // Propuesta a la que pertenece este comentario
    public function propuesta(): BelongsTo
    {
        return $this->belongsTo(Propuesta::class, 'propuestas_id');
    }
}
