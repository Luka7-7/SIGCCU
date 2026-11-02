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
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocenteController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        return view('docente.dashboard', [
            'usuario' => $usuario,
            'congresos' => $usuario->congresosSolicitados()
                ->with(['programas', 'propuestas.comentarios'])
                ->orderByDesc('id')
                ->get(),
            'evaluaciones' => $usuario->evaluador?->propuestas()
                ->with(['congreso', 'usuario', 'comentarios'])
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

    public function activities(Request $request, Congreso $congreso): View
    {
        $this->authorizeCongressActivities($request, $congreso);

        return view('docente.congresos.activities', [
            'congreso' => $congreso->load([
                'conferenciasTalleres' => fn ($query) => $query
                    ->orderBy('fecha')
                    ->orderBy('hora_inicio'),
            ]),
        ]);
    }

    public function storeActivity(Request $request, Congreso $congreso): RedirectResponse
    {
        $this->authorizeCongressActivities($request, $congreso);

        $data = $request->validate([
            'descripcion' => ['required', 'string', 'max:5000'],
            'tipo' => ['required', Rule::in(['conferencia', 'taller'])],
            'cupo' => ['required', 'integer', 'min:1'],
            'ubicacion' => ['required', 'string', 'max:100'],
            'fecha' => [
                'required',
                'date',
                'after_or_equal:'.$congreso->fecha_inicio->toDateString(),
                'before_or_equal:'.$congreso->fecha_fin->toDateString(),
            ],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'descripcion.required' => 'Escribe la descripción de la actividad.',
            'descripcion.max' => 'La descripción no debe exceder 5000 caracteres.',
            'tipo.required' => 'Selecciona si es conferencia o taller.',
            'tipo.in' => 'El tipo de actividad seleccionado no es válido.',
            'cupo.required' => 'Indica el cupo de la actividad.',
            'cupo.min' => 'El cupo debe ser de al menos una persona.',
            'ubicacion.required' => 'Indica la ubicación de la actividad.',
            'fecha.after_or_equal' => 'La fecha debe estar dentro de las fechas del congreso.',
            'fecha.before_or_equal' => 'La fecha debe estar dentro de las fechas del congreso.',
            'hora_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',
        ]);

        $congreso->conferenciasTalleres()->create($data);

        return redirect()
            ->route('docente.congresos.activities', $congreso)
            ->with('status', 'La conferencia o taller se habilitó correctamente.');
    }

    private function authorizeCongressActivities(Request $request, Congreso $congreso): void
    {
        abort_unless(
            $congreso->solicitante_id === $request->user()->id
                && $congreso->dictamen === 'aceptada',
            404
        );
    }

    public function viewProposal(Request $request, Propuesta $propuesta): BinaryFileResponse
    {
        $this->authorizeAcceptedEvaluation($request, $propuesta);

        $path = $propuesta->resumen_url;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return $this->inlinePdf($path, 'resumen-propuesta.pdf');
    }

    public function viewExtendedProposal(Request $request, Propuesta $propuesta): BinaryFileResponse
    {
        $isAuthor = $propuesta->usuario_id === $request->user()->id;
        $isAcceptedEvaluator = $propuesta->evaluador?->usuario_id === $request->user()->id
            && $propuesta->evaluador_aceptado;
        abort_unless($isAuthor || $isAcceptedEvaluator, 404);

        $path = $propuesta->extenso_url;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return $this->inlinePdf($path, 'propuesta-extensa.pdf');
    }

    public function storeComment(Request $request, Propuesta $propuesta): RedirectResponse
    {
        $this->authorizeAcceptedEvaluation($request, $propuesta);
        abort_unless(
            ! $propuesta->extenso_url || $propuesta->resumen_estado !== 'Aceptado',
            404
        );

        $data = $request->validate([
            'comentario' => ['required', 'string', 'max:256'],
            'resumen_estado' => ['required', Rule::in(['A corregir', 'Aceptado', 'Rechazado'])],
        ], [
            'comentario.required' => 'Escribe los comentarios de devolución.',
            'comentario.max' => 'El comentario no debe exceder 256 caracteres.',
            'resumen_estado.required' => 'Selecciona el estado de la propuesta.',
            'resumen_estado.in' => 'El estado seleccionado no es válido.',
        ]);

        DB::transaction(function () use ($propuesta, $data): void {
            $propuesta->comentarios()->create([
                'Comentario' => $data['comentario'],
            ]);
            $propuesta->update(['resumen_estado' => $data['resumen_estado']]);

            if ($propuesta->extenso_url && $data['resumen_estado'] === 'Aceptado') {
                $propuesta->congreso()->update(['dictamen' => 'aceptada']);
            }
        });

        return redirect()
            ->route('docente.dashboard')
            ->with('status', 'Los comentarios se enviaron al autor de la propuesta.');
    }

    public function storeExtendedProposal(Request $request, Propuesta $propuesta): RedirectResponse
    {
        abort_unless(
            $propuesta->usuario_id === $request->user()->id
                && (
                    (! $propuesta->extenso_url && $propuesta->resumen_estado === 'Aceptado')
                    || ($propuesta->extenso_url && $propuesta->resumen_estado === 'A corregir')
                ),
            404
        );

        $data = $request->validate([
            'extenso_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'extenso_pdf.required' => 'Selecciona el PDF de la propuesta extensa.',
            'extenso_pdf.mimes' => 'La propuesta extensa debe ser un archivo PDF.',
            'extenso_pdf.max' => 'El PDF de la propuesta extensa no debe exceder 10 MB.',
        ]);

        $path = Storage::disk('local')->putFile('propuestas', $data['extenso_pdf']);
        if (! $path) {
            throw new RuntimeException('No fue posible guardar el PDF de la propuesta extensa.');
        }

        $previousPath = $propuesta->extenso_url;
        $propuesta->update([
            'extenso_url' => $path,
            'resumen_estado' => 'En revision',
        ]);

        if ($previousPath && $previousPath !== $path) {
            Storage::disk('local')->delete($previousPath);
        }

        return redirect()
            ->route('docente.dashboard')
            ->with('status', 'La propuesta extensa se envió correctamente.');
    }

    private function authorizeAcceptedEvaluation(Request $request, Propuesta $propuesta): void
    {
        abort_unless(
            $propuesta->evaluador?->usuario_id === $request->user()->id
                && $propuesta->evaluador_aceptado,
            404
        );
    }

    private function inlinePdf(string $path, string $filename): BinaryFileResponse
    {
        return response()->file(Storage::disk('local')->path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
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
            'propuesta_pdf.mimes' => 'El resumen de la propuesta debe ser un archivo PDF.',
            'propuesta_pdf.max' => 'El PDF del resumen no debe exceder 10 MB.',
        ]);

        $solicitudPath = Storage::disk('local')->putFile(
            'solicitudes-congresos',
            $request->file('solicitud_pdf')
        );
        $resumenPath = Storage::disk('local')->putFile(
            'propuestas',
            $request->file('propuesta_pdf')
        );

        if (! $solicitudPath || ! $resumenPath) {
            throw new RuntimeException('No fue posible guardar los documentos PDF enviados.');
        }

        DB::transaction(function () use ($data, $request, $solicitudPath, $resumenPath): void {
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
                'resumen_url' => $resumenPath,
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
