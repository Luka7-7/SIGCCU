<x-layout.main title="Actividades del congreso · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <a href="{{ route('congresos.index') }}" class="btn btn-link px-0">← Volver a congresos</a>
      <h1 class="page-title">{{ $congreso->nombre }}</h1>
      @if ($yaInscritoCongreso)
        <p class="text-secondary mb-0">Estas son las actividades que seleccionaste. La inscripción está confirmada y no se puede modificar.</p>
      @else
        <p class="text-secondary mb-0">
          Elige al menos {{ $congreso->minimo_conferencias }} conferencia(s) y
          {{ $congreso->minimo_talleres }} taller(es). La inscripción al congreso se confirmará
          junto con las actividades seleccionadas.
        </p>
      @endif
    </div>

    @if (! $yaInscritoCongreso && (now()->lt($congreso->registro_abre) || now()->gt($congreso->registro_cierre)))
      <div class="alert alert-warning" role="alert">
        El periodo de inscripción está cerrado. No puedes inscribirte en este momento.
      </div>
    @endif

    @if (! $yaInscritoCongreso && $errors->any())
      <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php
      $seleccionadas = $yaInscritoCongreso
        ? $actividadesInscritas
        : old('actividades', $actividadesInscritas);
      $seleccionadas = is_array($seleccionadas) ? $seleccionadas : [];
    @endphp

    @if ($yaInscritoCongreso)
      @if ($actividades->isEmpty())
        <div class="alert alert-info" role="status">No hay actividades registradas para esta inscripción.</div>
      @endif
    @else
      <form method="POST" action="{{ route('congresos.inscripcion.store', $congreso) }}">
        @csrf
    @endif

      @foreach (['conferencia' => 'Conferencias', 'taller' => 'Talleres'] as $tipo => $titulo)
        @php
          $actividadesTipo = $actividades->where('tipo', $tipo);
        @endphp
        <section class="card mb-4">
          <div class="card-header">
            <h2 class="card-title mb-0">{{ $titulo }}</h2>
          </div>
          <div class="list-group list-group-flush">
            @forelse ($actividadesTipo as $actividad)
              @php
                $seleccionada = in_array($actividad->id, $seleccionadas);
                $yaInscrito = in_array($actividad->id, $actividadesInscritas, true);
                $lugaresDisponibles = $actividad->cupo - $actividad->inscripciones_count + ($yaInscrito ? 1 : 0);
              @endphp
              <label class="list-group-item d-flex gap-3 align-items-start">
                <input
                  class="form-check-input mt-1"
                  type="checkbox"
                  name="actividades[]"
                  value="{{ $actividad->id }}"
                  @checked($yaInscritoCongreso || $seleccionada)
                  @disabled($yaInscritoCongreso || ($lugaresDisponibles <= 0 && ! $seleccionada))
                >
                <span class="w-100">
                  <span class="d-flex flex-wrap justify-content-between gap-2">
                    <strong>{{ $actividad->descripcion }}</strong>
                    <span class="badge bg-azure-lt">{{ ucfirst($actividad->tipo) }}</span>
                  </span>
                  <span class="d-block text-secondary">
                    {{ $actividad->fecha->format('d/m/Y') }} ·
                    {{ substr($actividad->hora_inicio, 0, 5) }}–{{ substr($actividad->hora_fin, 0, 5) }} ·
                    {{ $actividad->ubicacion }} ·
                    Lugares disponibles: {{ max(0, $lugaresDisponibles) }} de {{ $actividad->cupo }}
                  </span>
                </span>
              </label>
            @empty
              <div class="list-group-item text-secondary">No hay {{ strtolower($titulo) }} habilitadas.</div>
            @endforelse
          </div>
        </section>
      @endforeach

      @unless ($yaInscritoCongreso)
        <button
          type="submit"
          class="btn btn-primary"
          @disabled(now()->lt($congreso->registro_abre) || now()->gt($congreso->registro_cierre))
        >Confirmar inscripción al congreso</button>
        </form>
      @endunless
  </div>
</x-layout.main>
