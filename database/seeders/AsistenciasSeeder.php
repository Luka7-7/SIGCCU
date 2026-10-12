<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsistenciasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('asistencias')->insert([
            ['usuario_id' => 2, 'conferencia_taller_id' => 1, 'fecha_hora' => '2026-11-18 10:05:00', 'ip' => '192.168.1.10'],
            ['usuario_id' => 3, 'conferencia_taller_id' => 1, 'fecha_hora' => '2026-11-18 10:12:00', 'ip' => '192.168.1.11'],
            ['usuario_id' => 2, 'conferencia_taller_id' => 3, 'fecha_hora' => '2026-11-19 12:03:00', 'ip' => '192.168.1.10'],
            ['usuario_id' => 4, 'conferencia_taller_id' => 5, 'fecha_hora' => '2025-10-14 11:02:00', 'ip' => '10.0.0.25'],
            ['usuario_id' => 7, 'conferencia_taller_id' => 6, 'fecha_hora' => '2025-10-15 10:01:00', 'ip' => '2001:db8::1'],
        ]);
    }
}
