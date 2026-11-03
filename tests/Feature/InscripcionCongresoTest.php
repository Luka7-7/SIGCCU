<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\ConferenciaTaller;
use App\Models\Congreso;
use App\Models\InscripcionConferenciaTaller;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InscripcionCongresoTest extends TestCase
{
    use RefreshDatabase;

    public function test_alumno_can_view_and_register_for_an_approved_congress_for_their_career(): void
    {
        $alumno = $this->createUser('alumno');
        $congreso = $this->createCongress($alumno->carrera->nombre_carrera);

        $this->actingAs($alumno)
            ->get(route('congresos.index'))
            ->assertOk()
            ->assertSee($congreso->nombre)
            ->assertSee('Seleccionar actividades');

        $this->actingAs($alumno)
            ->get(route('congresos.activities', $congreso))
            ->assertOk()
            ->assertSee('Confirmar inscripción al congreso');

        $this->actingAs($alumno)
            ->post(route('congresos.inscripcion.store', $congreso), ['actividades' => []])
            ->assertRedirect(route('congresos.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('inscripciones_congresos', [
            'usuario_id' => $alumno->id,
            'congreso_id' => $congreso->id,
            'aceptado' => false,
        ]);

        $this->from(route('congresos.index'))
            ->post(route('congresos.inscripcion.store', $congreso), ['actividades' => []])
            ->assertRedirect(route('congresos.index'))
            ->assertSessionHasErrors('congreso');

        $this->assertDatabaseCount('inscripciones_congresos', 1);
    }

    public function test_docente_can_register_for_an_approved_congress_for_their_career(): void
    {
        $docente = $this->createUser('docente');
        $congreso = $this->createCongress($docente->carrera->nombre_carrera);

        $this->actingAs($docente)
            ->post(route('congresos.inscripcion.store', $congreso), ['actividades' => []])
            ->assertRedirect(route('congresos.index'));

        $this->assertDatabaseHas('inscripciones_congresos', [
            'usuario_id' => $docente->id,
            'congreso_id' => $congreso->id,
        ]);
    }

    public function test_user_cannot_register_for_a_congress_outside_their_career_or_registration_period(): void
    {
        $alumno = $this->createUser('alumno');
        $congresoNoApto = $this->createCongress('Otra carrera');
        $congresoCerrado = $this->createCongress($alumno->carrera->nombre_carrera, [
            'registro_abre' => now()->subDays(3)->format('Y-m-d H:i:s'),
            'registro_cierre' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $this->actingAs($alumno)
            ->post(route('congresos.inscripcion.store', $congresoNoApto))
            ->assertNotFound();

        $this->actingAs($alumno)
            ->from(route('congresos.index'))
            ->post(route('congresos.inscripcion.store', $congresoCerrado), ['actividades' => []])
            ->assertRedirect(route('congresos.index'))
            ->assertSessionHasErrors('congreso');

        $this->assertDatabaseCount('inscripciones_congresos', 0);
    }

    public function test_listing_only_shows_approved_congresses_that_match_the_users_career(): void
    {
        $alumno = $this->createUser('alumno');
        $apto = $this->createCongress($alumno->carrera->nombre_carrera);
        $this->createCongress('Otra carrera');
        $noAprobado = $this->createCongress($alumno->carrera->nombre_carrera, [
            'dictamen' => 'rechazada',
            'nombre' => 'Congreso rechazado',
        ]);

        $this->actingAs($alumno)
            ->get(route('congresos.index'))
            ->assertOk()
            ->assertSee($apto->nombre)
            ->assertDontSee($noAprobado->nombre)
            ->assertDontSee('Congreso para Otra carrera');
    }

    public function test_other_roles_cannot_access_congress_registration(): void
    {
        $externo = $this->createUser('externo');

        $this->actingAs($externo)
            ->get(route('congresos.index'))
            ->assertForbidden();
    }

    public function test_registration_requires_the_minimum_number_of_conferences_and_workshops(): void
    {
        $alumno = $this->createUser('alumno');
        $congreso = $this->createCongress($alumno->carrera->nombre_carrera, [
            'minimo_conferencias' => 2,
            'minimo_talleres' => 1,
        ]);
        $conferenciaUno = $this->createActivity($congreso, 'conferencia');
        $conferenciaDos = $this->createActivity($congreso, 'conferencia');
        $taller = $this->createActivity($congreso, 'taller');
        $noSeleccionada = $this->createActivity($congreso, 'conferencia', [
            'descripcion' => 'Conferencia no seleccionada',
        ]);

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('congresos.inscripcion.store', $congreso), [
                'actividades' => [$conferenciaUno->id, $conferenciaDos->id],
            ])
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('actividades');

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('congresos.inscripcion.store', $congreso), [
                'actividades' => [$conferenciaUno->id, $taller->id],
            ])
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('actividades');

        $this->assertDatabaseCount('inscripciones_congresos', 0);
        $this->assertDatabaseCount('inscripciones_conferencias_talleres', 0);

        $this->actingAs($alumno)
            ->post(route('congresos.inscripcion.store', $congreso), [
                'actividades' => [$conferenciaUno->id, $conferenciaDos->id, $taller->id],
            ])
            ->assertRedirect(route('congresos.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('inscripciones_congresos', [
            'usuario_id' => $alumno->id,
            'congreso_id' => $congreso->id,
        ]);
        $this->assertDatabaseCount('inscripciones_conferencias_talleres', 3);

        $this->actingAs($alumno)
            ->get(route('congresos.activities', $congreso))
            ->assertOk()
            ->assertSee($conferenciaUno->descripcion)
            ->assertDontSee($noSeleccionada->descripcion)
            ->assertSee('no se puede modificar')
            ->assertDontSee('Confirmar inscripción al congreso');

        $this->actingAs($alumno)
            ->get(route('congresos.index'))
            ->assertOk()
            ->assertSee('Actividades seleccionadas')
            ->assertSee($conferenciaUno->descripcion)
            ->assertDontSee($noSeleccionada->descripcion)
            ->assertDontSee('Gestionar actividades');

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('congresos.inscripcion.store', $congreso), [
                'actividades' => [$noSeleccionada->id],
            ])
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('congreso');

        $this->assertDatabaseCount('inscripciones_conferencias_talleres', 3);
    }

    public function test_registration_is_rejected_if_a_selected_activity_is_full(): void
    {
        $alumno = $this->createUser('alumno');
        $otroAlumno = $this->createUser('alumno');
        $congreso = $this->createCongress($alumno->carrera->nombre_carrera);
        $actividad = $this->createActivity($congreso, 'conferencia', ['cupo' => 1]);
        InscripcionConferenciaTaller::query()->create([
            'usuario_id' => $otroAlumno->id,
            'conferencia_taller_id' => $actividad->id,
        ]);

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('congresos.inscripcion.store', $congreso), [
                'actividades' => [$actividad->id],
            ])
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('actividades');

        $this->assertDatabaseCount('inscripciones_congresos', 0);
    }

    public function test_user_cannot_select_an_activity_from_another_congress(): void
    {
        $alumno = $this->createUser('alumno');
        $congreso = $this->createCongress($alumno->carrera->nombre_carrera);
        $otroCongreso = $this->createCongress($alumno->carrera->nombre_carrera);
        $actividadAjena = $this->createActivity($otroCongreso, 'conferencia');

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('congresos.inscripcion.store', $congreso), [
                'actividades' => [$actividadAjena->id],
            ])
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('actividades.0');

        $this->assertDatabaseCount('inscripciones_congresos', 0);
    }

    private function createUser(string $rol): Usuario
    {
        $carrera = Carrera::query()->create([
            'nombre_carrera' => 'Ingeniería en Computación',
            'unidad_academica' => 'Unidad Académica de Ciencias Básicas e Ingenierías',
            'area' => 'ciencias basicas e ingenierias',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);

        return Usuario::query()->create([
            'correo_electronico' => fake()->unique()->safeEmail(),
            'contrasena' => Hash::make('password'),
            'nombres' => 'Usuario',
            'apellidos' => 'Prueba',
            'rol' => $rol,
            'Carrera_id' => $carrera->id,
            'activo' => true,
        ]);
    }

    private function createCongress(string $programa, array $overrides = []): Congreso
    {
        $congreso = Congreso::query()->create(array_merge([
            'nombre' => $programa === 'Otra carrera' ? 'Congreso para Otra carrera' : 'Congreso compatible',
            'siglas' => 'CC',
            'edicion' => 1,
            'fecha_inicio' => now()->addMonth()->toDateString(),
            'fecha_fin' => now()->addMonth()->addDays(2)->toDateString(),
            'registro_abre' => now()->subDay()->format('Y-m-d H:i:s'),
            'registro_cierre' => now()->addDay()->format('Y-m-d H:i:s'),
            'descripcion' => 'Congreso de prueba.',
            'minimo_talleres' => 0,
            'minimo_conferencias' => 0,
            'eje_tematico' => 'Pruebas',
            'dictamen' => 'aceptada',
        ], $overrides));

        $congreso->programas()->create(['programa' => $programa]);

        return $congreso;
    }

    private function createActivity(
        Congreso $congreso,
        string $tipo,
        array $overrides = []
    ): ConferenciaTaller {
        return $congreso->conferenciasTalleres()->create(array_merge([
            'descripcion' => 'Actividad de prueba',
            'tipo' => $tipo,
            'cupo' => 25,
            'ubicacion' => 'Auditorio',
            'fecha' => now()->addMonth()->toDateString(),
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:00:00',
        ], $overrides));
    }
}
