<?php

namespace App\Http\Controllers;

use App\Models\Congreso;
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
            'programas' => PerfilController::PROGRAMAS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
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
            'programas.*' => ['required', 'string', Rule::in(PerfilController::PROGRAMAS)],
            'solicitud_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'propuesta_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'programas.required' => 'Selecciona al menos un programa participante.',
            'programas.min' => 'Selecciona al menos un programa participante.',
            'programas.*.in' => 'Uno de los programas seleccionados no es válido.',
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
