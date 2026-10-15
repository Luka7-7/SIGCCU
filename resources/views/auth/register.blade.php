<x-layout.guest title="Crea una cuenta en - SIGCCU">


    <!-- Encabezado del Logo -->
    <div class="text-center mb-4">
        <x-logo />
    </div>

    <div class="card p-5" x-data="{ 
            step: 1, 
            perfil: '{{ old('perfil_inicial', 'estudiante') }}',
            showPassword: false,
            showPasswordConfirm: false 
        }">
        <div class="card-body px-0">
            <h1 class="text-center mb-4">Crear cuenta</h1>
            <p class="text-secondary mt-2">Tus datos están protegidos y serán tratados con confidencialidad.</p>

            <form class="mt-4" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- PASO 1: Selección de Perfil Inicial -->
                <div class="step-content" x-show="step === 1">
                    <div class="mb-3">
                        <label class="form-label">Soy un</label>
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">

                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="perfil_inicial" value="estudiante"
                                    class="form-selectgroup-input" x-model="perfil" />
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div>
                                        <span class="me-2"><i class="ti ti-school fs-2"></i></span>Estudiante
                                    </div>
                                </div>
                            </label>

                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="perfil_inicial" value="docente" class="form-selectgroup-input"
                                    x-model="perfil" />
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div>
                                        <span class="me-2"><i class="ti ti-chalkboard-teacher fs-2"></i></span>Docente
                                    </div>
                                </div>
                            </label>

                            <label class="form-selectgroup-item flex-fill">
                                <input type="radio" name="perfil_inicial" value="externo" class="form-selectgroup-input"
                                    x-model="perfil" />
                                <div class="form-selectgroup-label d-flex align-items-center p-3">
                                    <div class="me-3">
                                        <span class="form-selectgroup-check"></span>
                                    </div>
                                    <div>
                                        <span class="me-2"><i class="ti ti-user fs-2"></i></span>Externo
                                    </div>
                                </div>
                            </label>

                        </div>
                    </div>

                    <!-- Botón Continuar Paso 1 -->
                    <div class="form-footer">
                        <button type="button" class="btn btn-primary w-100 py-2 fs-3"
                            @click="step = 2">Continuar</button>
                    </div>

                    <div class="hr-text">¿Ya tienes cuenta?</div>
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">Inicia sesión</a>
                </div>

                <!-- PASO 2: Datos Personales y Contraseña -->
                <div class="step-content" x-show="step === 2" x-cloak>
                    <!-- Nombres -->
                    <div class="mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" class="form-control" name="nombres" value="{{ old('nombres') }}"
                            placeholder="Ej. Juan Carlos">
                    </div>

                    <!-- Apellidos -->
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" class="form-control" name="apellidos" value="{{ old('apellidos') }}"
                            placeholder="Ej. Pérez Ramírez">
                    </div>

                    <!-- Correo Institucional -->
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                            placeholder="usuario@uan.edu.mx">
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <div class="input-group input-group-flat">
                            <input :type="showPassword ? 'text' : 'password'" class="form-control" name="password"
                                placeholder="Mínimo 8 caracteres" autocomplete="off">
                            <span class="input-group-text">
                                <a href="#" class="input-group-link" @click.prevent="showPassword = !showPassword"
                                    x-text="showPassword ? 'Ocultar' : 'Mostrar'"></a>
                            </span>
                        </div>
                    </div>

                    <!-- Repite la contraseña -->
                    <div class="mb-3">
                        <label class="form-label">Repite la contraseña</label>
                        <div class="input-group input-group-flat">
                            <input :type="showPasswordConfirm ? 'text' : 'password'" class="form-control"
                                name="password_confirmation" placeholder="Ingresa de nuevo la contraseña"
                                autocomplete="off">
                            <span class="input-group-text">
                                <a href="#" class="input-group-link"
                                    @click.prevent="showPasswordConfirm = !showPasswordConfirm"
                                    x-text="showPasswordConfirm ? 'Ocultar' : 'Mostrar'"></a>
                            </span>
                        </div>
                    </div>

                    <!-- Botones Continuar Paso 2 -->
                    <div class="form-footer d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 py-2 fs-3"
                            @click="step = 1">Anterior</button>
                        <button type="button" class="btn btn-primary w-50 py-2 fs-3"
                            @click="step = 3">Continuar</button>
                    </div>
                </div>

                <!-- PASO 3: Datos Académicos -->
                <div class="step-content" x-show="step === 3" x-cloak>

                    <!-- Campo Condicional: Matrícula (Estudiante) -->
                    <div class="mb-3" x-show="perfil === 'estudiante'">
                        <label class="form-label">Matrícula</label>
                        <input type="text" class="form-control" name="matricula" value="{{ old('matricula') }}"
                            placeholder="Ej. 18004321" :disabled="perfil !== 'estudiante'">
                    </div>

                    <!-- Campo Condicional: Número de empleado (Docente) -->
                    <div class="mb-3" x-show="perfil === 'docente'">
                        <label class="form-label">Número de Empleado</label>
                        <input type="text" class="form-control" name="numero_empleado"
                            value="{{ old('numero_empleado') }}" placeholder="Ej. EMP-98210"
                            :disabled="perfil !== 'docente'">
                    </div>

                    <!-- Área Académica -->
                    <div class="mb-3">
                        <label class="form-label">Área Académica</label>
                        <select class="form-select w-100" name="area_academica">
                            <option value="" disabled selected>Selecciona tu área académica</option>
                            <option value="CB">Área de Ciencias Básicas e Ingenierías</option>
                            <option value="CS">Área de Ciencias de la Salud</option>
                            <option value="EA">Área de Económico Administrativas</option>
                            <option value="SH">Área de Ciencias Sociales y Humanidades</option>
                        </select>
                    </div>

                    <!-- Unidad Académica -->
                    <div class="mb-3">
                        <label class="form-label">Unidad Académica</label>
                        <select class="form-select w-100" name="unidad_academica">
                            <option value="" disabled selected>Selecciona tu unidad académica</option>
                            <option value="02">Unidad Académica de Ciencias e Ingenierías</option>
                        </select>
                    </div>

                    <!-- Programa Académico -->
                    <div class="mb-3">
                        <label class="form-label">Programa Académico</label>
                        <select class="form-select w-100" name="programa_academico">
                            <option value="" disabled selected>Selecciona tu programa académico</option>
                            <option value="03">Ingeniería en Computación</option>
                        </select>
                    </div>

                    <!-- Botones Anterior y Crear cuenta -->
                    <div class="form-footer d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary w-50 py-2 fs-3"
                            @click="step = 2">Anterior</button>
                        <button type="submit" class="btn btn-primary w-50 py-2 fs-3">Crear cuenta</button>
                    </div>
                </div>

            </form>

        </div>
    </div>

    </x-layout.guest>