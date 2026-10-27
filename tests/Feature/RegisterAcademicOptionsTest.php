<?php

namespace Tests\Feature;

use App\Models\Carrera;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterAcademicOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_loads_academic_options_from_carreras(): void
    {
        $carrera = $this->createCarrera(
            'Ingenieria en Sistemas',
            'Unidad Academica de Tecnologia',
            'ciencias basicas e ingenierias'
        );

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('carreras:')
            ->assertSee($carrera->area)
            ->assertSee($carrera->unidad_academica)
            ->assertSee($carrera->nombre_carrera);
    }

    public function test_registration_stores_the_selected_carrera_relationship(): void
    {
        $carrera = $this->createCarrera(
            'Ingenieria en Sistemas',
            'Unidad Academica de Tecnologia',
            'ciencias basicas e ingenierias'
        );

        $this->post(route('register'), [
            'perfil_inicial' => 'estudiante',
            'nombres' => 'Ana',
            'apellidos' => 'Ejemplo',
            'email' => 'ana@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'matricula' => '12345678',
            'area_academica' => $carrera->area,
            'unidad_academica' => $carrera->unidad_academica,
            'programa_academico' => $carrera->nombre_carrera,
        ])->assertRedirect(route('profile'));

        $this->assertDatabaseHas('usuarios', [
            'correo_electronico' => 'ana@example.test',
            'rol' => 'alumno',
            'Carrera_id' => $carrera->id,
        ]);
    }

    public function test_registration_rejects_academic_values_that_do_not_match_one_carrera(): void
    {
        $firstCareer = $this->createCarrera(
            'Ingenieria en Sistemas',
            'Unidad Academica de Tecnologia',
            'ciencias basicas e ingenierias'
        );
        $secondCareer = $this->createCarrera(
            'Licenciatura en Contaduria',
            'Unidad Academica de Administracion',
            'ciencias economico y administrativas'
        );

        $this->from(route('register'))
            ->post(route('register'), [
                'perfil_inicial' => 'docente',
                'nombres' => 'Luis',
                'apellidos' => 'Ejemplo',
                'email' => 'luis@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'numero_empleado' => '87654321',
                'area_academica' => $firstCareer->area,
                'unidad_academica' => $firstCareer->unidad_academica,
                'programa_academico' => $secondCareer->nombre_carrera,
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('programa_academico');

        $this->assertDatabaseMissing('usuarios', [
            'correo_electronico' => 'luis@example.test',
        ]);
    }

    private function createCarrera(string $programa, string $unidad, string $area): Carrera
    {
        return Carrera::create([
            'nombre_carrera' => $programa,
            'unidad_academica' => $unidad,
            'area' => $area,
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);
    }
}
