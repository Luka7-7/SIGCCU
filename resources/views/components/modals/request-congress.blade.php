<div class="modal fade"
     id="requestCongressModal"
     tabindex="-1"
     aria-labelledby="requestCongressModalLabel"
     aria-hidden="true"
     data-bs-backdrop="static"
     x-data="congressWizard()">

    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

        <form action="{{ route('docente.congresos.store') }}"
              method="POST"
              enctype="multipart/form-data"
              class="modal-content modal-content-fixed"
              x-ref="form"
              @submit.prevent="submitForm()">

            @csrf

            <!-- ================= HEADER INTACTO ================= -->
            <div class="modal-header">
                <h5 class="modal-title" id="requestCongressModalLabel">Solicitar Nuevo Congreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- FEEDBACK DE ERRORES DEVUELTOS POR LARAVEL -->
            @if ($errors->any())
                <div class="modal-body pb-0">
                    <div class="alert alert-danger mb-0">
                        <div class="font-weight-bold mb-1">Errores devueltos por el servidor:</div>
                        <ul class="mb-0 ps-3 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- CONTENEDOR CON SCROLL INTERNO PARA EL CUERPO DEL MODAL -->
            <div class="modal-body-scrollable">

                <!-- ====================================================== -->
                <!-- PASO 1: IDENTIDAD E INFORMACIÓN GENERAL               -->
                <!-- ====================================================== -->
                <div x-show="step === 1"
                     x-ref="step1"
                     x-transition:enter.opacity.duration.200ms>

                    <!-- Bloque 1: Identificación del Evento -->
                    <div class="modal-body">
                        <div class="row">
                            {{-- Nombre del Congreso --}}
                            <div class="col-lg-8 mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text"
                                       id="nombre"
                                       name="nombre"
                                       class="form-control"
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej. XI Congreso Internacional de Inteligencia Artificial" />
                            </div>

                            {{-- Siglas --}}
                            <div class="col-lg-4 mb-3">
                                <label for="siglas" class="form-label">Siglas</label>
                                <input type="text"
                                       id="siglas"
                                       name="siglas"
                                       class="form-control text-uppercase"
                                       value="{{ old('siglas') }}"
                                       placeholder="Ej. CIIARA" />
                            </div>

                            {{-- Eje Temático --}}
                            <div class="col-lg-8 mb-3">
                                <label for="eje_tematico" class="form-label">Eje Temático</label>
                                <input type="text"
                                       id="eje_tematico"
                                       name="eje_tematico"
                                       class="form-control"
                                       value="{{ old('eje_tematico') }}"
                                       placeholder="Ej. Robótica Aplicada e Innovación Social" />
                            </div>

                            {{-- Edición --}}
                            <div class="col-lg-4 mb-3">
                                <label for="edicion" class="form-label">Edición (#)</label>
                                <input type="number"
                                       id="edicion"
                                       name="edicion"
                                       class="form-control"
                                       min="1"
                                       value="{{ old('edicion', 1) }}" />
                            </div>

                            {{-- Descripción --}}
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion"
                                          name="descripcion"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Describa el objetivo, alcance y aportación del congreso...">{{ old('descripcion') }}</textarea>
                                <small class="form-hint">Sintetice el propósito académico del evento.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Bloque 2: Identidad Visual (Avatar Upload Nativo de Tabler.io) -->
                    <div class="modal-body border-top">
                        <div class="row align-items-center" style="width: 100%;">
                            <div class="col-auto">
                                <input type="file"
                                       id="imagen_congreso"
                                       name="imagen_congreso"
                                       class="visually-hidden"
                                       accept="image/jpeg,image/png,image/webp"
                                       @change="handleImagePreview($event)" />

                                <a href="javascript:void(0)"
                                   class="avatar avatar-upload avatar-xl rounded cursor-pointer"
                                   @click="document.getElementById('imagen_congreso').click()">
                                    <template x-if="!imagePreview">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon">
                                                <path d="M12 5l0 14" />
                                                <path d="M5 12l14 0" />
                                            </svg>
                                            <span class="avatar-upload-text">Cargar</span>
                                        </div>
                                    </template>
                                    <template x-if="imagePreview">
                                        <img :src="imagePreview" class="avatar-img rounded" style="width: 100%; height: 100%; object-fit: cover;" alt="Vista previa" />
                                    </template>
                                </a>
                            </div>
                            <div class="col">
                                <label class="form-label mb-1 cursor-pointer" @click="document.getElementById('imagen_congreso').click()">
                                    Imagen Principal
                                </label>
                                <div class="text-secondary text-xs" x-text="imageFileName ? imageFileName : 'Haga clic en el botón para subir la imagen oficial (JPG, PNG o WEBP)'"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ====================================================== -->
                <!-- PASO 2: FECHAS, ESTRUCTURA ACADÉMICA Y DOCUMENTACIÓN   -->
                <!-- ====================================================== -->
                <div x-show="step === 2"
                     x-ref="step2"
                     x-cloak
                     x-transition:enter.opacity.duration.200ms>

                    <!-- BLOQUE 1 (ARRIBA): FECHAS OFICIALES DEL EVENTO -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Fechas Oficiales del Evento</label>
                            <p class="text-secondary text-xs mb-0">Establezca el periodo de realización y las ventanas de registro para participantes.</p>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                <input type="date"
                                       id="fecha_inicio"
                                       name="fecha_inicio"
                                       class="form-control"
                                       value="{{ old('fecha_inicio') }}" />
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin</label>
                                <input type="date"
                                       id="fecha_fin"
                                       name="fecha_fin"
                                       class="form-control"
                                       value="{{ old('fecha_fin') }}" />
                            </div>
                            <div class="col-lg-6 mb-3 mb-lg-0">
                                <label for="registro_abre" class="form-label">Apertura de Registros</label>
                                <input type="date"
                                       id="registro_abre"
                                       name="registro_abre"
                                       class="form-control"
                                       value="{{ old('registro_abre') }}" />
                            </div>
                            <div class="col-lg-6">
                                <label for="registro_cierre" class="form-label">Cierre de Registros</label>
                                <input type="date"
                                       id="registro_cierre"
                                       name="registro_cierre"
                                       class="form-control"
                                       value="{{ old('registro_cierre') }}" />
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 2 (MEDIO): ESTRUCTURA ACADÉMICA (3 TOM SELECTS AL 100% CADA UNO / col-12) -->
                    <div class="modal-body border-top">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Estructura Académica Participante</label>
                            <p class="text-secondary text-xs mb-0">Busque y seleccione las áreas, facultades y programas asociados al congreso.</p>
                        </div>

                        <div class="row">
                            {{-- 1. Áreas Participantes (100% Ancho) --}}
                            <div class="col-12 mb-3">
                                <label for="areas_participantes" class="form-label">Áreas Participantes</label>
                                <select class="form-select w-100"
                                        id="areas_participantes"
                                        name="areas_participantes[]"
                                        multiple="true"
                                        data-placeholder="Seleccionar áreas participantes...">
                                    @foreach($carreras->pluck('area')->filter()->unique() as $area)
                                        <option value="{{ $area }}" @selected(in_array($area, old('areas_participantes', [])))>
                                            {{ $area }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Unidades Académicas (100% Ancho) --}}
                            <div class="col-12 mb-3">
                                <label for="unidades_participantes" class="form-label">Unidades Académicas</label>
                                <select class="form-select w-100"
                                        id="unidades_participantes"
                                        name="unidades_participantes[]"
                                        multiple="true"
                                        data-placeholder="Seleccionar unidades académicas...">
                                    @foreach($carreras->pluck('unidad_academica')->filter()->unique() as $unidad)
                                        <option value="{{ $unidad }}" @selected(in_array($unidad, old('unidades_participantes', [])))>
                                            {{ $unidad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Programas Educativos (100% Ancho) --}}
                            <div class="col-12 mb-3">
                                <label for="programas" class="form-label">Programas Educativos</label>
                                <select class="form-select w-100"
                                        id="programas"
                                        name="programas[]"
                                        multiple="true"
                                        data-placeholder="Seleccionar programas educativos...">
                                    @foreach($carreras->pluck('nombre_carrera')->filter()->unique() as $carrera)
                                        <option value="{{ $carrera }}" @selected(in_array($carrera, old('programas', [])))>
                                            {{ $carrera }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- BLOQUE 3 (ABAJO): DOCUMENTACIÓN ADJUNTA (SOLICITUD Y PROPUESTA AMBOS EN ESTILO AVATAR UPLOAD XL) -->
                    <div class="modal-body border-top">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Expediente Oficial (PDF)</label>
                            <p class="text-secondary text-xs mb-0">Adjunte los documentos normativos para la validación del comité.</p>
                        </div>

                        <div class="row g-3">
                            {{-- 1. Documento de Solicitud (PDF) --}}
                            <div class="col-lg-6">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <input type="file"
                                               id="solicitud_pdf"
                                               name="solicitud_pdf"
                                               class="visually-hidden"
                                               accept="application/pdf"
                                               @change="handleSolicitudPdfUpload($event)" />

                                        <a href="javascript:void(0)"
                                           class="avatar avatar-upload avatar-xl rounded cursor-pointer"
                                           @click="document.getElementById('solicitud_pdf').click()">
                                            
                                            <template x-if="!solicitudPdfName">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon">
                                                        <path d="M12 5l0 14" />
                                                        <path d="M5 12l14 0" />
                                                    </svg>
                                                    <span class="avatar-upload-text">Cargar</span>
                                                </div>
                                            </template>

                                            <template x-if="solicitudPdfName">
                                                <div class="d-flex flex-column align-items-center justify-content-center text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-1">
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                        <path d="M9 15l2 2l4 -4" />
                                                    </svg>
                                                    <span class="avatar-upload-text fw-bold" style="font-size: 0.65rem;">PDF OK</span>
                                                </div>
                                            </template>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-1 cursor-pointer" @click="document.getElementById('solicitud_pdf').click()">
                                            Documento de Solicitud (PDF)
                                        </label>
                                        <div class="text-secondary text-xs" x-text="solicitudPdfName ? solicitudPdfName : 'Oficio firmado de solicitud (Máx. 5 MB)'"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Resumen de Propuesta (PDF) --}}
                            <div class="col-lg-6">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <input type="file"
                                               id="propuesta_pdf"
                                               name="propuesta_pdf"
                                               class="visually-hidden"
                                               accept="application/pdf"
                                               @change="handlePropuestaPdfUpload($event)" />

                                        <a href="javascript:void(0)"
                                           class="avatar avatar-upload avatar-xl rounded cursor-pointer"
                                           @click="document.getElementById('propuesta_pdf').click()">
                                            
                                            <template x-if="!propuestaPdfName">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" class="icon">
                                                        <path d="M12 5l0 14" />
                                                        <path d="M5 12l14 0" />
                                                    </svg>
                                                    <span class="avatar-upload-text">Cargar</span>
                                                </div>
                                            </template>

                                            <template x-if="propuestaPdfName">
                                                <div class="d-flex flex-column align-items-center justify-content-center text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-1">
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                        <path d="M9 15l2 2l4 -4" />
                                                    </svg>
                                                    <span class="avatar-upload-text fw-bold" style="font-size: 0.65rem;">PDF OK</span>
                                                </div>
                                            </template>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <label class="form-label mb-1 cursor-pointer" @click="document.getElementById('propuesta_pdf').click()">
                                            Resumen de propuesta (PDF)
                                        </label>
                                        <div class="text-secondary text-xs" x-text="propuestaPdfName ? propuestaPdfName : 'Resumen extenso en PDF (Máx. 10 MB)'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ================= FOOTER CON BOTONES NATIVOS ALINEADOS A LA DERECHA ================= -->
            <div class="modal-footer modal-footer-fixed d-flex align-items-center">
                <!-- Botón Cancelar a la izquierda -->
                <button type="button" class="btn btn-link link-secondary me-auto mb-0" data-bs-dismiss="modal">
                    Cancelar
                </button>

                <!-- Grupo de botones de navegación juntos a la derecha -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Botón Anterior (Visible solo en Paso 2) -->
                    <button type="button"
                            class="btn btn-outline-secondary"
                            x-show="step === 2"
                            @click="prevStep()"
                            x-cloak>
                        Anterior
                    </button>

                    <!-- Botón Siguiente (Visible solo en Paso 1) -->
                    <button type="button"
                            class="btn btn-primary"
                            x-show="step === 1"
                            @click="nextStep()">
                        Siguiente
                    </button>

                    <!-- Botón Submit (Visible solo en Paso 2) -->
                    <button type="submit"
                            class="btn btn-primary"
                            x-show="step === 2"
                            :disabled="submitting"
                            x-cloak>
                        <span x-text="submitting ? 'Enviando...' : 'Terminar y solicitar'"></span>
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('congressWizard', () => ({

        step: 1,
        submitting: false,
        selectInstances: {},
        imagePreview: null,
        imageFileName: null,
        solicitudPdfName: null,
        propuestaPdfName: null,

        init() {
            const selects = ['areas_participantes', 'unidades_participantes', 'programas'];

            selects.forEach(id => {
                const element = this.$refs.form.querySelector(`#${id}`);
                
                if (element && window.TomSelect) {
                    if (element.tomselect) {
                        element.tomselect.destroy();
                    }

                    this.selectInstances[id] = new TomSelect(element, {
                        copyClassesToDropdown: false,
                        dropdownParent: 'body',
                        plugins: ['remove_button'],
                        closeAfterSelect: true
                    });
                }
            });

            const hasErrors = Boolean({{ $errors->any() ? 'true' : 'false' }});
            const errorsArray = @js($errors->keys());
            const step2Fields = [
                'areas_participantes',
                'unidades_participantes',
                'programas',
                'fecha_inicio',
                'fecha_fin',
                'registro_abre',
                'registro_cierre',
                'solicitud_pdf',
                'propuesta_pdf'
            ];

            if (errorsArray.some(field => step2Fields.includes(field))) {
                this.step = 2;
            }

            if (hasErrors) {
                this.$nextTick(() => {
                    const modalEl = document.getElementById('requestCongressModal');
                    if (modalEl && window.bootstrap) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                });
            }
        },

        handleImagePreview(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageFileName = file.name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        handleSolicitudPdfUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.solicitudPdfName = file.name;
            } else {
                this.solicitudPdfName = null;
            }
        },

        handlePropuestaPdfUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.propuestaPdfName = file.name;
            } else {
                this.propuestaPdfName = null;
            }
        },

        nextStep() {
            this.step = 2;

            this.$nextTick(() => {
                Object.values(this.selectInstances).forEach(instance => {
                    instance.close();
                    instance.refreshOptions(false);
                });
            });
        },

        prevStep() {
            Object.values(this.selectInstances).forEach(instance => {
                instance.close();
            });

            this.step = 1;
        },

        submitForm() {
            if (this.submitting) return;

            Object.values(this.selectInstances).forEach(instance => {
                instance.close();
            });

            this.submitting = true;
            this.$refs.form.submit();
        }

    }));

});
</script>