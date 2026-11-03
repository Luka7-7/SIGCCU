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

            <div>
              @if ($congreso->usuario_inscrito)
                <span class="badge bg-green-lt">Ya estás inscrito</span>
              @elseif ($inscripcionesAbiertas)
                <a class="btn btn-primary" href="{{ route('congresos.activities', $congreso) }}">
                  Seleccionar actividades
                </a>
              @else
                <span class="badge bg-secondary-lt">
                  {{ now()->lt($congreso->registro_abre) ? 'Inscripción próximamente' : 'Inscripción cerrada' }}
                </span>
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
                      </span>
                    </li>
                  @endforeach
                </ul>
              @else
                <p class="text-secondary mb-0">No seleccionaste actividades para este congreso.</p>
              @endif
            </div>
          @endif
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
