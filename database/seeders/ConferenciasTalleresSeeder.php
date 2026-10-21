<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConferenciasTalleresSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('conferencias_talleres')->upsert([
            [
                'id' => 1, 'descripcion' => 'Conferencia magistral: Inteligencia artificial y sociedad.',
                'tipo' => 'conferencia', 'cupo' => 300, 'ubicacion' => 'Auditorio Central',
                'fecha' => '2026-11-18', 'hora_inicio' => '10:00:00', 'hora_fin' => '11:30:00',
                'congreso_id' => 1, 'qr_link' => 'qr/cicyt-conf-1',
            ],
            [
                'id' => 2, 'descripcion' => 'Conferencia: Energías renovables en la industria.',
                'tipo' => 'conferencia', 'cupo' => 200, 'ubicacion' => 'Sala A',
                'fecha' => '2026-11-19', 'hora_inicio' => '09:00:00', 'hora_fin' => '10:30:00',
                'congreso_id' => 1, 'qr_link' => 'qr/cicyt-conf-2',
            ],
            [
                'id' => 3, 'descripcion' => 'Taller: Introducción a Laravel y desarrollo web.',
                'tipo' => 'taller', 'cupo' => 30, 'ubicacion' => 'Laboratorio de Cómputo 2',
                'fecha' => '2026-11-19', 'hora_inicio' => '12:00:00', 'hora_fin' => '15:00:00',
                'congreso_id' => 1, 'qr_link' => 'qr/cicyt-taller-3',
            ],
            [
                'id' => 4, 'descripcion' => 'Taller: Redacción de artículos científicos.',
                'tipo' => 'taller', 'cupo' => 40, 'ubicacion' => 'Sala B',
                'fecha' => '2026-11-20', 'hora_inicio' => '09:00:00', 'hora_fin' => '12:00:00',
                'congreso_id' => 1, 'qr_link' => null,
            ],
            [
                'id' => 5, 'descripcion' => 'Conferencia: Identidad y memoria colectiva.',
                'tipo' => 'conferencia', 'cupo' => 150, 'ubicacion' => 'Auditorio de Humanidades',
                'fecha' => '2025-10-14', 'hora_inicio' => '11:00:00', 'hora_fin' => '12:30:00',
                'congreso_id' => 2, 'qr_link' => 'qr/chs-conf-5',
            ],
            [
                'id' => 6, 'descripcion' => 'Taller: Metodologías cualitativas de investigación.',
                'tipo' => 'taller', 'cupo' => 25, 'ubicacion' => 'Aula 5',
                'fecha' => '2025-10-15', 'hora_inicio' => '10:00:00', 'hora_fin' => '13:00:00',
                'congreso_id' => 2, 'qr_link' => 'qr/chs-taller-6',
            ],
        ], ['id'], ['descripcion', 'tipo', 'cupo', 'ubicacion', 'fecha', 'hora_inicio', 'hora_fin', 'congreso_id', 'qr_link']);
    }
}
