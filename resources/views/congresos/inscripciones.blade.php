<x-layout.main title="Congresos disponibles · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <h1 class="page-title">Congresos disponibles</h1>
      <p class="text-secondary mb-0">
        Congresos aprobados para la carrera {{ $usuario->carrera?->nombre_carrera ?? 'no especificada' }}.
      </p>
    </div>

    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    @error('congreso')
      <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror

    @forelse ($congresos as $congreso)
      @php
        $inscripcionesAbiertas = now()->between($congreso->registro_abre, $congreso->registro_cierre);
        $inscripcionUsuario = $congreso->inscripciones->first();
        $postulacionOrganizador = $inscripcionUsuario?->rol_congreso === 'organizador';
        $esStaff = $congreso->solicitante_id === $usuario->id
          || (
            in_array($inscripcionUsuario?->rol_congreso, ['organizador', 'comite'], true)
            && $inscripcionUsuario->aceptado
          );
      @endphp
      <article class="card mb-3">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
              <h2 class="h2 mb-1">{{ $congreso->nombre }} · {{ $congreso->siglas }}</h2>
              <p class="text-secondary mb-2">
                Edición {{ $congreso->edicion }} ·
                {{ $congreso->fecha_inicio->format('d/m/Y') }}–{{ $congreso->fecha_fin->format('d/m/Y') }}
              </p>
              <p class="mb-2">{{ $congreso->descripcion }}</p>
              <p class="mb-0"><strong>Eje temático:</strong> {{ $congreso->eje_tematico }}</p>
              <p class="text-secondary mb-0">
                Inscripción del {{ $congreso->registro_abre->format('d/m/Y H:i') }}
                al {{ $congreso->registro_cierre->format('d/m/Y H:i') }}
              </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
              @if ($congreso->usuario_inscrito)
                <span class="badge bg-green-lt">Ya estás inscrito</span>
                <a class="btn btn-outline-primary" href="{{ route('congresos.activities', $congreso) }}">
                  Ver actividades y asistencia
                </a>
              @elseif ($inscripcionesAbiertas)
                <a class="btn btn-primary" href="{{ route('congresos.activities', $congreso) }}">
                  Seleccionar actividades
                </a>
              @else
                <span class="badge bg-secondary-lt">
                  {{ now()->lt($congreso->registro_abre) ? 'Inscripción próximamente' : 'Inscripción cerrada' }}
                </span>
              @endif

              @if ($esStaff)
                <a class="btn btn-primary" href="{{ route('congresos.staff.activities', $congreso) }}">
                  Gestionar asistencia
                </a>
              @endif

              @if ($postulacionOrganizador)
                @if ($inscripcionUsuario->aceptado)
                  <span class="badge bg-green-lt">Postulación aceptada: staff organizador</span>
                @elseif ($inscripcionUsuario->fecha_hora_respuesta)
                  <span class="badge bg-red-lt">Postulación rechazada</span>
                @else
                  <span class="badge bg-yellow-lt">Postulación pendiente: staff organizador</span>
                @endif
              @else
                <button
                  type="button"
                  class="btn btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#postulacion-organizador-{{ $congreso->id }}"
                >Postularme como parte del staff organizador</button>
              @endif
            </div>
          </div>
          @if ($congreso->usuario_inscrito)
            <div class="mt-4">
              <h3 class="h3">Actividades seleccionadas</h3>
              @if ($congreso->conferenciasTalleres->isNotEmpty())
                <ul class="mb-0">
                  @foreach ($congreso->conferenciasTalleres as $actividad)
                    <li>
                      <strong>{{ ucfirst($actividad->tipo) }}:</strong> {{ $actividad->descripcion }}
                      <span class="text-secondary">
                        · {{ $actividad->fecha->format('d/m/Y') }}
                        · {{ substr($actividad->hora_inicio, 0, 5) }}–{{ substr($actividad->hora_fin, 0, 5) }}
                        · {{ $actividad->ubicacion }}
                        · Lugares disponibles: {{ max(0, $actividad->cupo - $actividad->inscripciones_count) }} de {{ $actividad->cupo }}
                      </span>
                    </li>
                  @endforeach
                </ul>
              @else
                <p class="text-secondary mb-0">No seleccionaste actividades para este congreso.</p>
              @endif
            </div>
          @endif

          @unless ($postulacionOrganizador)
            <div
              class="modal modal-blur fade"
              id="postulacion-organizador-{{ $congreso->id }}"
              tabindex="-1"
              role="dialog"
              aria-labelledby="postulacion-organizador-titulo-{{ $congreso->id }}"
              aria-hidden="true"
            >
              <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h2 class="modal-title" id="postulacion-organizador-titulo-{{ $congreso->id }}">
                      Confirmar postulación
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                  </div>
                  <div class="modal-body">
                    ¿Estás seguro de registrarte como parte del staff organizador de
                    <strong>{{ $congreso->nombre }}</strong>?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                      Cancelar
                    </button>
                    <form method="POST" action="{{ route('congresos.organizador.store', $congreso) }}">
                      @csrf
                      <button type="submit" class="btn btn-primary">Sí, postularme</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @endunless
        </div>
      </article>
    @empty
      <div class="card">
        <div class="card-body text-secondary">
          No hay congresos aprobados disponibles para tu carrera.
        </div>
      </div>
    @endforelse
  </div>
</x-layout.main>
