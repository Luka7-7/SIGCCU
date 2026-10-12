<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionesCongresosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inscripciones_congresos')->insert([
            ['usuario_id' => 1, 'congreso_id' => 1, 'fecha_hora_inscripcion' => '2026-08-01 09:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-08-01 09:05:00', 'rol_congreso' => 'organizador'],
            ['usuario_id' => 5, 'congreso_id' => 1, 'fecha_hora_inscripcion' => '2026-08-02 10:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-08-03 12:00:00', 'rol_congreso' => 'comite'],
            ['usuario_id' => 2, 'congreso_id' => 1, 'fecha_hora_inscripcion' => '2026-09-10 11:30:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-09-11 08:00:00', 'rol_congreso' => 'asistente y/o ponente'],
            ['usuario_id' => 3, 'congreso_id' => 1, 'fecha_hora_inscripcion' => '2026-09-12 16:20:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-09-13 09:00:00', 'rol_congreso' => 'asistente y/o ponente'],
            ['usuario_id' => 8, 'congreso_id' => 1, 'fecha_hora_inscripcion' => '2026-10-01 14:00:00', 'aceptado' => 0, 'fecha_hora_respuesta' => null, 'rol_congreso' => null],
            ['usuario_id' => 6, 'congreso_id' => 2, 'fecha_hora_inscripcion' => '2025-07-02 09:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2025-07-02 10:00:00', 'rol_congreso' => 'organizador'],
            ['usuario_id' => 4, 'congreso_id' => 2, 'fecha_hora_inscripcion' => '2025-08-15 13:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2025-08-16 09:00:00', 'rol_congreso' => 'asistente y/o ponente'],
            ['usuario_id' => 7, 'congreso_id' => 2, 'fecha_hora_inscripcion' => '2025-08-20 10:30:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2025-08-21 09:00:00', 'rol_congreso' => 'comite'],
        ]);
    }
}
