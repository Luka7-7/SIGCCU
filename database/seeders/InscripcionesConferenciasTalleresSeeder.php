<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InscripcionesConferenciasTalleresSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inscripciones_conferencias_talleres')->insert([
            ['usuario_id' => 2, 'conferencia_taller_id' => 1, 'fecha_hora_inscripcion' => '2026-09-15 10:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-09-15 12:00:00', 'tipo_participacion' => 'asistente'],
            ['usuario_id' => 2, 'conferencia_taller_id' => 3, 'fecha_hora_inscripcion' => '2026-09-15 10:05:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-09-15 12:00:00', 'tipo_participacion' => 'asistente'],
            ['usuario_id' => 3, 'conferencia_taller_id' => 1, 'fecha_hora_inscripcion' => '2026-09-16 11:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-09-16 15:00:00', 'tipo_participacion' => 'asistente'],
            ['usuario_id' => 3, 'conferencia_taller_id' => 2, 'fecha_hora_inscripcion' => '2026-09-16 11:10:00', 'aceptado' => 0, 'fecha_hora_respuesta' => null, 'tipo_participacion' => null],
            ['usuario_id' => 5, 'conferencia_taller_id' => 3, 'fecha_hora_inscripcion' => '2026-08-20 09:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-08-20 10:00:00', 'tipo_participacion' => 'tallerista'],
            ['usuario_id' => 5, 'conferencia_taller_id' => 1, 'fecha_hora_inscripcion' => '2026-08-20 09:05:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2026-08-20 10:00:00', 'tipo_participacion' => 'ponente'],
            ['usuario_id' => 4, 'conferencia_taller_id' => 5, 'fecha_hora_inscripcion' => '2025-08-20 12:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2025-08-21 09:00:00', 'tipo_participacion' => 'asistente'],
            ['usuario_id' => 7, 'conferencia_taller_id' => 6, 'fecha_hora_inscripcion' => '2025-08-22 09:00:00', 'aceptado' => 1, 'fecha_hora_respuesta' => '2025-08-22 11:00:00', 'tipo_participacion' => 'tallerista'],
        ]);
    }
}
