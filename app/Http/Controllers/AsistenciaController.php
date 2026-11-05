<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\ConferenciaTaller;
use App\Models\InscripcionConferenciaTaller;
use App\Models\InscripcionCongreso;
use App\Support\AttendanceWindow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AsistenciaController extends Controller
{
    public function open(Request $request, ConferenciaTaller $actividad): RedirectResponse
    {
        $this->authorizeStaff($request, $actividad);

        DB::transaction(function () use ($actividad): void {
            $actividad = ConferenciaTaller::query()
                ->whereKey($actividad->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($actividad->asistencia_activa_hasta?->isFuture()) {
                throw ValidationException::withMessages([
                    'asistencia' => 'El registro de asistencia ya está abierto para esta actividad.',
                ]);
            }

            AttendanceWindow::expireActivity($actividad->id);

            $asistencias = InscripcionConferenciaTaller::query()
                ->where('conferencia_taller_id', $actividad->id)
                ->pluck('usuario_id')
                ->map(fn (int $usuarioId): array => [
                    'usuario_id' => $usuarioId,
                    'conferencia_taller_id' => $actividad->id,
                    'fecha_hora' => now(),
                    'ip' => AttendanceWindow::pendingIp(),
                    'Activo' => true,
                ])
                ->all();

            AttendanceWindow::open($actividad->id, now()->startOfSecond()->addMinutes(5));

            foreach (array_chunk($asistencias, 500) as $lote) {
                DB::table('asistencias')->upsert(
                    $lote,
                    ['usuario_id', 'conferencia_taller_id'],
                    ['Activo']
                );
            }

            DB::table('asistencias')
                ->where('conferencia_taller_id', $actividad->id)
                ->update(['Activo' => true]);
        });

        return redirect()
            ->route('congresos.staff.activities', $actividad->congreso_id)
            ->with('status', 'El registro de asistencia estará abierto durante cinco minutos.');
    }

    public function store(Request $request, ConferenciaTaller $actividad): RedirectResponse
    {
        $usuario = $request->user();
        $inscrito = InscripcionConferenciaTaller::query()
            ->where('usuario_id', $usuario->id)
            ->where('conferencia_taller_id', $actividad->id)
            ->exists();

        abort_unless($inscrito, 404);

        AttendanceWindow::expireActivity($actividad->id);

        DB::transaction(function () use ($request, $actividad, $usuario): void {
            $actividad = ConferenciaTaller::query()
                ->whereKey($actividad->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $actividad->asistencia_activa_hasta?->isFuture()) {
                throw ValidationException::withMessages([
                    'asistencia' => 'El registro de asistencia no está abierto o ya venció.',
                ]);
            }

            $asistencia = DB::table('asistencias')
                ->where('usuario_id', $usuario->id)
                ->where('conferencia_taller_id', $actividad->id)
                ->lockForUpdate()
                ->first();

            if (! $asistencia || ! $asistencia->Activo) {
                throw ValidationException::withMessages([
                    'asistencia' => 'El registro de asistencia no está abierto para ti.',
                ]);
            }

            if ($asistencia->ip !== AttendanceWindow::pendingIp()) {
                throw ValidationException::withMessages([
                    'asistencia' => 'Ya registraste tu asistencia a esta actividad.',
                ]);
            }

            DB::table('asistencias')
                ->where('usuario_id', $usuario->id)
                ->where('conferencia_taller_id', $actividad->id)
                ->update([
                    'fecha_hora' => now(),
                    'ip' => $request->ip(),
                    'Activo' => true,
                ]);
        });

        return back()->with('status', 'Tu asistencia se registró correctamente.');
    }

    public function status(Request $request, ConferenciaTaller $actividad): JsonResponse
    {
        $usuario = $request->user();
        $inscrito = InscripcionConferenciaTaller::query()
            ->where('usuario_id', $usuario->id)
            ->where('conferencia_taller_id', $actividad->id)
            ->exists();

        abort_unless($inscrito, 404);

        AttendanceWindow::expireActivity($actividad->id);

        $asistencia = Asistencia::query()
            ->where('usuario_id', $usuario->id)
            ->where('conferencia_taller_id', $actividad->id)
            ->first();
        $registrada = $asistencia !== null
            && $asistencia->ip !== AttendanceWindow::pendingIp();
        $activa = $actividad->asistencia_activa_hasta?->isFuture()
            && $asistencia?->Activo;

        return response()->json([
            'activa' => (bool) $activa,
            'registrada' => $registrada,
            'fecha_hora' => $asistencia?->fecha_hora?->format('d/m/Y H:i'),
            'vence_en' => $activa ? $actividad->asistencia_activa_hasta->format('H:i:s') : null,
        ]);
    }

    private function authorizeStaff(Request $request, ConferenciaTaller $actividad): void
    {
        $congreso = $actividad->congreso;
        $esSolicitante = $congreso->solicitante_id === $request->user()->id;
        $esOrganizadorAceptado = InscripcionCongreso::query()
            ->where('congreso_id', $congreso->id)
            ->where('usuario_id', $request->user()->id)
            ->whereIn('rol_congreso', ['organizador', 'comite'])
            ->where('aceptado', true)
            ->exists();

        abort_unless(
            ($esSolicitante || $esOrganizadorAceptado)
                && $congreso->dictamen === 'aceptada',
            404
        );
    }
}
