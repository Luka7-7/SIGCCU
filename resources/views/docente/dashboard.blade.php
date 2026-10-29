<x-layout.main title="Portal docente · SIGCCU">
  <div class="container-xl py-4">
    <div class="page-header mb-4">
      <h1 class="page-title">Portal docente</h1>
      <p class="text-secondary mb-0">Consulta tus datos, revisa propuestas asignadas y envía solicitudes de congreso.</p>
    </div>

    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    @php
      $historialEvaluaciones = $evaluaciones->filter(
        fn ($evaluacion) => $evaluacion->extenso_url && $evaluacion->resumen_estado === 'Aceptado'
      );
      $evaluacionesActivas = $evaluaciones->reject(
        fn ($evaluacion) => $evaluacion->extenso_url && $evaluacion->resumen_estado === 'Aceptado'
      );
    @endphp

    @if ($evaluacionesActivas->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header">
          <h2 class="card-title">Solicitudes de evaluación</h2>
        </div>
        <div class="list-group list-group-flush">
          @foreach ($evaluacionesActivas as $evaluacion)
            <div class="list-group-item">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                  <h3 class="h3 mb-1">{{ $evaluacion->congreso?->nombre ?? 'Congreso' }}</h3>
                  <p class="text-secondary mb-1">
                    Propuesta de {{ $evaluacion->usuario?->nombre_completo ?? 'Usuario eliminado' }}
                  </p>
                  <span class="badge {{ $evaluacion->evaluador_aceptado ? 'bg-green-lt' : 'bg-yellow-lt' }}">
                    {{ $evaluacion->evaluador_aceptado ? 'Asignación aceptada' : 'Pendiente de aceptación' }}
                  </span>
                  @if ($evaluacion->evaluador_aceptado)
                    <span class="badge bg-azure-lt">{{ $evaluacion->resumen_estado }}</span>
                  @endif
                </div>
                @unless ($evaluacion->evaluador_aceptado)
                  <form method="POST" action="{{ route('docente.propuestas.accept', $evaluacion) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary">Aceptar evaluación</button>
                  </form>
                @endunless
              </div>
              @if ($evaluacion->evaluador_aceptado)
                <div class="mt-3">
                  <a
                    class="btn btn-outline-primary"
                    href="{{ route('docente.propuestas.pdf', $evaluacion) }}"
                    target="_blank"
                    rel="noopener"
                  >Leer resumen (PDF)</a>
                  @if ($evaluacion->extenso_url)
                    <a
                      class="btn btn-outline-primary ms-2"
                      href="{{ route('docente.propuestas.extenso', $evaluacion) }}"
                      target="_blank"
                      rel="noopener"
                    >Leer propuesta extensa (PDF)</a>
                  @endif
                </div>

                @if ($evaluacion->comentarios->isNotEmpty())
                  <div class="mt-3">
                    <h4 class="h4">Devoluciones anteriores</h4>
                    <ul class="mb-0">
                      @foreach ($evaluacion->comentarios as $comentario)
                        <li>{{ $comentario->Comentario }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form
                  method="POST"
                  action="{{ route('docente.propuestas.comments.store', $evaluacion) }}"
                  class="mt-3"
                >
                  @csrf
                  <label for="comentario-{{ $evaluacion->id }}" class="form-label">Devolución para el autor</label>
                  <textarea
                    id="comentario-{{ $evaluacion->id }}"
                    name="comentario"
                    class="form-control"
                    rows="3"
                    maxlength="256"
                    required
                  >{{ old('comentario') }}</textarea>
                  @error('comentario')
                    <div class="text-danger mt-1" role="alert">{{ $message }}</div>
                  @enderror
                  <label for="estado-{{ $evaluacion->id }}" class="form-label mt-3">
                    {{ $evaluacion->extenso_url ? 'Estado de la propuesta extensa' : 'Estado del resumen' }}
                  </label>
                  <select
                    id="estado-{{ $evaluacion->id }}"
                    name="resumen_estado"
                    class="form-select"
                    required
                  >
                    <option value="">Selecciona un estado</option>
                    @foreach (['A corregir', 'Aceptado', 'Rechazado'] as $estado)
                      <option
                        value="{{ $estado }}"
                        @selected(old('resumen_estado', $evaluacion->resumen_estado) === $estado)
                      >{{ $estado }}</option>
                    @endforeach
                  </select>
                  @error('resumen_estado')
                    <div class="text-danger mt-1" role="alert">{{ $message }}</div>
                  @enderror
                  <button type="submit" class="btn btn-primary mt-2">Enviar comentarios</button>
                </form>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endif

    @if ($historialEvaluaciones->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header">
          <h2 class="card-title">Historial de propuestas evaluadas</h2>
        </div>
        <div class="list-group list-group-flush">
          @foreach ($historialEvaluaciones as $evaluacion)
            <div class="list-group-item">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                  <h3 class="h3 mb-1">{{ $evaluacion->congreso?->nombre ?? 'Congreso' }}</h3>
                  <p class="text-secondary mb-1">
                    Propuesta de {{ $evaluacion->usuario?->nombre_completo ?? 'Usuario eliminado' }}
                  </p>
                  <span class="badge bg-green-lt">Propuesta extensa aceptada</span>
                </div>
                <div>
                  <a
                    class="btn btn-outline-primary"
                    href="{{ route('docente.propuestas.pdf', $evaluacion) }}"
                    target="_blank"
                    rel="noopener"
                  >Leer resumen (PDF)</a>
                  <a
                    class="btn btn-outline-primary ms-2"
                    href="{{ route('docente.propuestas.extenso', $evaluacion) }}"
                    target="_blank"
                    rel="noopener"
                  >Leer propuesta extensa (PDF)</a>
                </div>
              </div>
              @if ($evaluacion->comentarios->isNotEmpty())
                <div class="mt-3">
                  <h4 class="h4">Historial de devoluciones</h4>
                  <ul class="mb-0">
                    @foreach ($evaluacion->comentarios as $comentario)
                      <li>{{ $comentario->Comentario }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <p class="text-secondary mt-3 mb-0">Evaluación finalizada. No se permiten más cambios.</p>
            </div>
          @endforeach
        </div>
      </div>
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
          Adjunta el PDF de solicitud y el resumen de la propuesta. El extenso se habilitará
          cuando recibas el primer comentario del evaluador.
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

          <fieldset
            class="mt-4"
            x-data="{
              carreras: @js($carreras->map(fn ($carrera) => [
                'area' => $carrera->area,
                'unidad' => $carrera->unidad_academica,
                'programa' => $carrera->nombre_carrera,
              ])->values()),
              areasSeleccionadas: @js(old('areas_participantes', [])),
              unidadesSeleccionadas: @js(old('unidades_participantes', [])),
              seleccionados: @js(old('programas', [])),
              get areas() {
                return [...new Set(this.carreras.map(carrera => carrera.area))];
              },
              get unidades() {
                return [...new Set(this.carreras
                  .filter(carrera => this.areasSeleccionadas.includes(carrera.area))
                  .map(carrera => carrera.unidad))];
              },
              get programas() {
                return [...new Set(this.carreras
                  .filter(carrera => this.areasSeleccionadas.includes(carrera.area)
                    && this.unidadesSeleccionadas.includes(carrera.unidad))
                  .map(carrera => carrera.programa))];
              },
              actualizarAreas() {
                this.unidadesSeleccionadas = this.unidadesSeleccionadas
                  .filter(unidad => this.unidades.includes(unidad));
                this.actualizarUnidades();
              },
              actualizarUnidades() {
                this.seleccionados = this.seleccionados
                  .filter(programa => this.programas.includes(programa));
              }
            }"
          >
            <legend class="h3">Programas participantes</legend>
            <p class="text-secondary">Selecciona una o varias áreas y unidades académicas. Después marca los programas que podrán participar.</p>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <h4 class="form-label">Áreas académicas</h4>
                <div class="row g-2">
                  <template x-for="(area, index) in areas" :key="area">
                    <div class="col-12">
                      <label class="form-check">
                        <input
                          type="checkbox"
                          name="areas_participantes[]"
                          :id="'area-participante-' + index"
                          :value="area"
                          x-model="areasSeleccionadas"
                          @change="actualizarAreas()"
                          class="form-check-input"
                        >
                        <span class="form-check-label" x-text="area"></span>
                      </label>
                    </div>
                  </template>
                </div>
              </div>
              <div class="col-md-6">
                <h4 class="form-label">Unidades académicas</h4>
                <div class="row g-2">
                  <template x-for="(unidad, index) in unidades" :key="unidad">
                    <div class="col-12">
                      <label class="form-check">
                        <input
                          type="checkbox"
                          name="unidades_participantes[]"
                          :id="'unidad-participante-' + index"
                          :value="unidad"
                          x-model="unidadesSeleccionadas"
                          @change="actualizarUnidades()"
                          class="form-check-input"
                        >
                        <span class="form-check-label" x-text="unidad"></span>
                      </label>
                    </div>
                  </template>
                  <p class="text-secondary mb-0" x-show="areasSeleccionadas.length === 0">Primero selecciona una o varias áreas.</p>
                </div>
              </div>
            </div>
            <div class="row g-2">
              <template x-for="programa in programas" :key="programa">
                <div class="col-md-6">
                  <label class="form-check">
                    <input
                      type="checkbox"
                      name="programas[]"
                      :value="programa"
                      x-model="seleccionados"
                      class="form-check-input"
                      :disabled="programas.length === 0"
                    >
                    <span class="form-check-label" x-text="programa"></span>
                  </label>
                </div>
              </template>
              <p class="text-secondary mb-0" x-show="unidadesSeleccionadas.length === 0">Selecciona una o varias unidades para ver sus programas.</p>
              <p class="text-secondary mb-0" x-show="unidadesSeleccionadas.length > 0 && programas.length === 0">No hay programas disponibles para las unidades seleccionadas.</p>
            </div>
          </fieldset>

          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label for="solicitud_pdf" class="form-label">PDF de solicitud del congreso</label>
              <input id="solicitud_pdf" name="solicitud_pdf" type="file" accept=".pdf,application/pdf" class="form-control" required>
              <div class="form-hint">PDF de hasta 10 MB.</div>
            </div>
            <div class="col-md-6">
              <label for="propuesta_pdf" class="form-label">PDF del resumen de propuesta</label>
              <input id="propuesta_pdf" name="propuesta_pdf" type="file" accept=".pdf,application/pdf" class="form-control" required>
              <div class="form-hint">Resumen de hasta 10 MB. El PDF extenso se solicitará después de recibir el primer comentario.</div>
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
              <th>Devolución del evaluador</th>
              <th>Propuesta extensa</th>
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
                <td>
                  @forelse ($congreso->propuestas->flatMap->comentarios as $comentario)
                    <p class="mb-1">{{ $comentario->Comentario }}</p>
                  @empty
                    <span class="text-secondary">Sin comentarios</span>
                  @endforelse
                </td>
                <td>
                  @forelse ($congreso->propuestas as $propuesta)
                    @if ($propuesta->extenso_url)
                      <p>
                        <a href="{{ route('docente.propuestas.extenso', $propuesta) }}" target="_blank" rel="noopener">
                          Leer propuesta extensa (PDF)
                        </a>
                      </p>
                    @endif

                    @if (! $propuesta->extenso_url && $propuesta->resumen_estado === 'Aceptado')
                      <form
                        method="POST"
                        action="{{ route('docente.propuestas.extenso.store', $propuesta) }}"
                        enctype="multipart/form-data"
                        class="mb-3"
                      >
                        @csrf
                        <label for="extenso-pdf-{{ $propuesta->id }}" class="form-label">Subir PDF extenso</label>
                        <input
                          id="extenso-pdf-{{ $propuesta->id }}"
                          name="extenso_pdf"
                          type="file"
                          accept=".pdf,application/pdf"
                          class="form-control"
                          required
                        >
                        <div class="form-hint">PDF de hasta 10 MB.</div>
                        @error('extenso_pdf')
                          <div class="text-danger mt-1" role="alert">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Enviar propuesta extensa</button>
                      </form>
                    @elseif ($propuesta->extenso_url && $propuesta->resumen_estado === 'Aceptado')
                      <span class="badge bg-green-lt">Propuesta extensa aceptada. No se permiten cambios.</span>
                    @elseif ($propuesta->extenso_url && $propuesta->resumen_estado === 'A corregir')
                      <form
                        method="POST"
                        action="{{ route('docente.propuestas.extenso.store', $propuesta) }}"
                        enctype="multipart/form-data"
                        class="mb-3"
                      >
                        @csrf
                        <label for="extenso-pdf-{{ $propuesta->id }}" class="form-label">Corregir PDF extenso</label>
                        <input
                          id="extenso-pdf-{{ $propuesta->id }}"
                          name="extenso_pdf"
                          type="file"
                          accept=".pdf,application/pdf"
                          class="form-control"
                          required
                        >
                        <div class="form-hint">PDF de hasta 10 MB.</div>
                        @error('extenso_pdf')
                          <div class="text-danger mt-1" role="alert">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Enviar corrección</button>
                      </form>
                    @elseif ($propuesta->extenso_url)
                      <span class="text-secondary">Propuesta extensa en revisión; no se puede modificar.</span>
                    @else
                      <span class="text-secondary">Disponible cuando el resumen sea aceptado.</span>
                    @endif
                  @empty
                    <span class="text-secondary">Sin propuesta</span>
                  @endforelse
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-secondary">Aún no has enviado solicitudes.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</x-layout.main>
