<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramaCongreso extends Model
{
    protected $table = 'congresos_programas';
    public $timestamps = false;

    protected $fillable = [
        'programa',
    ];

    public function congreso(): BelongsTo
    {
        return $this->belongsTo(Congreso::class, 'congreso_id');
    }
}
