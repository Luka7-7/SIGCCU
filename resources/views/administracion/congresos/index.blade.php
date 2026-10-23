<x-layout.main title="Revisión de congresos · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <h1 class="page-title">Revisión de solicitudes de congresos</h1>
    </div>

    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    @forelse ($congresos as $congreso)
      <section class="card mb-4">
        <div class="card-header">
          <h2 class="card-title">{{ $congreso->nombre }} ({{ $congreso->siglas }})</h2>
          <div class="card-actions">
            @if ($congreso->dictamen === null)
              <span class="badge bg-yellow-lt">Pendiente</span>
            @else
              <span class="badge bg-blue-lt">{{ ucfirst($congreso->dictamen) }}</span>
            @endif
          </div>
        </div>
        <div class="card-body">
          <p><strong>Solicitante:</strong> {{ $congreso->solicitante?->nombre_completo ?? 'Usuario eliminado' }}</p>
          <p><strong>Fechas:</strong> {{ $congreso->fecha_inicio->format('d/m/Y') }} al {{ $congreso->fecha_fin->format('d/m/Y') }}</p>
          <p><strong>Eje temático:</strong> {{ $congreso->eje_tematico }}</p>
          <p><strong>Descripción:</strong> {{ $congreso->descripcion }}</p>
          <p><strong>Programas participantes:</strong> {{ $congreso->programas->pluck('programa')->join(', ') }}</p>

          <div class="d-flex flex-wrap gap-2 mb-3">
            <a class="btn btn-outline-primary" href="{{ route('administracion.congresos.solicitud', $congreso) }}">
              Descargar solicitud PDF
            </a>
            @foreach ($congreso->propuestas as $propuesta)
              <a class="btn btn-outline-primary" href="{{ route('administracion.congresos.propuesta', $propuesta) }}">
                Descargar propuesta académica PDF
              </a>
            @endforeach
          </div>

          <form method="POST" action="{{ route('administracion.congresos.update', $congreso) }}" class="d-flex flex-wrap align-items-end gap-2">
            @csrf
            @method('PATCH')
            <div>
              <label class="form-label" for="dictamen-{{ $congreso->id }}">Dictamen</label>
              <select id="dictamen-{{ $congreso->id }}" name="dictamen" class="form-select" required>
                <option value="aceptada" @selected($congreso->dictamen === 'aceptada')>Aceptada</option>
                <option value="con observaciones" @selected($congreso->dictamen === 'con observaciones')>Con observaciones</option>
                <option value="rechazada" @selected($congreso->dictamen === 'rechazada')>Rechazada</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar dictamen</button>
          </form>
        </div>
      </section>
    @empty
      <div class="empty">
        <p class="empty-title">No hay solicitudes de congresos.</p>
      </div>
    @endforelse
  </div>
</x-layout.main>
