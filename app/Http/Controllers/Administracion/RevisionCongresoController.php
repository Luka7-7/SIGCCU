<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Congreso;
use App\Models\Propuesta;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RevisionCongresoController extends Controller
{
    public function index(): View
    {
        return view('administracion.congresos.index', [
            'congresos' => Congreso::query()
                ->whereNotNull('solicitante_id')
                ->with([
                    'solicitante.carrera',
                    'programas',
                    'propuestas.usuario',
                    'propuestas.evaluador.usuario',
                ])
                ->orderByRaw('CASE WHEN dictamen IS NULL THEN 0 ELSE 1 END')
                ->orderByDesc('solicitada_en')
                ->get(),
            'docentes' => Usuario::query()
                ->where('rol', 'docente')
                ->with('carrera')
                ->orderBy('nombres')
                ->orderBy('apellidos')
                ->get(),
        ]);
    }

    public function downloadSolicitud(Congreso $congreso): StreamedResponse
    {
        abort_unless($congreso->solicitante_id !== null, 404);

        $path = $congreso->solicitud_pdf;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function downloadPropuesta(Propuesta $propuesta): StreamedResponse
    {
        $propuesta->loadMissing('congreso');
        abort_unless($propuesta->congreso?->solicitante_id !== null, 404);

        $path = $propuesta->resumen_url;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function update(Request $request, Congreso $congreso): RedirectResponse
    {
        abort_unless($congreso->solicitante_id !== null, 404);

        $data = $request->validate([
            'dictamen' => [
                'required',
                Rule::in(['aceptada', 'con observaciones', 'rechazada']),
            ],
        ], [
            'dictamen.required' => 'Selecciona un dictamen para la solicitud.',
            'dictamen.in' => 'El dictamen seleccionado no es válido.',
        ]);

        $congreso->update(['dictamen' => $data['dictamen']]);

        return redirect()
            ->route('administracion.congresos.index')
            ->with('status', 'El dictamen del congreso se actualizó correctamente.');
    }

    public function assignEvaluator(Request $request, Propuesta $propuesta): RedirectResponse
    {
        abort_unless($propuesta->congreso()->whereNotNull('solicitante_id')->exists(), 404);

        $data = $request->validate([
            'evaluador_id' => [
                'required',
                'integer',
                Rule::exists('usuarios', 'id')->where(
                    fn (Builder $query) => $query->where('rol', 'docente')
                ),
            ],
        ], [
            'evaluador_id.required' => 'Selecciona un docente evaluador.',
            'evaluador_id.exists' => 'El usuario seleccionado no es un docente válido.',
        ]);

        if ((int) $data['evaluador_id'] === $propuesta->usuario_id) {
            throw ValidationException::withMessages([
                'evaluador_id' => 'El autor de la propuesta no puede evaluarla.',
            ]);
        }

        DB::transaction(function () use ($data, $propuesta): void {
            $usuario = Usuario::query()->with('carrera')->findOrFail($data['evaluador_id']);
            $evaluador = $usuario->evaluador()->firstOrCreate([], [
                'licenciatura' => $usuario->nombre_carrera ?? 'No especificada',
            ]);

            $propuesta->update([
                'evaluador_id' => $evaluador->id,
                'evaluador_aceptado' => false,
            ]);
        });

        return redirect()
            ->route('administracion.congresos.index')
            ->with('status', 'El docente evaluador fue asignado y debe aceptar la propuesta desde su portal.');
    }
}
