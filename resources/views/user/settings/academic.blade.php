<x-layout.main title="Académico · SIGCCU">
  <div class="container-xl">
    <div class="card">
      <div class="row g-0">
        <!-- BEGIN SETTINGS NAV -->
        <div class="col-12 col-md-3 border-end">
          <div class="card-body">
            <h4 class="subheader">Ajustes</h4>
            <x-settings-menu />
          </div>
        </div>
        <!-- END SETTINGS NAV -->

        <div class="col-12 col-md-9 d-flex flex-column">
          <!-- Formulario de actualización académica -->
          <form action="{{ route('settings.update.academic') }}" method="POST" class="d-flex flex-column flex-fill">
            @csrf
            @method('PUT')

            <div class="card-body">
              {{-- Alerta de éxito --}}
              @if (session('status'))
                <div class="alert alert-success alert-dismissible" role="alert">
                  <div class="d-flex">
                    <div>{{ session('status') }}</div>
                  </div>
                  <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
              @endif

              <h2 class="mb-4">Información Académica</h2>

              <div class="row g-3 mb-3">
                <!-- Matrícula / No. Trabajador -->
                <div class="col-sm-6">
                  <label class="form-label" for="matricula">
                    {{ $usuario->rol === 'docente' ? 'Número de Empleado' : 'Matrícula' }}
                  </label>
                  <input type="text" 
                         class="form-control @error('matricula') is-invalid @enderror" 
                         id="matricula" 
                         name="matricula" 
                         value="{{ old('matricula', $usuario->matricula_numtrabajador) }}">
                  @error('matricula')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Área Académica -->
                <div class="col-sm-6">
                  <label class="form-label" for="area">Área Académica</label>
                  <select name="area" id="area" class="form-select @error('area') is-invalid @enderror">
                    @foreach ($areas as $valor => $texto)
                      <option value="{{ $valor }}" @selected(old('area', $usuario->area) === $valor)>{{ $texto }}</option>
                    @endforeach
                  </select>
                  @error('area')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Unidad Académica -->
                <div class="col-sm-6">
                  <label class="form-label" for="unidad_academica">Unidad Académica</label>
                  <select name="unidad_academica" id="unidad_academica" class="form-select @error('unidad_academica') is-invalid @enderror">
                    @foreach ($unidades as $unidad)
                      <option value="{{ $unidad }}" @selected(old('unidad_academica', $usuario->unidad_academica) === $unidad)>{{ $unidad }}</option>
                    @endforeach
                  </select>
                  @error('unidad_academica')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Programa Académico -->
                <div class="col-sm-6">
                  <label class="form-label" for="nombre_carrera">Programa Académico</label>
                  <select name="nombre_carrera" id="nombre_carrera" class="form-select @error('nombre_carrera') is-invalid @enderror">
                    @foreach ($programas as $programa)
                      <option value="{{ $programa }}" @selected(old('nombre_carrera', $usuario->nombre_carrera) === $programa)>{{ $programa }}</option>
                    @endforeach
                  </select>
                  @error('nombre_carrera')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Footer con el botón SUBMIT dentro del form -->
            <div class="card-footer bg-transparent mt-auto">
              <div class="btn-list justify-content-end">
                <a href="{{ route('settings.academic') }}" class="btn">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-layout.main>