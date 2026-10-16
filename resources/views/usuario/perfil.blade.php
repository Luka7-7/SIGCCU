{{-- Ubicación: resources/views/usuario/perfil.blade.php --}}
{{-- Controlador: return view('usuario.perfil', [...]) --}}
<x-layout.guest title="Mi perfil · SIGCCU">
    @php
        $iniciales = mb_strtoupper(mb_substr($usuario->nombres, 0, 1) . mb_substr($usuario->apellidos, 0, 1));
        $pideIdentificador = in_array($usuario->rol, ['alumno', 'docente'], true);
        $etiquetaIdentificador = $usuario->rol === 'docente' ? 'Número de empleado' : 'Matrícula';

        // Si hubo error en el cambio de contraseña, abre esa pestaña
        $pestanaInicial = $errors->hasAny(['current_password', 'password']) ? 'seguridad' : 'datos';

        $colorEstado = fn ($estado) => match ($estado) {
            'Aceptado'    => 'bg-green-lt',
            'Rechazado'   => 'bg-red-lt',
            'A corregir'  => 'bg-orange-lt',
            'En revision' => 'bg-azure-lt',
            default       => 'bg-secondary-lt',
        };
    @endphp

    <div class="container-xl py-4"
        x-data="{ tab: '{{ $pestanaInicial }}', showActual: false, showNueva: false, showConfirm: false }">

        @if (session('status'))
            <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        {{-- ============ ENCABEZADO ============ --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <span class="avatar avatar-xl bg-primary-lt">{{ $iniciales }}</span>
                    </div>
                    <div class="col">
                        <h1 class="h2 mb-1">{{ $usuario->nombres }} {{ $usuario->apellidos }}</h1>
                        <div class="text-secondary">{{ $usuario->correo_electronico }}</div>
                        <div class="mt-2">
                            <span class="badge bg-blue-lt">{{ $roles[$usuario->rol] ?? ucfirst($usuario->rol) }}</span>
                            <span class="text-secondary ms-2">{{ $usuario->institucion }}</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto">
                        <div class="row text-center g-3">
                            <div class="col">
                                <div class="h2 mb-0">{{ $inscripciones->count() }}</div>
                                <div class="text-secondary small">Congresos</div>
                            </div>
                            <div class="col">
                                <div class="h2 mb-0">{{ $propuestas->count() }}</div>
                                <div class="text-secondary small">Propuestas</div>
                            </div>
                            <div class="col">
                                <div class="h2 mb-0">{{ $totalConstancias }}</div>
                                <div class="text-secondary small">Constancias</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PESTAÑAS ============ --}}
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="#" class="nav-link" :class="{ 'active': tab === 'datos' }"
                            @click.prevent="tab = 'datos'">Mis datos</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" :class="{ 'active': tab === 'seguridad' }"
                            @click.prevent="tab = 'seguridad'">Contraseña</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link" :class="{ 'active': tab === 'actividad' }"
                            @click.prevent="tab = 'actividad'">Mi actividad</a>
                    </li>
                </ul>
            </div>

            {{-- ---------- Pestaña: Mis datos ---------- --}}
            <div x-show="tab === 'datos'">
                <form action="{{ route('perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <h3 class="card-title mb-3">Datos personales</h3>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombres</label>
                                <input type="text" name="nombres" value="{{ old('nombres', $usuario->nombres) }}"
                                    class="form-control @error('nombres') is-invalid @enderror">
                                @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $usuario->apellidos) }}"
                                    class="form-control @error('apellidos') is-invalid @enderror">
                                @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="{{ $pideIdentificador ? 'col-md-8' : 'col-12' }} mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email" value="{{ old('email', $usuario->correo_electronico) }}"
                                    class="form-control @error('email') is-invalid @enderror">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            @if ($pideIdentificador)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $etiquetaIdentificador }}</label>
                                    <input type="text" name="matricula_numtrabajador" maxlength="8"
                                        value="{{ old('matricula_numtrabajador', $usuario->matricula_numtrabajador) }}"
                                        class="form-control @error('matricula_numtrabajador') is-invalid @enderror">
                                    @error('matricula_numtrabajador') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card-body border-top">
                        <h3 class="card-title mb-3">Datos académicos</h3>

                        <div class="mb-3">
                            <label class="form-label">Área académica</label>
                            <select name="area" class="form-select @error('area') is-invalid @enderror">
                                @foreach ($areas as $valor => $texto)
                                    <option value="{{ $valor }}" @selected(old('area', $usuario->area) === $valor)>{{ $texto }}</option>
                                @endforeach
                            </select>
                            @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unidad académica</label>
                                <select name="unidad_academica" class="form-select @error('unidad_academica') is-invalid @enderror">
                                    @foreach ($unidades as $unidad)
                                        <option value="{{ $unidad }}" @selected(old('unidad_academica', $usuario->unidad_academica) === $unidad)>{{ $unidad }}</option>
                                    @endforeach
                                </select>
                                @error('unidad_academica') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Programa académico</label>
                                <select name="nombre_carrera" class="form-select @error('nombre_carrera') is-invalid @enderror">
                                    @foreach ($programas as $programa)
                                        <option value="{{ $programa }}" @selected(old('nombre_carrera', $usuario->nombre_carrera) === $programa)>{{ $programa }}</option>
                                    @endforeach
                                </select>
                                @error('nombre_carrera') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>

            {{-- ---------- Pestaña: Contraseña ---------- --}}
            <div x-show="tab === 'seguridad'" x-cloak>
                <form action="{{ route('perfil.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <h3 class="card-title mb-3">Cambiar contraseña</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contraseña actual</label>
                                    <div class="input-group input-group-flat">
                                        <input :type="showActual ? 'text' : 'password'" name="current_password"
                                            class="form-control @error('current_password') is-invalid @enderror"
                                            autocomplete="current-password">
                                        <span class="input-group-text">
                                            <a href="#" class="input-group-link" @click.prevent="showActual = !showActual"
                                                x-text="showActual ? 'Ocultar' : 'Mostrar'"></a>
                                        </span>
                                    </div>
                                    @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contraseña nueva</label>
                                    <div class="input-group input-group-flat">
                                        <input :type="showNueva ? 'text' : 'password'" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Mínimo 8 caracteres" autocomplete="new-password">
                                        <span class="input-group-text">
                                            <a href="#" class="input-group-link" @click.prevent="showNueva = !showNueva"
                                                x-text="showNueva ? 'Ocultar' : 'Mostrar'"></a>
                                        </span>
                                    </div>
                                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Repite la contraseña nueva</label>
                                    <div class="input-group input-group-flat">
                                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                                            class="form-control" autocomplete="new-password">
                                        <span class="input-group-text">
                                            <a href="#" class="input-group-link" @click.prevent="showConfirm = !showConfirm"
                                                x-text="showConfirm ? 'Ocultar' : 'Mostrar'"></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                    </div>
                </form>
            </div>

            {{-- ---------- Pestaña: Mi actividad ---------- --}}
            <div x-show="tab === 'actividad'" x-cloak>
                <div class="card-body">
                    <div class="row g-4">

                        {{-- Congresos --}}
                        <div class="col-lg-6">
                            <h3 class="card-title mb-3">Mis congresos</h3>
                            <div class="list-group">
                                @forelse ($inscripciones as $inscripcion)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold">{{ $inscripcion->siglas }} {{ $inscripcion->edicion }}</div>
                                                <div class="text-secondary small">{{ $inscripcion->nombre }}</div>
                                            </div>
                                            @if ($inscripcion->aceptado)
                                                <span class="badge bg-green-lt">Aceptado</span>
                                            @else
                                                <span class="badge bg-yellow-lt">Pendiente</span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small mt-1">
                                            @if ($inscripcion->rol_congreso)
                                                {{ ucfirst($inscripcion->rol_congreso) }} ·
                                            @endif
                                            Inscrito el {{ date('d/m/Y', strtotime($inscripcion->fecha_hora_inscripcion)) }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="list-group-item text-secondary">Aún no te has inscrito a ningún congreso.</div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Propuestas --}}
                        <div class="col-lg-6">
                            <h3 class="card-title mb-3">Mis propuestas</h3>
                            <div class="list-group">
                                @forelse ($propuestas as $propuesta)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Propuesta #{{ $propuesta->id }} · {{ $propuesta->siglas }} {{ $propuesta->edicion }}</span>
                                        <span class="badge {{ $colorEstado($propuesta->resumen_estado) }}">{{ $propuesta->resumen_estado }}</span>
                                    </div>
                                @empty
                                    <div class="list-group-item text-secondary">Todavía no has enviado propuestas.</div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layout.guest>