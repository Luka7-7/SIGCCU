<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Congreso;
use App\Models\Propuesta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class DocenteController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        return view('docente.dashboard', [
            'usuario' => $usuario,
            'congresos' => $usuario->congresosSolicitados()
                ->with(['programas', 'propuestas'])
                ->orderByDesc('id')
                ->get(),
            'evaluaciones' => $usuario->evaluador?->propuestas()
                ->with(['congreso', 'usuario'])
                ->orderByDesc('id')
                ->get() ?? collect(),
            'carreras' => Carrera::query()
                ->select('area', 'unidad_academica', 'nombre_carrera')
                ->distinct()
                ->orderBy('area')
                ->orderBy('unidad_academica')
                ->orderBy('nombre_carrera')
                ->get(),
        ]);
    }

    public function acceptProposal(Request $request, Propuesta $propuesta): RedirectResponse
    {
        abort_unless($propuesta->evaluador?->usuario_id === $request->user()->id, 404);

        if (! $propuesta->evaluador_aceptado) {
            DB::transaction(function () use ($propuesta): void {
                $propuesta->update([
                    'evaluador_aceptado' => true,
                    'resumen_estado' => 'En revision',
                ]);
            });
        }

        return redirect()
            ->route('docente.dashboard')
            ->with('status', 'Aceptaste la asignación para evaluar la propuesta.');
    }

    public function store(Request $request): RedirectResponse
    {
        $areas = Carrera::query()->distinct()->pluck('area')->all();

        $seleccion = $request->validate([
            'areas_participantes' => ['required', 'array', 'min:1'],
            'areas_participantes.*' => ['required', 'string', 'distinct', Rule::in($areas)],
        ], [
            'areas_participantes.required' => 'Selecciona al menos un área académica.',
            'areas_participantes.min' => 'Selecciona al menos un área académica.',
            'areas_participantes.*.in' => 'Una de las áreas académicas seleccionadas no es válida.',
        ]);

        $unidades = Carrera::query()
            ->whereIn('area', $seleccion['areas_participantes'])
            ->distinct()
            ->pluck('unidad_academica')
            ->all();

        $seleccion['unidades_participantes'] = $request->validate([
            'unidades_participantes' => ['required', 'array', 'min:1'],
            'unidades_participantes.*' => ['required', 'string', 'distinct', Rule::in($unidades)],
        ], [
            'unidades_participantes.required' => 'Selecciona al menos una unidad académica.',
            'unidades_participantes.min' => 'Selecciona al menos una unidad académica.',
            'unidades_participantes.*.in' => 'Una de las unidades no corresponde a las áreas seleccionadas.',
        ])['unidades_participantes'];

        $programasDisponibles = Carrera::query()
            ->whereIn('area', $seleccion['areas_participantes'])
            ->whereIn('unidad_academica', $seleccion['unidades_participantes'])
            ->distinct()
            ->pluck('nombre_carrera')
            ->all();

        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'siglas' => ['required', 'string', 'max:12'],
            'edicion' => ['required', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'registro_abre' => ['required', 'date', 'after_or_equal:today'],
            'registro_cierre' => [
                'required',
                'date',
                'after:registro_abre',
                'before_or_equal:fecha_inicio',
            ],
            'descripcion' => ['required', 'string'],
            'eje_tematico' => ['required', 'string', 'max:100'],
            'programas' => ['required', 'array', 'min:1'],
            'programas.*' => ['required', 'string', 'distinct', Rule::in($programasDisponibles)],
            'solicitud_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'propuesta_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'programas.required' => 'Selecciona al menos un programa participante.',
            'programas.min' => 'Selecciona al menos un programa participante.',
            'programas.*.in' => 'Uno de los programas seleccionados no es válido.',
            'registro_cierre.after' => 'El cierre del registro debe ser posterior a su apertura.',
            'solicitud_pdf.mimes' => 'El documento de solicitud debe ser un archivo PDF.',
            'solicitud_pdf.max' => 'El PDF de solicitud no debe exceder 10 MB.',
            'propuesta_pdf.mimes' => 'La propuesta académica debe ser un archivo PDF.',
            'propuesta_pdf.max' => 'El PDF de la propuesta no debe exceder 10 MB.',
        ]);

        $solicitudPath = Storage::disk('local')->putFile(
            'solicitudes-congresos',
            $request->file('solicitud_pdf')
        );
        $propuestaPath = Storage::disk('local')->putFile(
            'propuestas',
            $request->file('propuesta_pdf')
        );

        if (! $solicitudPath || ! $propuestaPath) {
            throw new RuntimeException('No fue posible guardar los documentos PDF enviados.');
        }

        DB::transaction(function () use ($data, $request, $solicitudPath, $propuestaPath): void {
            $congreso = Congreso::create([
                'nombre' => $data['nombre'],
                'siglas' => $data['siglas'],
                'edicion' => $data['edicion'],
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'registro_abre' => $data['registro_abre'],
                'registro_cierre' => $data['registro_cierre'],
                'descripcion' => $data['descripcion'],
                'minimo_talleres' => 0,
                'minimo_conferencias' => 0,
                'eje_tematico' => $data['eje_tematico'],
                'dictamen' => null,
                'solicitante_id' => $request->user()->id,
                'solicitud_pdf' => $solicitudPath,
                'solicitada_en' => now(),
            ]);

            $congreso->programas()->createMany(
                collect($data['programas'])
                    ->unique()
                    ->map(fn (string $programa): array => ['programa' => $programa])
                    ->all()
            );

            $request->user()->propuestas()->create([
                'resumen_url' => $propuestaPath,
                'extenso_url' => null,
                'resumen_estado' => 'Enviado',
                'congreso_id' => $congreso->id,
                'evaluador_id' => null,
            ]);
        });

        return redirect()
            ->route('docente.dashboard')
            ->with('status', 'La solicitud del congreso y la propuesta académica se enviaron para revisión.');
    }
}
