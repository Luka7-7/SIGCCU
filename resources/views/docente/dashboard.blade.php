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
            <div class="col-sm-4 col-lg-4">
                <x-congress-card />
            </div>

            <div class="col-sm-6 col-lg-4">
                <x-congress-card />
            </div>

            <div class="col-sm-6 col-lg-4">
                <x-congress-card />
            </div>

            <div class="col-sm-6 col-lg-4">
                <x-congress-card />
            </div>

            <div class="col-sm-6 col-lg-4">
                <x-congress-card />
            </div>

            <div class="col-sm-6 col-lg-4">
                <x-congress-card />
            </div>
        </div>
        
    </div>

    
    {{-- Modal extraído a componente Blade --}}
    <x-modals.request-congress :carreras="$carreras" />
</x-layout.main>
