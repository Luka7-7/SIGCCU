<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CongresosSedesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('congresos_sedes')->insert([
            ['congreso_id' => 1, 'sede_id' => 1],
            ['congreso_id' => 1, 'sede_id' => 2],
            ['congreso_id' => 2, 'sede_id' => 3],
            ['congreso_id' => 3, 'sede_id' => 1],
        ]);
    }
}
