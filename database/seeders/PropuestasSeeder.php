<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropuestasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('propuestas')->insert([
            ['id' => 1, 'resumen_url' => 'propuestas/resumen-001.pdf', 'extenso_url' => 'propuestas/extenso-001.pdf', 'resumen_estado' => 'Aceptado', 'usuario_id' => 2, 'congreso_id' => 1, 'evaluador_id' => 1],
            ['id' => 2, 'resumen_url' => 'propuestas/resumen-002.pdf', 'extenso_url' => null, 'resumen_estado' => 'En revision', 'usuario_id' => 3, 'congreso_id' => 1, 'evaluador_id' => 1],
            ['id' => 3, 'resumen_url' => 'propuestas/resumen-003.pdf', 'extenso_url' => null, 'resumen_estado' => 'A corregir', 'usuario_id' => 2, 'congreso_id' => 1, 'evaluador_id' => 3],
            ['id' => 4, 'resumen_url' => 'propuestas/resumen-004.pdf', 'extenso_url' => null, 'resumen_estado' => 'Enviado', 'usuario_id' => 8, 'congreso_id' => 1, 'evaluador_id' => null],
            ['id' => 5, 'resumen_url' => 'propuestas/resumen-005.pdf', 'extenso_url' => 'propuestas/extenso-005.pdf', 'resumen_estado' => 'Rechazado', 'usuario_id' => 4, 'congreso_id' => 2, 'evaluador_id' => 2],
        ]);
    }
}
