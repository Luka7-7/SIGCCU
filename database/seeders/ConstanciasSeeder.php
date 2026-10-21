<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstanciasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('constancias')->upsert([
            ['id' => 1, 'usuario_id' => 4, 'congreso_id' => 2, 'conferencia_taller_id' => null, 'url_constancia' => 'constancias/chs-2025-usuario4.pdf'],
            ['id' => 2, 'usuario_id' => 4, 'congreso_id' => null, 'conferencia_taller_id' => 5, 'url_constancia' => 'constancias/chs-conf5-usuario4.pdf'],
            ['id' => 3, 'usuario_id' => 7, 'congreso_id' => null, 'conferencia_taller_id' => 6, 'url_constancia' => 'constancias/chs-taller6-usuario7.pdf'],
            ['id' => 4, 'usuario_id' => 7, 'congreso_id' => 2, 'conferencia_taller_id' => null, 'url_constancia' => 'constancias/chs-2025-usuario7.pdf'],
        ], ['id'], ['usuario_id', 'congreso_id', 'conferencia_taller_id', 'url_constancia']);
    }
}
