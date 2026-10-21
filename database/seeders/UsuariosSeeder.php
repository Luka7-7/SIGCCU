<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        DB::table('usuarios')->upsert([
            [
                'id' => 1, 'correo_electronico' => 'admin@congresos.test', 'contrasena' => $password,
                'nombres' => 'Administrador', 'apellidos' => 'General', 'rol' => 'administrador',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => null,
                'unidad_academica' => null, 'nombre_carrera' => null, 'activo' => 1,
                'area' => 'ciencias basicas e ingenierias',
            ],
            [
                'id' => 2, 'correo_electronico' => 'maria.lopez@alumnos.test', 'contrasena' => $password,
                'nombres' => 'María Fernanda', 'apellidos' => 'López Hernández', 'rol' => 'alumno',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => '20210001',
                'unidad_academica' => 'Unidad Académica de Ingeniería', 'nombre_carrera' => 'Ingeniería en Sistemas',
                'activo' => 1, 'area' => 'ciencias basicas e ingenierias',
            ],
            [
                'id' => 3, 'correo_electronico' => 'juan.perez@alumnos.test', 'contrasena' => $password,
                'nombres' => 'Juan Carlos', 'apellidos' => 'Pérez Ramírez', 'rol' => 'alumno',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => '20210002',
                'unidad_academica' => 'Unidad Académica de Economía', 'nombre_carrera' => 'Licenciatura en Administración',
                'activo' => 1, 'area' => 'ciencias economico y administrativas',
            ],
            [
                'id' => 4, 'correo_electronico' => 'ana.garcia@alumnos.test', 'contrasena' => $password,
                'nombres' => 'Ana Sofía', 'apellidos' => 'García Torres', 'rol' => 'alumno',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => '20220015',
                'unidad_academica' => 'Unidad Académica de Medicina', 'nombre_carrera' => 'Médico Cirujano',
                'activo' => 1, 'area' => 'ciencias de la salud',
            ],
            [
                'id' => 5, 'correo_electronico' => 'roberto.sanchez@docentes.test', 'contrasena' => $password,
                'nombres' => 'Roberto', 'apellidos' => 'Sánchez Mora', 'rol' => 'docente',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => 'T0010234',
                'unidad_academica' => 'Unidad Académica de Ingeniería', 'nombre_carrera' => null,
                'activo' => 1, 'area' => 'ciencias basicas e ingenierias',
            ],
            [
                'id' => 6, 'correo_electronico' => 'laura.martinez@docentes.test', 'contrasena' => $password,
                'nombres' => 'Laura', 'apellidos' => 'Martínez Cruz', 'rol' => 'docente',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => 'T0010567',
                'unidad_academica' => 'Unidad Académica de Humanidades', 'nombre_carrera' => null,
                'activo' => 1, 'area' => 'ciencias sociales y humanidades',
            ],
            [
                'id' => 7, 'correo_electronico' => 'pedro.ruiz@docentes.test', 'contrasena' => $password,
                'nombres' => 'Pedro', 'apellidos' => 'Ruiz Domínguez', 'rol' => 'docente',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => 'T0011890',
                'unidad_academica' => 'Unidad Académica de Agricultura', 'nombre_carrera' => null,
                'activo' => 1, 'area' => 'ciencias biologicas y agropecuarias',
            ],
            [
                'id' => 8, 'correo_electronico' => 'carmen.vega@externo.test', 'contrasena' => $password,
                'nombres' => 'Carmen', 'apellidos' => 'Vega Solís', 'rol' => 'externo',
                'institucion' => 'Instituto Tecnológico Nacional', 'matricula_numtrabajador' => null,
                'unidad_academica' => null, 'nombre_carrera' => null,
                'activo' => 1, 'area' => 'artes',
            ],
            [
                'id' => 9, 'correo_electronico' => 'diego.flores@alumnos.test', 'contrasena' => $password,
                'nombres' => 'Diego', 'apellidos' => 'Flores Navarro', 'rol' => 'alumno',
                'institucion' => 'Universidad Autónoma', 'matricula_numtrabajador' => '20230044',
                'unidad_academica' => 'Unidad Académica de Artes', 'nombre_carrera' => 'Licenciatura en Artes Visuales',
                'activo' => 0, 'area' => 'artes',
            ],
        ], ['id'], ['correo_electronico', 'contrasena', 'nombres', 'apellidos', 'rol', 'institucion', 'matricula_numtrabajador', 'unidad_academica', 'nombre_carrera', 'activo', 'area']);
    }
}
