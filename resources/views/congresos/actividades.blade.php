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

    @if ($errors->any())
      <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if (session('status'))
      <div class="alert alert-success" role="status">{{ session('status') }}</div>
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
                $lugaresDisponibles = max(0, $actividad->cupo - $actividad->inscripciones_count);
                $asistencia = $actividad->asistencias->first();
                $asistenciaRegistrada = $asistencia
                  && $asistencia->ip !== \App\Support\AttendanceWindow::pendingIp();
                $ventanaAsistenciaActiva = $actividad->asistencia_activa_hasta?->isFuture()
                  && $asistencia?->Activo;
              @endphp
              <div class="list-group-item">
                <label class="d-flex gap-3 align-items-start">
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
                      Lugares disponibles: {{ $lugaresDisponibles }} de {{ $actividad->cupo }}
                    </span>
                  </span>
                </label>
                @if ($yaInscritoCongreso)
                  <div class="mt-3 ms-4" data-asistencia
                    data-status-url="{{ route('asistencias.status', $actividad) }}"
                    data-marked="{{ $asistenciaRegistrada ? 'true' : 'false' }}">
                    <p class="mb-2 text-secondary" data-asistencia-mensaje>
                      @if ($asistenciaRegistrada)
                        Asistencia registrada el {{ $asistencia->fecha_hora->format('d/m/Y H:i') }}.
                      @elseif ($ventanaAsistenciaActiva)
                        La ventana está abierta hasta {{ $actividad->asistencia_activa_hasta->format('H:i:s') }}.
                      @else
                        El registro de asistencia aún no está abierto.
                      @endif
                    </p>
                    <form method="POST" action="{{ route('asistencias.store', $actividad) }}">
                      @csrf
                      <button
                        type="submit"
                        class="btn btn-success"
                        data-asistencia-boton
                        @disabled(! $ventanaAsistenciaActiva || $asistenciaRegistrada)
                      >
                        {{ $asistenciaRegistrada ? 'Asistencia registrada' : 'Registrar mi asistencia' }}
                      </button>
                    </form>
                  </div>
                @endif
              </div>
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
  @if ($yaInscritoCongreso)
    <script>
      document.querySelectorAll('[data-asistencia]').forEach((panel) => {
        const boton = panel.querySelector('[data-asistencia-boton]');
        const mensaje = panel.querySelector('[data-asistencia-mensaje]');
        let registrada = panel.dataset.marked === 'true';

        const actualizarEstado = () => {
          fetch(panel.dataset.statusUrl, { headers: { Accept: 'application/json' } })
            .then((response) => {
              if (!response.ok) {
                throw new Error('No fue posible consultar el estado de asistencia.');
              }

              return response.json();
            })
            .then((estado) => {
              registrada = estado.registrada;
              boton.disabled = registrada || !estado.activa;
              boton.textContent = registrada ? 'Asistencia registrada' : 'Registrar mi asistencia';
              mensaje.textContent = registrada
                ? `Asistencia registrada el ${estado.fecha_hora}.`
                : estado.activa
                  ? `La ventana está abierta hasta ${estado.vence_en}.`
                  : 'El registro de asistencia aún no está abierto.';
            })
            .catch((error) => {
              mensaje.textContent = error.message;
              boton.disabled = true;
            });
        };

        if (!registrada) {
          const intervalo = window.setInterval(() => {
            if (registrada) {
              window.clearInterval(intervalo);
              return;
            }

            actualizarEstado();
          }, 5000);
        }
      });
    </script>
  @endif
</x-layout.main>
