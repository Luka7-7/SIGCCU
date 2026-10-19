<x-layout.guest title="Accede · SIGCCU">

        <!-- Encabezado del Logo -->
        <div class="text-center mb-4">
            <x-logo />
        </div>

    <div class="card card-md" x-data="{
            showPassword: false,
            showPasswordConfirm: false 
        }">
        <div class="card-body">
            <h1 class="h2 text-center">Acceso a la plataforma</h1>
            <form class="mt-4" method="POST" action="{{ route('login.store') }}">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <!-- Email o Matrícula -->
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Ej. usuario@uan.edu.mx" required autofocus>
                </div>

                <!-- Contraseña -->
                <div class="mb-3">
                    <label class="form-label">
                        Contraseña
                        <span class="form-label-description">
                            <a href="{{ route('fogot') }}">¿Olvidaste la contraseña?</a>
                        </span>
                    </label>
                    <div class="input-group input-group-flat">
                        <input :type="showPassword ? 'text' : 'password'" type="password" name="password" class="form-control" placeholder="Ingresa la contraseña"
                            autocomplete="off" required>
                             <span class="input-group-text">
                                <a href="#" class="input-group-link" @click.prevent="showPassword = !showPassword"
                                    x-text="showPassword ? 'Ocultar' : 'Mostrar'"></a>
                            </span>
                    </div>
                </div>

                <!-- Checkbox Recordar -->
                <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="right" title="Si activas esta opción tu cuenta permanecera abierta durante más tiempo." />
                        <span class="form-check-label">Mantener cuenta abierta</span>
                    </label>
                </div>

                <!-- Botón Iniciar Sesión -->
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                </div>
            </form>

            <div class="hr-text">¿No tienes cuenta?</div>
            <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100">Registrarse</a>
        </div>
    </div>
    </x-layout.guest>