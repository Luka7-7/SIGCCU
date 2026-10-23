<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SedesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sedes')->upsert([
            [
                'id' => 1,
                'nombre' => 'Auditorio Central',
                'direccion' => 'Av. Universidad #100, Col. Centro',
                'ciudad' => 'Guadalajara',
                'estado' => 'Jalisco',
                'url_ubicacion' => 'https://maps.google.com/?q=Auditorio+Central',
            ],
            [
                'id' => 2,
                'nombre' => 'Centro de Convenciones',
                'direccion' => 'Blvd. Principal #2500, Col. Moderna',
                'ciudad' => 'Guadalajara',
                'estado' => 'Jalisco',
                'url_ubicacion' => 'https://maps.google.com/?q=Centro+de+Convenciones',
            ],
            [
                'id' => 3,
                'nombre' => 'Unidad Académica de Humanidades',
                'direccion' => 'Calle Reforma #45, Col. Jardines',
                'ciudad' => 'Zapopan',
                'estado' => 'Jalisco',
                'url_ubicacion' => 'https://maps.google.com/?q=Unidad+Academica+Humanidades',
            ],
        ], ['id'], ['nombre', 'direccion', 'ciudad', 'estado', 'url_ubicacion']);
    }
}
