<x-layout.guest title="Accede · SIGCCU">

        <!-- Encabezado del Logo -->
        <div class="text-center mb-4">
            <x-logo />
        </div>

    <div class="card p-5">
        <div class="card-body px-0">
            <h1 class="text-center mb-4">Acceso a la plataforma</h1>

            <form class="mt-4" method="POST" action="{{ route('login.store') }}">
                @csrf
                <!-- Email o Matrícula -->
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="Ej. usuario@uan.edu.mx">
                </div>

                <!-- Contraseña -->
                <div class="mb-3">
                    <label class="form-label">
                        Contraseña
                        <span class="form-label-description">
                            <a href="#">¿Olvidaste la contraseña?</a>
                        </span>
                    </label>
                    <div class="input-group input-group-flat">
                        <input type="password" name="password" class="form-control" placeholder="Ingresa la contraseña"
                            autocomplete="off">
                        <span class="input-group-text">
                            <a href="#" class="link-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                    <path
                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                </svg>
                            </a>
                        </span>
                    </div>
                </div>

                <!-- Checkbox Recordar -->
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" />
                        <span class="form-check-label">Mantener cuenta abierta</span>
                    </label>
                </div>

                <!-- Botón Iniciar Sesión -->
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100 py-2 fs-3">Iniciar sesión</button>
                </div>
            </form>

            <div class="hr-text">¿No tienes cuenta?</div>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100">Regístrate</a>
        </div>
    </div>
    </x-layout.guest>