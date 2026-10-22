<x-layout.main title="Personal · SIGCCU">
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
                    <!-- Apertura del Formulario -->
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-fill">
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

                            <h2 class="mb-4">Información personal</h2>

                            <h2 class="card-title">Imagen de perfil</h2>
                            <div class="row align-items-center mb-4">
                                <div class="col-auto">
                                    <span style="background-image: url(/static/avatar.jpg)" class="avatar avatar-xl"></span>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn">Cambiar imagen</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-ghost-danger">Eliminar imagen</button>
                                </div>
                            </div>

                            <h2 class="card-title">Datos</h2>
                            <div class="row g-3 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label required" for="nombres">Nombres</label>
                                    <input type="text" 
                                           class="form-control @error('nombres') is-invalid @enderror" 
                                           id="nombres"
                                           name="nombres" 
                                           value="{{ old('nombres', $usuario->nombres) }}">
                                    @error('nombres')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label required" for="apellidos">Apellidos</label>
                                    <input type="text" 
                                           class="form-control @error('apellidos') is-invalid @enderror" 
                                           id="apellidos"
                                           name="apellidos" 
                                           value="{{ old('apellidos', $usuario->apellidos) }}">
                                    @error('apellidos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h2 class="card-title mt-4">Correo electrónico</h2>
                            <p class="card-subtitle">Este es el correo con el que accedes a la plataforma.</p>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label visually-hidden" for="email">Email</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $usuario->correo_electronico) }}" 
                                           autocomplete="email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- El Footer con el botón SUBMIT debe quedar DENTRO del <form> -->
                        <div class="card-footer bg-transparent mt-auto">
                            <div class="btn-list justify-content-end">
                                <a href="{{ route('settings') }}" class="btn">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.main>