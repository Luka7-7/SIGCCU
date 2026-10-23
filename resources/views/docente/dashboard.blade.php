<x-layout.main title="Portal docente · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <h1 class="page-title">Portal docente</h1>
      <p class="text-secondary mb-0">Consulta tus datos y envía solicitudes de congreso y propuestas académicas.</p>
    </div>

    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="card-title">Mis datos</h2>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6"><strong>Nombre:</strong> {{ $usuario->nombres }} {{ $usuario->apellidos }}</div>
          <div class="col-md-6"><strong>Correo:</strong> {{ $usuario->correo_electronico }}</div>
          <div class="col-md-6"><strong>Número de empleado:</strong> {{ $usuario->matricula_numtrabajador }}</div>
          <div class="col-md-6"><strong>Unidad académica:</strong> {{ $usuario->unidad_academica }}</div>
          <div class="col-md-6"><strong>Programa:</strong> {{ $usuario->nombre_carrera }}</div>
          <div class="col-md-6"><strong>Área:</strong> {{ $usuario->area }}</div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h2 class="card-title">Solicitar un congreso y enviar una propuesta</h2>
      </div>
      <div class="card-body">
        <p class="text-secondary">
          La solicitud del congreso quedará pendiente de revisión por la administración.
          Adjunta un PDF para la solicitud y otro para la propuesta académica.
        </p>

        @if ($errors->any())
          <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('docente.congresos.store') }}" enctype="multipart/form-data">
          @csrf

          <h3 class="h3 mt-2">Datos del congreso</h3>
          <div class="row g-3">
            <div class="col-md-8">
              <label for="nombre" class="form-label">Nombre del congreso</label>
              <input id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" maxlength="120" required>
            </div>
            <div class="col-md-4">
              <label for="siglas" class="form-label">Siglas</label>
              <input id="siglas" name="siglas" class="form-control" value="{{ old('siglas') }}" maxlength="12" required>
            </div>
            <div class="col-md-4">
              <label for="edicion" class="form-label">Edición</label>
              <input id="edicion" name="edicion" type="number" min="1" class="form-control" value="{{ old('edicion', 1) }}" required>
            </div>
            <div class="col-md-4">
              <label for="fecha_inicio" class="form-label">Inicio del congreso</label>
              <input id="fecha_inicio" name="fecha_inicio" type="date" class="form-control" value="{{ old('fecha_inicio') }}" required>
            </div>
            <div class="col-md-4">
              <label for="fecha_fin" class="form-label">Fin del congreso</label>
              <input id="fecha_fin" name="fecha_fin" type="date" class="form-control" value="{{ old('fecha_fin') }}" required>
            </div>
            <div class="col-md-6">
              <label for="registro_abre" class="form-label">Apertura de registro</label>
              <input id="registro_abre" name="registro_abre" type="datetime-local" class="form-control" value="{{ old('registro_abre') }}" required>
            </div>
            <div class="col-md-6">
              <label for="registro_cierre" class="form-label">Cierre de registro</label>
              <input id="registro_cierre" name="registro_cierre" type="datetime-local" class="form-control" value="{{ old('registro_cierre') }}" required>
            </div>
            <div class="col-md-6">
              <label for="eje_tematico" class="form-label">Eje temático</label>
              <input id="eje_tematico" name="eje_tematico" class="form-control" value="{{ old('eje_tematico') }}" maxlength="100" required>
            </div>
            <div class="col-12">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea id="descripcion" name="descripcion" class="form-control" rows="3" required>{{ old('descripcion') }}</textarea>
            </div>
          </div>

          <fieldset class="mt-4">
            <legend class="h3">Programas participantes</legend>
            <p class="text-secondary">Selecciona todos los programas que podrán participar en este congreso.</p>
            <div class="row g-2">
              @foreach ($programas as $programa)
                <div class="col-md-6">
                  <label class="form-check">
                    <input
                      type="checkbox"
                      name="programas[]"
                      value="{{ $programa }}"
                      class="form-check-input"
                      @checked(in_array($programa, old('programas', []), true))
                    >
                    <span class="form-check-label">{{ $programa }}</span>
                  </label>
                </div>
              @endforeach
            </div>
          </fieldset>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label for="solicitud_pdf" class="form-label">PDF de solicitud del congreso</label>
              <input id="solicitud_pdf" name="solicitud_pdf" type="file" accept=".pdf,application/pdf" class="form-control" required>
              <div class="form-hint">PDF de hasta 10 MB.</div>
            </div>
            <div class="col-md-6">
              <label for="propuesta_pdf" class="form-label">PDF de propuesta académica</label>
              <input id="propuesta_pdf" name="propuesta_pdf" type="file" accept=".pdf,application/pdf" class="form-control" required>
              <div class="form-hint">Se asociará al congreso solicitado. PDF de hasta 10 MB.</div>
            </div>
          </div>

          <div class="form-footer mt-4">
            <button type="submit" class="btn btn-primary">Enviar solicitud y propuesta</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2 class="card-title">Mis solicitudes</h2>
      </div>
      <div class="table-responsive">
        <table class="table card-table table-vcenter">
          <thead>
            <tr>
              <th>Congreso</th>
              <th>Programas participantes</th>
              <th>Estado de revisión</th>
              <th>Propuesta académica</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($congresos as $congreso)
              <tr>
                <td>{{ $congreso->nombre }} ({{ $congreso->siglas }})</td>
                <td>{{ $congreso->programas->pluck('programa')->join(', ') }}</td>
                <td>
                  @if ($congreso->dictamen === null)
                    Pendiente de revisión
                  @else
                    {{ ucfirst($congreso->dictamen) }}
                  @endif
                </td>
                <td>{{ $congreso->propuestas->first()?->resumen_estado ?? 'Sin propuesta' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-secondary">Aún no has enviado solicitudes.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-layout.main>
