<?php

namespace Tests\Feature;

use App\Models\Usuario;
use App\Models\Carrera;
use App\Models\Propuesta;
use Carbon\Carbon;
use Database\Seeders\CarrerasSeeder;
use Database\Seeders\UsuariosSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocenteDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_carrera_and_user_seeders_create_valid_carrera_relationships(): void
    {
        $this->seed([CarrerasSeeder::class, UsuariosSeeder::class]);

        $usuario = Usuario::query()->findOrFail(2);

        $this->assertSame('Ingeniería en Computación', $usuario->carrera->nombre_carrera);
        $this->assertDatabaseHas('usuarios', [
            'id' => $usuario->id,
            'Carrera_id' => $usuario->carrera->id,
        ]);
    }

    public function test_user_can_update_academic_details_to_an_existing_carrera(): void
    {
        $this->seed(CarrerasSeeder::class);
        $usuario = $this->createUser('alumno');

        $this->actingAs($usuario)
            ->put(route('settings.update.academic'), [
                'matricula' => '87654321',
                'area' => 'ciencias basicas e ingenierias',
                'unidad_academica' => 'Unidad Académica de Ciencias Básicas e Ingenierías',
                'nombre_carrera' => 'Ingeniería en Electrónica',
            ])
            ->assertRedirect(route('settings.academic'));

        $usuario->refresh();

        $this->assertSame('Ingeniería en Electrónica', $usuario->carrera->nombre_carrera);
        $this->assertSame('87654321', $usuario->matricula_numtrabajador);
    }

    public function test_docente_can_submit_a_congress_request_programs_and_academic_proposal(): void
    {
        Storage::fake('local');
        $docente = $this->createUser('docente');
        Carrera::create([
            'nombre_carrera' => 'Licenciatura en Matemáticas',
            'unidad_academica' => 'Unidad Académica de Ciencias e Ingenierías',
            'area' => 'ciencias basicas e ingenierias',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);
        Carrera::create([
            'nombre_carrera' => 'Licenciatura en Enfermería',
            'unidad_academica' => 'Unidad Académica de Salud',
            'area' => 'ciencias de la salud',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);
        $fechaInicio = Carbon::today()->addMonths(5);
        $fechaRegistroAbre = Carbon::today()->addMonths(1);
        $fechaRegistroCierre = Carbon::today()->addMonths(3);

        $response = $this->actingAs($docente)->post(route('docente.congresos.store'), [
            'nombre' => 'Congreso de Innovación',
            'siglas' => 'CI',
            'edicion' => 1,
            'fecha_inicio' => $fechaInicio->toDateString(),
            'fecha_fin' => $fechaInicio->copy()->addDays(2)->toDateString(),
            'registro_abre' => $fechaRegistroAbre->toDateTimeString(),
            'registro_cierre' => $fechaRegistroCierre->toDateTimeString(),
            'descripcion' => 'Congreso académico de prueba.',
            'eje_tematico' => 'Innovación',
            'areas_participantes' => [
                'ciencias basicas e ingenierias',
                'ciencias de la salud',
            ],
            'unidades_participantes' => [
                'Unidad Académica de Ciencias e Ingenierías',
                'Unidad Académica de Salud',
            ],
            'programas' => [
                'Ingeniería en Computación',
                'Licenciatura en Matemáticas',
                'Licenciatura en Enfermería',
            ],
            'solicitud_pdf' => UploadedFile::fake()->create('solicitud.pdf', 10, 'application/pdf'),
            'propuesta_pdf' => UploadedFile::fake()->create('propuesta.pdf', 10, 'application/pdf'),
        ]);

        $response->assertRedirect(route('docente.dashboard'));
        $this->assertDatabaseHas('congresos', [
            'nombre' => 'Congreso de Innovación',
            'solicitante_id' => $docente->id,
            'dictamen' => null,
        ]);
        $this->assertDatabaseHas('congresos_programas', [
            'programa' => 'Ingeniería en Computación',
        ]);
        $this->assertDatabaseHas('congresos_programas', [
            'programa' => 'Licenciatura en Enfermería',
        ]);
        $this->assertDatabaseCount('congresos_programas', 3);
        $this->assertDatabaseHas('propuestas', [
            'usuario_id' => $docente->id,
            'resumen_estado' => 'Enviado',
        ]);

        $archivosGuardados = array_merge(
            Storage::disk('local')->allFiles('solicitudes-congresos'),
            Storage::disk('local')->allFiles('propuestas')
        );
        $this->assertCount(2, $archivosGuardados);
    }

    public function test_docente_sees_a_clear_message_when_registration_closes_before_it_opens(): void
    {
        $docente = $this->createUser('docente');
        $fechaInicio = Carbon::today()->addMonths(5);
        $fechaRegistroAbre = Carbon::today()->addMonths(1);

        $this->actingAs($docente)
            ->from(route('docente.dashboard'))
            ->post(route('docente.congresos.store'), [
                'nombre' => 'Congreso de Innovación',
                'siglas' => 'CI',
                'edicion' => 1,
                'fecha_inicio' => $fechaInicio->toDateString(),
                'fecha_fin' => $fechaInicio->copy()->addDays(2)->toDateString(),
                'registro_abre' => $fechaRegistroAbre->toDateTimeString(),
                'registro_cierre' => $fechaRegistroAbre->toDateTimeString(),
                'descripcion' => 'Congreso académico de prueba.',
                'eje_tematico' => 'Innovación',
                'areas_participantes' => ['ciencias basicas e ingenierias'],
                'unidades_participantes' => ['Unidad Académica de Ciencias e Ingenierías'],
                'programas' => ['Ingeniería en Computación'],
                'solicitud_pdf' => UploadedFile::fake()->create('solicitud.pdf', 10, 'application/pdf'),
                'propuesta_pdf' => UploadedFile::fake()->create('propuesta.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect(route('docente.dashboard'))
            ->assertSessionHasErrors([
                'registro_cierre' => 'El cierre del registro debe ser posterior a su apertura.',
            ]);
    }

    public function test_docente_cannot_select_a_unit_outside_selected_areas(): void
    {
        $docente = $this->createUser('docente');
        Carrera::create([
            'nombre_carrera' => 'Licenciatura en Enfermería',
            'unidad_academica' => 'Unidad Académica de Salud',
            'area' => 'ciencias de la salud',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);

        $this->actingAs($docente)
            ->from(route('docente.dashboard'))
            ->post(route('docente.congresos.store'), [
                'nombre' => 'Congreso de Innovación',
                'siglas' => 'CI',
                'edicion' => 1,
                'fecha_inicio' => Carbon::today()->addMonths(5)->toDateString(),
                'fecha_fin' => Carbon::today()->addMonths(5)->addDays(2)->toDateString(),
                'registro_abre' => Carbon::today()->addMonths(1)->toDateTimeString(),
                'registro_cierre' => Carbon::today()->addMonths(3)->toDateTimeString(),
                'descripcion' => 'Congreso académico de prueba.',
                'eje_tematico' => 'Innovación',
                'areas_participantes' => ['ciencias basicas e ingenierias'],
                'unidades_participantes' => ['Unidad Académica de Salud'],
                'programas' => ['Licenciatura en Enfermería'],
                'solicitud_pdf' => UploadedFile::fake()->create('solicitud.pdf', 10, 'application/pdf'),
                'propuesta_pdf' => UploadedFile::fake()->create('propuesta.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect(route('docente.dashboard'))
            ->assertSessionHasErrors('unidades_participantes.0');
    }

    public function test_docente_dashboard_lists_participating_programs_from_carreras_table(): void
    {
        $docente = $this->createUser('docente');
        Carrera::create([
            'nombre_carrera' => 'Programa de prueba',
            'unidad_academica' => 'Unidad de prueba',
            'area' => 'ciencias basicas e ingenierias',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);

        $this->actingAs($docente)
            ->get(route('docente.dashboard'))
            ->assertOk()
            ->assertSee('Programa de prueba')
            ->assertSee('Unidad de prueba')
            ->assertSee('name="areas_participantes[]"', false)
            ->assertSee('name="unidades_participantes[]"', false);
    }

    public function test_docente_profile_redirects_to_the_docente_dashboard(): void
    {
        $docente = $this->createUser('docente');

        $this->actingAs($docente)
            ->get(route('profile'))
            ->assertRedirect(route('docente.dashboard'));
    }

    public function test_docente_login_redirects_to_the_docente_dashboard(): void
    {
        $docente = $this->createUser('docente');

        $this->withSession(['url.intended' => route('dashboard')]);

        $this->post(route('login.store'), [
            'email' => $docente->correo_electronico,
            'password' => 'password',
        ])->assertRedirect(route('docente.dashboard'));
    }

    public function test_only_docentes_can_open_the_docente_dashboard(): void
    {
        $alumno = $this->createUser('alumno');

        $this->actingAs($alumno)
            ->get(route('docente.dashboard'))
            ->assertForbidden();
    }

    public function test_administrador_can_review_a_congress_request(): void
    {
        Storage::fake('local');
        $administrador = $this->createUser('administrador');
        $solicitante = $this->createUser('docente');
        $evaluadorDocente = $this->createUser('docente');
        Storage::disk('local')->put('solicitudes-congresos/solicitud.pdf', 'Solicitud PDF');
        Storage::disk('local')->put('propuestas/propuesta.pdf', 'Propuesta PDF');
        $congreso = \App\Models\Congreso::create([
            'nombre' => 'Congreso pendiente',
            'siglas' => 'CP',
            'edicion' => 1,
            'fecha_inicio' => '2027-03-10',
            'fecha_fin' => '2027-03-12',
            'registro_abre' => '2027-01-01 09:00',
            'registro_cierre' => '2027-02-01 18:00',
            'descripcion' => 'Solicitud para revisión.',
            'minimo_talleres' => 0,
            'minimo_conferencias' => 0,
            'eje_tematico' => 'Innovación',
            'solicitante_id' => $solicitante->id,
            'solicitud_pdf' => 'solicitudes-congresos/solicitud.pdf',
            'solicitada_en' => now(),
        ]);
        $propuesta = Propuesta::create([
            'resumen_url' => 'propuestas/propuesta.pdf',
            'resumen_estado' => 'Enviado',
            'usuario_id' => $solicitante->id,
            'congreso_id' => $congreso->id,
        ]);

        $this->actingAs($administrador)
            ->get(route('administracion.congresos.index'))
            ->assertOk()
            ->assertSee('Congreso pendiente')
            ->assertSee($solicitante->correo_electronico)
            ->assertSee($evaluadorDocente->nombre_completo)
            ->assertSee(route('administracion.congresos.solicitud', $congreso))
            ->assertSee(route('administracion.congresos.propuesta', $propuesta));

        $this->get(route('administracion.congresos.solicitud', $congreso))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=solicitud.pdf');
        $this->get(route('administracion.congresos.propuesta', $propuesta))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=propuesta.pdf');

        $this->actingAs($administrador)
            ->patch(route('administracion.propuestas.assign-evaluator', $propuesta), [
                'evaluador_id' => $evaluadorDocente->id,
            ])
            ->assertRedirect(route('administracion.congresos.index'))
            ->assertSessionHas('status', 'El docente evaluador fue asignado y debe aceptar la propuesta desde su portal.');

        $propuesta->refresh();
        $this->assertSame($evaluadorDocente->id, $propuesta->evaluador->usuario_id);
        $this->assertFalse($propuesta->evaluador_aceptado);

        $this->actingAs($solicitante)
            ->patch(route('docente.propuestas.accept', $propuesta))
            ->assertNotFound();

        $this->actingAs($evaluadorDocente)
            ->get(route('docente.dashboard'))
            ->assertOk()
            ->assertSee('Solicitudes de evaluación')
            ->assertSee('Aceptar evaluación');
        $this->patch(route('docente.propuestas.accept', $propuesta))
            ->assertRedirect(route('docente.dashboard'));

        $this->assertDatabaseHas('propuestas', [
            'id' => $propuesta->id,
            'evaluador_aceptado' => true,
            'resumen_estado' => 'En revision',
        ]);

        $this->actingAs($administrador)
            ->patch(route('administracion.congresos.update', $congreso), [
            'dictamen' => 'aceptada',
        ])->assertRedirect(route('administracion.congresos.index'));

        $this->assertDatabaseHas('congresos', [
            'id' => $congreso->id,
            'dictamen' => 'aceptada',
        ]);
    }

    public function test_administrador_login_redirects_to_congress_review(): void
    {
        $administrador = $this->createUser('administrador');

        $this->post(route('login.store'), [
            'email' => $administrador->correo_electronico,
            'password' => 'password',
        ])->assertRedirect(route('administracion.congresos.index'));
    }

    public function test_only_administradores_can_open_congress_review(): void
    {
        $docente = $this->createUser('docente');

        $this->actingAs($docente)
            ->get(route('administracion.congresos.index'))
            ->assertForbidden();
    }

    public function test_author_cannot_be_assigned_to_evaluate_their_own_proposal(): void
    {
        $administrador = $this->createUser('administrador');
        $autor = $this->createUser('docente');
        $congreso = \App\Models\Congreso::create([
            'nombre' => 'Congreso pendiente',
            'siglas' => 'CP',
            'edicion' => 1,
            'fecha_inicio' => '2027-03-10',
            'fecha_fin' => '2027-03-12',
            'registro_abre' => '2027-01-01 09:00',
            'registro_cierre' => '2027-02-01 18:00',
            'descripcion' => 'Solicitud para revisión.',
            'minimo_talleres' => 0,
            'minimo_conferencias' => 0,
            'eje_tematico' => 'Innovación',
            'solicitante_id' => $autor->id,
            'solicitud_pdf' => 'solicitudes-congresos/solicitud.pdf',
            'solicitada_en' => now(),
        ]);
        $propuesta = Propuesta::create([
            'resumen_url' => 'propuestas/propuesta.pdf',
            'resumen_estado' => 'Enviado',
            'usuario_id' => $autor->id,
            'congreso_id' => $congreso->id,
        ]);

        $this->actingAs($administrador)
            ->from(route('administracion.congresos.index'))
            ->patch(route('administracion.propuestas.assign-evaluator', $propuesta), [
                'evaluador_id' => $autor->id,
            ])
            ->assertRedirect(route('administracion.congresos.index'))
            ->assertSessionHasErrors([
                'evaluador_id' => 'El autor de la propuesta no puede evaluarla.',
            ]);
    }

    private function createUser(string $rol): Usuario
    {
        $carrera = Carrera::query()->firstOrCreate([
            'nombre_carrera' => 'Ingeniería en Computación',
            'unidad_academica' => 'Unidad Académica de Ciencias e Ingenierías',
            'area' => 'ciencias basicas e ingenierias',
            'institucion' => 'Universidad Autónoma de Nayarit',
        ]);

        return Usuario::create([
            'correo_electronico' => fake()->unique()->safeEmail(),
            'contrasena' => Hash::make('password'),
            'nombres' => 'Usuario',
            'apellidos' => 'Prueba',
            'rol' => $rol,
            'matricula_numtrabajador' => '12345678',
            'Carrera_id' => $carrera->id,
            'activo' => true,
        ]);
    }
}
