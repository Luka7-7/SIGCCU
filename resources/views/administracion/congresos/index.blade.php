<x-layout.main title="Revisión de congresos · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <h1 class="page-title">Revisión de solicitudes de congresos</h1>
    </div>

    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
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
          <p><strong>Edición:</strong> {{ $congreso->edicion }}</p>
          <p><strong>Solicitante:</strong> {{ $congreso->solicitante?->nombre_completo ?? 'Usuario eliminado' }}</p>
          @if ($congreso->solicitante)
            <p><strong>Correo:</strong> {{ $congreso->solicitante->correo_electronico }}</p>
            <p><strong>Programa del solicitante:</strong> {{ $congreso->solicitante->nombre_carrera }}</p>
          @endif
          <p><strong>Fechas:</strong> {{ $congreso->fecha_inicio->format('d/m/Y') }} al {{ $congreso->fecha_fin->format('d/m/Y') }}</p>
          <p><strong>Registro:</strong> {{ $congreso->registro_abre->format('d/m/Y H:i') }} al {{ $congreso->registro_cierre->format('d/m/Y H:i') }}</p>
          <p><strong>Eje temático:</strong> {{ $congreso->eje_tematico }}</p>
          <p><strong>Descripción:</strong> {{ $congreso->descripcion }}</p>
          <p><strong>Programas participantes:</strong> {{ $congreso->programas->pluck('programa')->join(', ') ?: 'No especificados' }}</p>

          <div class="d-flex flex-wrap gap-2 mb-3">
            @if ($congreso->solicitud_pdf)
              <a class="btn btn-outline-primary" href="{{ route('administracion.congresos.solicitud', $congreso) }}">
                Descargar solicitud PDF
              </a>
            @endif
            @foreach ($congreso->propuestas as $propuesta)
              <a class="btn btn-outline-primary" href="{{ route('administracion.congresos.propuesta', $propuesta) }}">
                Descargar propuesta académica PDF
              </a>
            @endforeach
          </div>

          @forelse ($congreso->propuestas as $propuesta)
            <section class="border rounded p-3 mb-3">
              <h3 class="h3">Evaluación de propuesta de {{ $propuesta->usuario?->nombre_completo ?? 'Usuario eliminado' }}</h3>
              <p>
                <strong>Estado:</strong>
                @if ($propuesta->evaluador)
                  {{ $propuesta->evaluador->usuario?->nombre_completo ?? 'Docente eliminado' }}
                  — {{ $propuesta->evaluador_aceptado ? 'Asignación aceptada' : 'Pendiente de aceptación' }}
                @else
                  Sin evaluador asignado
                @endif
              </p>
              <form method="POST" action="{{ route('administracion.propuestas.assign-evaluator', $propuesta) }}" class="d-flex flex-wrap align-items-end gap-2">
                @csrf
                @method('PATCH')
                <div>
                  <label class="form-label" for="evaluador-{{ $propuesta->id }}">Docente evaluador</label>
                  <select id="evaluador-{{ $propuesta->id }}" name="evaluador_id" class="form-select" required>
                    <option value="">Selecciona un docente</option>
                    @foreach ($docentes as $docente)
                      @if ($docente->id !== $propuesta->usuario_id)
                        <option value="{{ $docente->id }}" @selected($propuesta->evaluador?->usuario_id === $docente->id)>
                          {{ $docente->nombre_completo }} — {{ $docente->nombre_carrera }}
                        </option>
                      @endif
                    @endforeach
                  </select>
                </div>
                <button type="submit" class="btn btn-outline-primary">Asignar evaluador</button>
              </form>
            </section>
          @empty
            <p class="text-secondary">Esta solicitud aún no tiene propuestas académicas.</p>
          @endforelse

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
