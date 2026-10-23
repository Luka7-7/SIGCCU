<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Congreso;
use App\Models\Propuesta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RevisionCongresoController extends Controller
{
    public function index(): View
    {
        return view('administracion.congresos.index', [
            'congresos' => Congreso::query()
                ->whereNotNull('solicitante_id')
                ->with(['solicitante', 'programas', 'propuestas'])
                ->orderByRaw('CASE WHEN dictamen IS NULL THEN 0 ELSE 1 END')
                ->orderByDesc('solicitada_en')
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
        ]);

        $congreso->update(['dictamen' => $data['dictamen']]);

        return redirect()
            ->route('administracion.congresos.index')
            ->with('status', 'El dictamen del congreso se actualizó correctamente.');
    }
}
