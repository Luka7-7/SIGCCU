<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioPropuesta extends Model
{
    protected $table = 'Comentarios_propuestas';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Comentario',
        'propuestas_id',
    ];
}

