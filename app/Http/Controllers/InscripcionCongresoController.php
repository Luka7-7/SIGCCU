<?php

namespace App\Http\Controllers;

use App\Models\Congreso;
use App\Models\InscripcionConferenciaTaller;
use App\Models\InscripcionCongreso;
use App\Models\Usuario;
use App\Support\AttendanceWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InscripcionCongresoController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $nombreCarrera = $usuario->carrera?->nombre_carrera;

        $congresos = Congreso::query()
            ->where('dictamen', 'aceptada')
            ->when(
                $nombreCarrera,
                fn ($query) => $query->whereHas(
                    'programas',
                    fn ($programas) => $programas->where('programa', $nombreCarrera)
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->withCount([
                'inscripciones as usuario_inscrito' => fn ($inscripciones) => $inscripciones
                    ->where('usuario_id', $usuario->id),
            ])
            ->with([
                'inscripciones' => fn ($inscripciones) => $inscripciones
                    ->where('usuario_id', $usuario->id),
            ])
            ->with([
                'conferenciasTalleres' => fn ($actividades) => $actividades
                    ->withCount('inscripciones')
                    ->whereHas(
                        'inscripciones',
                        fn ($inscripciones) => $inscripciones->where('usuario_id', $usuario->id)
                    )
                    ->orderBy('fecha')
                    ->orderBy('hora_inicio'),
            ])
            ->orderBy('fecha_inicio')
            ->get();

        return view('congresos.inscripciones', compact('congresos', 'usuario'));
    }

    public function activities(Request $request, Congreso $congreso): View
    {
        $this->authorizeEligibleCongress($request->user(), $congreso);
        $usuario = $request->user();
        $yaInscritoCongreso = $usuario->inscripcionesCongresos()
            ->where('congreso_id', $congreso->id)
            ->exists();
        $consultaActividades = $congreso->conferenciasTalleres()
            ->withCount('inscripciones')
            ->with([
                'asistencias' => fn ($asistencias) => $asistencias
                    ->where('usuario_id', $usuario->id),
            ])
            ->orderBy('fecha')
            ->orderBy('hora_inicio');

        AttendanceWindow::expireExpired();

        if ($yaInscritoCongreso) {
            $consultaActividades->whereHas(
                'inscripciones',
                fn ($inscripciones) => $inscripciones->where('usuario_id', $usuario->id)
            );
        }

        $actividades = $consultaActividades->get();
        $actividadesInscritas = $actividades->modelKeys();

        return view('congresos.actividades', compact(
            'congreso',
            'actividades',
            'actividadesInscritas',
            'yaInscritoCongreso',
            'usuario'
        ));
    }

    public function store(Request $request, Congreso $congreso): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->authorizeEligibleCongress($usuario, $congreso);

        if (now()->lt($congreso->registro_abre) || now()->gt($congreso->registro_cierre)) {
            throw ValidationException::withMessages([
                'congreso' => 'El periodo de inscripción de este congreso no está abierto.',
            ]);
        }

        if ($usuario->inscripcionesCongresos()->where('congreso_id', $congreso->id)->exists()) {
            throw ValidationException::withMessages([
                'congreso' => 'Ya confirmaste tu inscripción; no es posible cambiar las actividades seleccionadas.',
            ]);
        }

        $data = $request->validate([
            'actividades' => ['nullable', 'array'],
            'actividades.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('conferencias_talleres', 'id')
                    ->where('congreso_id', $congreso->id),
            ],
        ], [
            'actividades.*.exists' => 'Selecciona únicamente actividades de este congreso.',
        ]);
        $actividadIds = array_map('intval', $data['actividades'] ?? []);
        $actividades = $congreso->conferenciasTalleres()
            ->whereIn('id', $actividadIds)
            ->get();

        $conferenciasSeleccionadas = $actividades->where('tipo', 'conferencia')->count();
        $talleresSeleccionados = $actividades->where('tipo', 'taller')->count();
        $erroresMinimos = [];

        if ($conferenciasSeleccionadas < $congreso->minimo_conferencias) {
            $erroresMinimos[] = sprintf(
                'Selecciona al menos %d conferencia(s); seleccionaste %d.',
                $congreso->minimo_conferencias,
                $conferenciasSeleccionadas
            );
        }

        if ($talleresSeleccionados < $congreso->minimo_talleres) {
            $erroresMinimos[] = sprintf(
                'Selecciona al menos %d taller(es); seleccionaste %d.',
                $congreso->minimo_talleres,
                $talleresSeleccionados
            );
        }

        if ($erroresMinimos !== []) {
            throw ValidationException::withMessages(['actividades' => $erroresMinimos]);
        }

        $actividadesDelCongreso = $congreso->conferenciasTalleres()->pluck('id');

        DB::transaction(function () use ($usuario, $congreso, $actividadIds): void {
            if ($usuario->inscripcionesCongresos()->where('congreso_id', $congreso->id)->exists()) {
                throw ValidationException::withMessages([
                    'congreso' => 'Ya confirmaste tu inscripción; no es posible cambiar las actividades seleccionadas.',
                ]);
            }

            $actividadesSeleccionadas = $congreso->conferenciasTalleres()
                ->whereIn('id', $actividadIds)
                ->lockForUpdate()
                ->get();

            foreach ($actividadesSeleccionadas as $actividad) {
                $lugaresOcupados = $actividad->inscripciones()
                    ->count();

                if ($lugaresOcupados >= $actividad->cupo) {
                    throw ValidationException::withMessages([
                        'actividades' => "Ya no hay lugares disponibles para: {$actividad->descripcion}.",
                    ]);
                }
            }

            $usuario->inscripcionesCongresos()->create([
                'congreso_id' => $congreso->id,
                'fecha_hora_inscripcion' => now(),
            ]);

            foreach ($actividadesSeleccionadas as $actividad) {
                InscripcionConferenciaTaller::query()->create([
                    'usuario_id' => $usuario->id,
                    'conferencia_taller_id' => $actividad->id,
                    'fecha_hora_inscripcion' => now(),
                    'tipo_participacion' => 'asistente',
                ]);
            }
        });

        return redirect()
            ->route('congresos.index')
            ->with('status', 'Tu inscripción al congreso y a las actividades seleccionadas se registró correctamente.');
    }

    public function applyAsOrganizer(Request $request, Congreso $congreso): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        $this->authorizeEligibleCongress($usuario, $congreso);

        DB::transaction(function () use ($usuario, $congreso): void {
            Usuario::query()->whereKey($usuario->id)->lockForUpdate()->firstOrFail();

            $inscripcion = InscripcionCongreso::query()
                ->where('usuario_id', $usuario->id)
                ->where('congreso_id', $congreso->id)
                ->lockForUpdate()
                ->first();

            if ($inscripcion?->rol_congreso === 'organizador') {
                throw ValidationException::withMessages([
                    'congreso' => 'Ya enviaste una postulación para formar parte del staff organizador.',
                ]);
            }

            if ($inscripcion) {
                $inscripcion->update([
                    'rol_congreso' => 'organizador',
                    'aceptado' => false,
                    'fecha_hora_respuesta' => null,
                ]);

                return;
            }

            InscripcionCongreso::query()->create([
                'usuario_id' => $usuario->id,
                'congreso_id' => $congreso->id,
                'fecha_hora_inscripcion' => now(),
                'aceptado' => false,
                'rol_congreso' => 'organizador',
            ]);
        });

        return redirect()
            ->route('congresos.index')
            ->with('status', 'Tu postulación para formar parte del staff organizador se registró correctamente.');
    }

    private function authorizeEligibleCongress(Usuario $usuario, Congreso $congreso): void
    {
        $nombreCarrera = $usuario->carrera?->nombre_carrera;

        abort_unless(
            $congreso->dictamen === 'aceptada'
                && $nombreCarrera
                && $congreso->programas()->where('programa', $nombreCarrera)->exists(),
            404
        );
    }
}
