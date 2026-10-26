<?php

namespace Database\Seeders;

use App\Http\Controllers\PerfilController;
use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CarrerasSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo = [];

        foreach (PerfilController::UNIDADES_PROGRAMAS as $unidad => $programas) {
            foreach ($programas as $programa) {
                $catalogo[] = [
                    'nombre_carrera' => $programa,
                    'unidad_academica' => $unidad,
                    'area' => $this->areaDe($programa),
                    'institucion' => 'Universidad Autónoma de Nayarit',
                ];
            }
        }

        $catalogo[] = [
            'nombre_carrera' => 'Licenciatura en Artes Visuales',
            'unidad_academica' => 'Unidad Académica de Artes',
            'area' => 'artes',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ];

        foreach ([
            'Universidad Autónoma de Nayarit',
            'Instituto Tecnológico Nacional',
        ] as $institucion) {
            $catalogo[] = [
                'nombre_carrera' => 'No especificada',
                'unidad_academica' => 'No especificada',
                'area' => 'ciencias sociales y humanidades',
                'institucion' => $institucion,
            ];
        }

        foreach ($catalogo as $carrera) {
            Carrera::query()->firstOrCreate($carrera);
        }
    }

    private function areaDe(string $programa): string
    {
        $programa = mb_strtolower($programa);

        return match (true) {
            str_contains($programa, 'música'),
            str_contains($programa, 'diseño'),
            str_contains($programa, 'artes visuales') => 'artes',

            str_contains($programa, 'ingeniería'),
            str_contains($programa, 'matemáticas'),
            str_contains($programa, 'seguridad informática'),
            str_contains($programa, 'sistemas computacionales'),
            str_contains($programa, 'tecnologías de información') => 'ciencias basicas e ingenierias',

            str_contains($programa, 'administración'),
            str_contains($programa, 'contaduría'),
            str_contains($programa, 'mercadotecnia'),
            str_contains($programa, 'negocios'),
            str_contains($programa, 'economía'),
            str_contains($programa, 'gestión empresarial') => 'ciencias economico y administrativas',

            str_contains($programa, 'agricultura'),
            str_contains($programa, 'biología'),
            str_contains($programa, 'veterinaria'),
            str_contains($programa, 'pesquera'),
            str_contains($programa, 'acuicultura') => 'ciencias biologicas y agropecuarias',

            str_contains($programa, 'enfermería'),
            str_contains($programa, 'médico'),
            str_contains($programa, 'cirujano dentista'),
            str_contains($programa, 'nutrición'),
            str_contains($programa, 'terapia física'),
            str_contains($programa, 'químico farmacobiólogo'),
            str_contains($programa, 'biomedicina'),
            str_contains($programa, 'cultura física') => 'ciencias de la salud',

            default => 'ciencias sociales y humanidades',
        };
    }
}
