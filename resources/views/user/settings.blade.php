<x-layout.main title="Mi perfil · SIGCCU">
  <div class="container-xl">
    <div class="row justify-content-center">
      <div class="col-12 col-md-12 col-lg-12">

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="card">
          @csrf
          @method('PUT')

          <div class="card-body">
            <h1 class="h2 mb-4">Ajustes de cuenta</h1>

            <!-- Imagen de perfil -->
            <div class="hr-text hr-text-start">Imagen de perfil</div>
            <div class="row align-items-center g-3 mb-3">
              <div class="col-auto">
                <span style="background-image: url(/static/avatar.jpg)" class="avatar avatar-xl"></span>
              </div>
              <div class="col-auto">
                <button type="button" class="btn">Cambiar imagen</button>
              </div>
            </div>

            <!-- Datos Personales -->
            <div class="hr-text hr-text-start">Personales</div>
            <div class="row g-3 mb-3">
              <div class="col-sm-6">
                <label class="form-label" for="nombres">Nombres</label>
                <input type="text" class="form-control @error('nombres') is-invalid @enderror" id="nombres"
                  name="nombres" value="{{ old('nombres', $usuario->nombres) }}">
                @error('nombres')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-sm-6">
                <label class="form-label" for="apellidos">Apellidos</label>
                <input type="text" class="form-control @error('apellidos') is-invalid @enderror" id="apellidos"
                  name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}">
                @error('apellidos')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <!-- Acceso -->
            <div class="hr-text hr-text-start">Acceso</div>
            <div class="mb-3">
              <label class="form-label" for="email">Correo electrónico</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email', $usuario->correo_electronico) }}" autocomplete="email">
              @error('email')
              <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Académicos -->
            <div class="hr-text hr-text-start">Académicos</div>
            <div class="mb-3">
              <label class="form-label" for="matricula">Matrícula / Número Trabajador</label>
              <input type="text" class="form-control @error('matricula') is-invalid @enderror" id="matricula"
                name="matricula" value="{{ old('matricula', $usuario->matricula_numtrabajador) }}">
              @error('matricula')
              <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row g-3 mb-3">
              <div class="col-sm-12">
                <label class="form-label">Área Académica</label>
                <select name="area" class="form-select @error('area') is-invalid @enderror">
                  @foreach ($areas as $valor => $texto)
                    <option value="{{ $valor }}" @selected(old('area', $usuario->area) === $valor)>{{ $texto }}</option>
                  @endforeach
                </select>
                @error('area')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-sm-6">
                <label class="form-label">Unidad Académica</label>
                <select name="unidad_academica" class="form-select @error('unidad_academica') is-invalid @enderror">
                  @foreach ($unidades as $unidad)
                    <option value="{{ $unidad }}" @selected(old('unidad_academica', $usuario->unidad_academica) === $unidad)>{{ $unidad }}</option>
                  @endforeach
                </select>
                @error('unidad_academica')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-sm-6">
                <label class="form-label">Programa Académico</label>
                <select name="nombre_carrera" class="form-select @error('nombre_carrera') is-invalid @enderror">
                  @foreach ($programas as $programa)
                    <option value="{{ $programa }}" @selected(old('nombre_carrera', $usuario->nombre_carrera) === $programa)>{{ $programa }}</option>
                  @endforeach
                </select>
                @error('nombre_carrera')
                <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

          </div>


          <!-- Acciones -->
          <div class="card-footer bg-transparent">
            {{-- Alerta de éxito al actualizar --}}
            @if (session('status'))
             
            <div class="alert alert-important alert-success alert-dismissible" role="alert">
                  <div class="alert-icon">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon alert-icon">
                      <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                      <path d="M12 8v4" />
                      <path d="M12 16h.01" />
                    </svg>
                  </div>
                  <div>Y{{ session('status') }}</div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                </div>
            @endif
            <div class="btn-list justify-content-end">
              <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>

          </div>

        </form>

      </div>
    </div>
  </div>
</x-layout.main>