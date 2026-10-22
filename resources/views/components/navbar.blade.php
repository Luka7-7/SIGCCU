<header class="navbar navbar-expand-md d-print-none">
  <div class="container-xl">
    <!-- BEGIN NAVBAR TOGGLER -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
      aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle primary navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- END NAVBAR TOGGLER -->

    <!-- BEGIN NAVBAR LOGO -->
    <div class="navbar-brand navbar-brand-autodark pe-0 pe-md-3">
      <x-logo />
    </div>
    <!-- END NAVBAR LOGO -->

    <!-- BEGIN NAVBAR ACTIONS -->
    <div class="navbar-nav flex-row order-md-last">

      <div class="d-none d-md-flex me-3">
        
      <!-- BEGIN USER MENU -->
      <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
          <span style="background-image: url(/static/avatar.jpg)" class="avatar avatar-sm"></span>
          <div class="d-none d-xl-block ps-2">
            <div>{{  auth()->user()->nombres }} {{ auth()->user()->apellidos }}</div>
            <div class="mt-1 small text-secondary text-capitalize">{{  auth()->user()->rol }}</div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <a class="dropdown-item"
            href="{{ route('profile') }}"><!-- Download SVG icon from http://tabler.io/icons/icon/user -->
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
              focusable="false" class="icon dropdown-item-icon">
              <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
              <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
            </svg>
            Mi perfil</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="{{ route('settings') }}">Ajustes</a>
          <a class="dropdown-item" href="{{ route('logout') }}">Cerrar sesión</a>
        </div>
      </div>
      <!-- END USER MENU -->
    </div>
    <!-- END NAVBAR ACTIONS -->
  </div>
</header>

<div class="navbar-expand-md">
  <div class="collapse navbar-collapse" id="navbar-menu">
    <div class="navbar">
      <div class="container-xl">
        <div class="row flex-column flex-md-row flex-fill align-items-center">
          <div class="col">
            <!-- BEGIN NAVBAR MENU -->
            <nav aria-label="Primary" class="nav-overflow nav-overflow-initialized" data-bs-toggle="nav-overflow">
              <!-- BEGIN NAVBAR MENU -->
              <ul class="navbar-nav">
                <!-- BEGIN NAVBAR ITEM -->
                <li class="nav-item active dropdown">
                  <a class="nav-link" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="nav-link-icon"><!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true" focusable="false" class="icon">
                        <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                        <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                      </svg></span>

                    <span class="nav-link-title">Tablero</span>
                  </a>
                </li>
              </ul>
              <!-- END NAVBAR MENU -->
            </nav>
            <!-- END NAVBAR MENU -->
          </div>

        </div>
      </div>
    </div>
  </div>
</div>