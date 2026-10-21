<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluadoresSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('evaluadores')->upsert([
            ['id' => 1, 'usuario_id' => 5, 'licenciatura' => 'Ingeniería en Computación', 'maestria' => 'Maestría en Ciencias de la Computación', 'doctorado' => 'Doctorado en Ciencias de la Ingeniería'],
            ['id' => 2, 'usuario_id' => 6, 'licenciatura' => 'Licenciatura en Sociología', 'maestria' => 'Maestría en Estudios Sociales', 'doctorado' => null],
            ['id' => 3, 'usuario_id' => 7, 'licenciatura' => 'Ingeniero Agrónomo', 'maestria' => null, 'doctorado' => null],
        ], ['id'], ['usuario_id', 'licenciatura', 'maestria', 'doctorado']);
    }
}
