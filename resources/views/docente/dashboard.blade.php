<x-layout.main title="Congresos · SIGCCU">
    <div class="container-xl">
        
        <!-- HEADER DE PÁGINA -->
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Docente</div>
                <h1 class="page-title">Mis Congresos</h1>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">

                    {{-- Botón Solicitar nuevo (Desktop) --}}
                    <button type="button" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#requestCongressModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="icon">
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                        Solicitar nuevo
                    </button>

                    {{-- Botón Solicitar nuevo (Mobile) --}}
                    <button type="button" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#requestCongressModal" aria-label="Solicitar nuevo congreso">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="icon">
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- GRID CON UNA SOLA TARJETA ESTÁTICA -->
        <div class="row row-cards mt-3">
            
            <div class="col-sm-6 col-lg-4">
                <div class="card d-flex flex-column h-100">
                    <a href="#">
                        <img class="card-img-top object-cover" style="height: 160px;" src="{{ asset('static/congress-photos/ciiara_2025.jpg') }}" alt="Portada del Congreso" />
                    </a>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-secondary-lt text-secondary">Ciencias de la Computación</span>
                            <span class="badge bg-blue-lt text-blue ms-auto">En Revisión</span>
                        </div>
                        <h3 class="card-title text-body font-semibold mb-3">
                            <a href="#" class="text-reset">XI Congreso Internacional de Inteligencia Artificial y Robótica (CIIARA 2026)</a>
                        </h3>
                        <div class="mt-auto pt-3 border-top d-flex flex-column gap-2 text-secondary">
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                                <span>12 – 14 Enero 2026</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
                                <span class="text-truncate">Centro Universitario de Convenciones</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 9l-6 6" /><path d="M10.5 15.5l-3 -3" /><path d="M13.5 8.5l3 3" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /></svg>
                                <span class="font-weight-medium text-body">2 Talleres / 4 Conferencias</span>
                            </div>
                        </div>
                        <div class="pt-3 mt-3">
                            <a href="#" class="btn btn-outline-primary w-100">Gestionar propuesta</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    {{-- Modal extraído a componente Blade --}}
    <x-modals.request-congress :carreras="$carreras" />
</x-layout.main>
