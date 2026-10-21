<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComentariosPropuestasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('Comentarios_propuestas')->upsert([
            [
                'Id' => 1,
                'Comentario' => 'El resumen cumple satisfactoriamente con la metodología y los objetivos planteados. Se recomienda aceptar.',
                'propuestas_id' => 1,
            ],
            [
                'Id' => 2,
                'Comentario' => 'Se está evaluando la congruencia entre el marco teórico y los resultados preliminares.',
                'propuestas_id' => 2,
            ],
            [
                'Id' => 3,
                'Comentario' => 'Favor de corregir el formato de citas bibliográficas según el estándar APA 7ma edición y ampliar la justificación.',
                'propuestas_id' => 3,
            ],
            [
                'Id' => 4,
                'Comentario' => 'El documento ha sido recibido y se encuentra en espera de asignación de evaluadores pares.',
                'propuestas_id' => 4,
            ],
            [
                'Id' => 5,
                'Comentario' => 'El trabajo no se apega a las líneas temáticas convocadas en este congreso y excede el límite de páginas permitido.',
                'propuestas_id' => 5,
            ],
        ], ['Id'], ['Comentario', 'propuestas_id']);
    }
}
