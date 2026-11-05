<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\ConferenciaTaller;
use App\Models\Congreso;
use App\Models\InscripcionConferenciaTaller;
use App\Models\InscripcionCongreso;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AsistenciaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::store('file')->forget('sigccu:attendance-windows');
    }

    public function test_staff_opens_a_five_minute_attendance_window_and_registered_student_marks_attendance(): void
    {
        $this->assertFalse(Schema::hasColumn('conferencias_talleres', 'asistencia_activa_hasta'));

        $inicioVentana = now()->startOfSecond();
        $this->travelTo($inicioVentana);

        $staff = $this->createUser('docente');
        $alumno = $this->createUser('alumno');
        $congreso = $this->createCongress($staff);
        $actividad = $this->createActivity($congreso);
        InscripcionConferenciaTaller::query()->create([
            'usuario_id' => $alumno->id,
            'conferencia_taller_id' => $actividad->id,
        ]);
        InscripcionCongreso::query()->create([
            'usuario_id' => $alumno->id,
            'congreso_id' => $congreso->id,
            'rol_congreso' => 'asistente y/o ponente',
        ]);

        $this->actingAs($alumno)
            ->get(route('congresos.activities', $congreso))
            ->assertOk()
            ->assertSee('El registro de asistencia aún no está abierto.')
            ->assertSee('Registrar mi asistencia');

        $this->actingAs($staff)
            ->post(route('asistencias.open', $actividad))
            ->assertRedirect(route('congresos.staff.activities', $congreso));

        $this->assertEquals(
            $inicioVentana->copy()->addMinutes(5)->timestamp,
            \App\Support\AttendanceWindow::expiresAt($actividad->id)?->timestamp
        );
        $this->assertDatabaseHas('asistencias', [
            'usuario_id' => $alumno->id,
            'conferencia_taller_id' => $actividad->id,
            'ip' => \App\Support\AttendanceWindow::pendingIp(),
            'Activo' => true,
        ]);

        $this->actingAs($alumno)
            ->getJson(route('asistencias.status', $actividad))
            ->assertOk()
            ->assertJson(['activa' => true, 'registrada' => false]);

        $this->actingAs($alumno)
            ->get(route('congresos.activities', $congreso))
            ->assertOk()
            ->assertSee('La ventana está abierta hasta')
            ->assertSee('Registrar mi asistencia');

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('asistencias.store', $actividad))
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('asistencias', [
            'usuario_id' => $alumno->id,
            'conferencia_taller_id' => $actividad->id,
            'Activo' => true,
        ]);
        $this->assertNotNull(
            DB::table('asistencias')
                ->where('usuario_id', $alumno->id)
                ->where('conferencia_taller_id', $actividad->id)
                ->value('fecha_hora')
        );

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('asistencias.store', $actividad))
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('asistencia');
    }

    public function test_attendance_window_expires_and_deactivates_attendance_rows(): void
    {
        $staff = $this->createUser('docente');
        $alumno = $this->createUser('alumno');
        $congreso = $this->createCongress($staff);
        $actividad = $this->createActivity($congreso);
        InscripcionConferenciaTaller::query()->create([
            'usuario_id' => $alumno->id,
            'conferencia_taller_id' => $actividad->id,
        ]);

        $this->actingAs($staff)->post(route('asistencias.open', $actividad));
        $this->travel(6)->minutes();

        \App\Support\AttendanceWindow::expireExpired();

        $this->assertDatabaseHas('asistencias', [
            'usuario_id' => $alumno->id,
            'conferencia_taller_id' => $actividad->id,
            'Activo' => false,
        ]);

        $this->actingAs($alumno)
            ->from(route('congresos.activities', $congreso))
            ->post(route('asistencias.store', $actividad))
            ->assertRedirect(route('congresos.activities', $congreso))
            ->assertSessionHasErrors('asistencia');
    }

    public function test_only_congress_staff_can_open_attendance_and_only_registered_users_can_mark_it(): void
    {
        $staff = $this->createUser('docente');
        $alumno = $this->createUser('alumno');
        $noInscrito = $this->createUser('alumno');
        $congreso = $this->createCongress($staff);
        $actividad = $this->createActivity($congreso);
        InscripcionConferenciaTaller::query()->create([
            'usuario_id' => $alumno->id,
            'conferencia_taller_id' => $actividad->id,
        ]);

        $this->actingAs($noInscrito)
            ->post(route('asistencias.open', $actividad))
            ->assertNotFound();

        InscripcionCongreso::query()->create([
            'usuario_id' => $alumno->id,
            'congreso_id' => $congreso->id,
            'rol_congreso' => 'organizador',
            'aceptado' => true,
        ]);
        $this->actingAs($alumno)
            ->get(route('congresos.staff.activities', $congreso))
            ->assertOk()
            ->assertSee('Abrir asistencia por 5 minutos')
            ->assertDontSee('Habilitar conferencia o taller');

        $this->actingAs($alumno)
            ->post(route('asistencias.open', $actividad))
            ->assertRedirect(route('congresos.staff.activities', $congreso));

        $this->actingAs($noInscrito)
            ->getJson(route('asistencias.status', $actividad))
            ->assertNotFound();

        $this->actingAs($noInscrito)
            ->post(route('asistencias.store', $actividad))
            ->assertNotFound();
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

    private function createCongress(Usuario $solicitante): Congreso
    {
        $congreso = Congreso::query()->create([
            'nombre' => 'Congreso de prueba',
            'siglas' => 'CP',
            'edicion' => 1,
            'fecha_inicio' => now()->addMonth()->toDateString(),
            'fecha_fin' => now()->addMonth()->addDays(2)->toDateString(),
            'registro_abre' => now()->subDay()->format('Y-m-d H:i:s'),
            'registro_cierre' => now()->addDay()->format('Y-m-d H:i:s'),
            'descripcion' => 'Congreso académico de prueba.',
            'minimo_talleres' => 0,
            'minimo_conferencias' => 0,
            'eje_tematico' => 'Pruebas',
            'dictamen' => 'aceptada',
            'solicitante_id' => $solicitante->id,
        ]);
        $congreso->programas()->create(['programa' => 'Ingeniería en Computación']);

        return $congreso;
    }

    private function createActivity(Congreso $congreso): ConferenciaTaller
    {
        return $congreso->conferenciasTalleres()->create([
            'descripcion' => 'Conferencia de prueba',
            'tipo' => 'conferencia',
            'cupo' => 25,
            'ubicacion' => 'Auditorio',
            'fecha' => now()->addMonth()->toDateString(),
            'hora_inicio' => '09:00:00',
            'hora_fin' => '10:00:00',
        ]);
    }
}
