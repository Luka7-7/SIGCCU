<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        DB::table('usuarios')->upsert([
            [
                'id' => 1, 'correo_electronico' => 'admin@congresos.test', 'contrasena' => $password,
                'nombres' => 'Administrador', 'apellidos' => 'General', 'rol' => 'administrador',
                'matricula_numtrabajador' => null, 'Carrera_id' => $this->carreraId(
                    'No especificada',
                    'No especificada',
                    'Universidad Autónoma de Nayarit',
                ), 'activo' => 1,
            ],
            [
                'id' => 2, 'correo_electronico' => 'maria.lopez@alumnos.test', 'contrasena' => $password,
                'nombres' => 'María Fernanda', 'apellidos' => 'López Hernández', 'rol' => 'alumno',
                'matricula_numtrabajador' => '20210001', 'Carrera_id' => $this->carreraId(
                    'Ingeniería en Computación',
                    'Unidad Académica de Ciencias e Ingenierías',
                ), 'activo' => 1,
            ],
            [
                'id' => 3, 'correo_electronico' => 'juan.perez@alumnos.test', 'contrasena' => $password,
                'nombres' => 'Juan Carlos', 'apellidos' => 'Pérez Ramírez', 'rol' => 'alumno',
                'matricula_numtrabajador' => '20210002', 'Carrera_id' => $this->carreraId(
                    'Licenciatura en Economía',
                    'Unidad Académica de Economía',
                ), 'activo' => 1,
            ],
            [
                'id' => 4, 'correo_electronico' => 'ana.garcia@alumnos.test', 'contrasena' => $password,
                'nombres' => 'Ana Sofía', 'apellidos' => 'García Torres', 'rol' => 'alumno',
                'matricula_numtrabajador' => '20220015', 'Carrera_id' => $this->carreraId(
                    'Licenciatura en Médico Cirujano',
                    'Unidad Académica de Medicina',
                ), 'activo' => 1,
            ],
            [
                'id' => 5, 'correo_electronico' => 'roberto.sanchez@docentes.test', 'contrasena' => $password,
                'nombres' => 'Roberto', 'apellidos' => 'Sánchez Mora', 'rol' => 'docente',
                'matricula_numtrabajador' => 'T0010234', 'Carrera_id' => $this->carreraId(
                    'Ingeniería en Computación',
                    'Unidad Académica de Ciencias e Ingenierías',
                ), 'activo' => 1,
            ],
            [
                'id' => 6, 'correo_electronico' => 'laura.martinez@docentes.test', 'contrasena' => $password,
                'nombres' => 'Laura', 'apellidos' => 'Martínez Cruz', 'rol' => 'docente',
                'matricula_numtrabajador' => 'T0010567', 'Carrera_id' => $this->carreraId(
                    'Licenciatura en Filosofía',
                    'Unidad Académica de Educación y Humanidades',
                ), 'activo' => 1,
            ],
            [
                'id' => 7, 'correo_electronico' => 'pedro.ruiz@docentes.test', 'contrasena' => $password,
                'nombres' => 'Pedro', 'apellidos' => 'Ruiz Domínguez', 'rol' => 'docente',
                'matricula_numtrabajador' => 'T0011890', 'Carrera_id' => $this->carreraId(
                    'Licenciatura en Agricultura',
                    'Unidad Académica de Agricultura',
                ), 'activo' => 1,
            ],
            [
                'id' => 8, 'correo_electronico' => 'carmen.vega@externo.test', 'contrasena' => $password,
                'nombres' => 'Carmen', 'apellidos' => 'Vega Solís', 'rol' => 'externo',
                'matricula_numtrabajador' => null, 'Carrera_id' => $this->carreraId(
                    'No especificada',
                    'No especificada',
                    'Instituto Tecnológico Nacional',
                ), 'activo' => 1,
            ],
            [
                'id' => 9, 'correo_electronico' => 'diego.flores@alumnos.test', 'contrasena' => $password,
                'nombres' => 'Diego', 'apellidos' => 'Flores Navarro', 'rol' => 'alumno',
                'matricula_numtrabajador' => '20230044', 'Carrera_id' => $this->carreraId(
                    'Licenciatura en Artes Visuales',
                    'Unidad Académica de Artes',
                ), 'activo' => 0,
            ],
        ], ['id'], [
            'correo_electronico', 'contrasena', 'nombres', 'apellidos', 'rol',
            'matricula_numtrabajador', 'Carrera_id', 'activo',
        ]);
    }

    private function carreraId(string $programa, string $unidad, string $institucion = 'Universidad Autónoma de Nayarit'): int
    {
        $id = DB::table('Carrera')
            ->where('nombre_carrera', $programa)
            ->where('unidad_academica', $unidad)
            ->where('institucion', $institucion)
            ->value('id');

        if ($id === null) {
            throw new RuntimeException("No existe la carrera de semilla: {$programa} ({$unidad}).");
        }

        return (int) $id;
    }
}
