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
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M9 10a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
              </svg>
              Mi perfil</a>
            @if (auth()->user()->rol === 'administrador')
              <a class="dropdown-item" href="{{ route('administracion.congresos.index') }}">Revisar congresos</a>
            @endif
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('settings') }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path
                  d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" />
                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
              </svg>
              Ajustes</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                  <path d="M9 12h12l-3 -3" />
                  <path d="M18 15l3 -3" />
                </svg>
                Cerrar sesión</button>
            </form>
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
                <li class="nav-item">
                  <a class="nav-link"
                    href="{{ auth()->user()->rol === 'administrador' ? route('administracion.congresos.index') : (auth()->user()->rol === 'docente' ? route('docente.dashboard') : route('profile')) }}"
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
                <!-- BEGIN NAVBAR ITEM -->

                <li class="nav-item {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}">
                  <a class="nav-link"
                    href="{{ auth()->user()->rol === 'administrador' ? route('administracion.congresos.index') : (auth()->user()->rol === 'docente' ? route('docente.dashboard') : route('profile')) }}"
                    role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="nav-link-icon">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-building-bank">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 21l18 0" />
                        <path d="M3 10l18 0" />
                        <path d="M5 6l7 -3l7 3" />
                        <path d="M4 10l0 11" />
                        <path d="M20 10l0 11" />
                        <path d="M8 14l0 3" />
                        <path d="M12 14l0 3" />
                        <path d="M16 14l0 3" />
                      </svg>
                    </span>
                    <span class="nav-link-title">Mis Congresos</span>
                  </a>
                </li>
                @if (in_array(auth()->user()->rol, ['alumno', 'docente'], true))
                  <li class="nav-item {{ request()->routeIs('congresos.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('congresos.index') }}">
                      <span class="nav-link-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                          class="icon icon-tabler icons-tabler-outline icon-tabler-world-map">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                          <path d="M20 8h-2a2 2 0 0 0 -2 2a2 2 0 1 1 -4 0v-1a2 2 0 0 0 -2 -2h-1a2 2 0 0 1 -2 -2v-.5" />
                          <path d="M3 12h3a2 2 0 0 1 2 2v.5a1.5 1.5 0 0 0 1.5 1.5a1.5 1.5 0 0 1 1.5 1.5v3.25" />
                          <path d="M15 20.5v-3.5a2 2 0 0 1 2 -2h3.5" />
                          <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        </svg>
                      </span>
                      <span class="nav-link-title">Explorar</span>
                    </a>
                  </li>
                @endif
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