<?php

namespace App\Models;

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
}