<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocenteDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_docente_can_submit_a_congress_request_programs_and_academic_proposal(): void
    {
        Storage::fake('local');
        $docente = $this->createUser('docente');
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
            'programas' => [
                'Ingeniería en Computación',
                'Licenciatura en Matemáticas',
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
        $administrador = $this->createUser('administrador');
        $solicitante = $this->createUser('docente');
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

        $this->actingAs($administrador)
            ->get(route('administracion.congresos.index'))
            ->assertOk()
            ->assertSee('Congreso pendiente');

        $this->patch(route('administracion.congresos.update', $congreso), [
            'dictamen' => 'aceptada',
        ])->assertRedirect(route('administracion.congresos.index'));

        $this->assertDatabaseHas('congresos', [
            'id' => $congreso->id,
            'dictamen' => 'aceptada',
        ]);
    }

    private function createUser(string $rol): Usuario
    {
        return Usuario::create([
            'correo_electronico' => fake()->unique()->safeEmail(),
            'contrasena' => Hash::make('password'),
            'nombres' => 'Usuario',
            'apellidos' => 'Prueba',
            'rol' => $rol,
            'institucion' => 'Universidad Autónoma de Nayarit',
            'matricula_numtrabajador' => '12345678',
            'unidad_academica' => 'Unidad Académica de Ciencias e Ingenierías',
            'nombre_carrera' => 'Ingeniería en Computación',
            'activo' => true,
            'area' => 'ciencias basicas e ingenierias',
        ]);
    }
}
