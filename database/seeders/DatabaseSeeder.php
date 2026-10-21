<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsuariosSeeder::class,
            CongresosSeeder::class,
            SedesSeeder::class,
            CongresosSedesSeeder::class,
            ConferenciasTalleresSeeder::class,
            InscripcionesCongresosSeeder::class,
            InscripcionesConferenciasTalleresSeeder::class,
            AsistenciasSeeder::class,
            ConstanciasSeeder::class,
            EvaluadoresSeeder::class,
            PropuestasSeeder::class,
            ComentariosPropuestasSeeder::class,
        ]);
    }
}
