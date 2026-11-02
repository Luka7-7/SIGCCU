<x-layout.main title="Conferencias y talleres · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <a href="{{ route('docente.dashboard') }}" class="btn btn-link px-0">← Volver al portal docente</a>
      <h1 class="page-title">{{ $congreso->nombre }}</h1>
      <p class="text-secondary mb-0">Gestiona las conferencias y talleres de este congreso.</p>
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

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="card-title">Habilitar conferencia o taller</h2>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('docente.congresos.activities.store', $congreso) }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-4">
              <label for="tipo" class="form-label">Tipo de actividad</label>
              <select id="tipo" name="tipo" class="form-select" required>
                <option value="">Selecciona un tipo</option>
                @foreach (['conferencia' => 'Conferencia', 'taller' => 'Taller'] as $value => $label)
                  <option value="{{ $value }}" @selected(old('tipo') === $value)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-8">
              <label for="ubicacion" class="form-label">Ubicación</label>
              <input
                id="ubicacion"
                name="ubicacion"
                class="form-control"
                value="{{ old('ubicacion') }}"
                maxlength="100"
                required
              >
            </div>
            <div class="col-12">
              <label for="descripcion" class="form-label">Nombre y descripción de la actividad</label>
              <textarea
                id="descripcion"
                name="descripcion"
                class="form-control"
                rows="4"
                maxlength="5000"
                required
              >{{ old('descripcion') }}</textarea>
            </div>
            <div class="col-md-4">
              <label for="cupo" class="form-label">Cupo</label>
              <input
                id="cupo"
                name="cupo"
                type="number"
                min="1"
                class="form-control"
                value="{{ old('cupo') }}"
                required
              >
            </div>
            <div class="col-md-4">
              <label for="fecha" class="form-label">Fecha</label>
              <input
                id="fecha"
                name="fecha"
                type="date"
                min="{{ $congreso->fecha_inicio->toDateString() }}"
                max="{{ $congreso->fecha_fin->toDateString() }}"
                class="form-control"
                value="{{ old('fecha') }}"
                required
              >
            </div>
            <div class="col-md-2">
              <label for="hora_inicio" class="form-label">Hora de inicio</label>
              <input
                id="hora_inicio"
                name="hora_inicio"
                type="time"
                class="form-control"
                value="{{ old('hora_inicio') }}"
                required
              >
            </div>
            <div class="col-md-2">
              <label for="hora_fin" class="form-label">Hora de fin</label>
              <input
                id="hora_fin"
                name="hora_fin"
                type="time"
                class="form-control"
                value="{{ old('hora_fin') }}"
                required
              >
            </div>
          </div>
          <button type="submit" class="btn btn-primary mt-4">Habilitar actividad</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Actividades habilitadas</h2>
      </div>
      <div class="list-group list-group-flush">
        @forelse ($congreso->conferenciasTalleres as $actividad)
          <div class="list-group-item">
            <div class="d-flex flex-wrap justify-content-between gap-3">
              <div>
                <span class="badge bg-azure-lt">{{ ucfirst($actividad->tipo) }}</span>
                <p class="mb-1 mt-2">{{ $actividad->descripcion }}</p>
                <span class="text-secondary">
                  {{ $actividad->fecha->format('d/m/Y') }} ·
                  {{ substr($actividad->hora_inicio, 0, 5) }}–{{ substr($actividad->hora_fin, 0, 5) }} ·
                  {{ $actividad->ubicacion }} · Cupo: {{ $actividad->cupo }}
                </span>
              </div>
            </div>
          </div>
        @empty
          <div class="list-group-item text-secondary">Aún no hay conferencias o talleres habilitados.</div>
        @endforelse
      </div>
    </div>
  </div>
</x-layout.main>
