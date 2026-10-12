<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CongresosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('congresos')->insert([
            [
                'id' => 1, 'nombre' => 'Congreso Internacional de Ciencia y Tecnología', 'siglas' => 'CICYT',
                'edicion' => 3, 'fecha_inicio' => '2026-11-18', 'fecha_fin' => '2026-11-20',
                'registro_abre' => '2026-08-01 08:00:00', 'registro_cierre' => '2026-11-10 23:59:59',
                'descripcion' => 'Congreso multidisciplinario para presentar avances de investigación científica y tecnológica.',
                'logo_url' => 'logos/cicyt.png', 'minimo_talleres' => 1, 'minimo_conferencias' => 2,
                'eje_tematico' => 'Innovación y sustentabilidad', 'dictamen' => 'aceptada',
            ],
            [
                'id' => 2, 'nombre' => 'Congreso de Humanidades y Sociedad', 'siglas' => 'CHS',
                'edicion' => 2, 'fecha_inicio' => '2025-10-14', 'fecha_fin' => '2025-10-16',
                'registro_abre' => '2025-07-01 08:00:00', 'registro_cierre' => '2025-10-05 23:59:59',
                'descripcion' => 'Espacio de diálogo sobre problemáticas sociales contemporáneas.',
                'logo_url' => null, 'minimo_talleres' => 0, 'minimo_conferencias' => 1,
                'eje_tematico' => 'Cultura y sociedad', 'dictamen' => 'con observaciones',
            ],
            [
                'id' => 3, 'nombre' => 'Congreso de Salud y Bienestar', 'siglas' => 'CSB',
                'edicion' => 1, 'fecha_inicio' => '2027-03-09', 'fecha_fin' => '2027-03-11',
                'registro_abre' => '2027-01-15 08:00:00', 'registro_cierre' => '2027-03-01 23:59:59',
                'descripcion' => 'Primera edición del congreso enfocado en salud pública y bienestar comunitario.',
                'logo_url' => null, 'minimo_talleres' => 2, 'minimo_conferencias' => 3,
                'eje_tematico' => 'Salud comunitaria', 'dictamen' => null,
            ],
        ]);
    }
}
