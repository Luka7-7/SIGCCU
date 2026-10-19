<aside class="navbar navbar-vertical navbar-expand-lg">
  <div class="container-fluid">
    <!-- BEGIN NAVBAR TOGGLER -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
      aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle sidebar navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!-- END NAVBAR TOGGLER -->

    <!-- BEGIN NAVBAR LOGO -->
    <div class="navbar-brand navbar-brand-autodark">
         <x-logo height="100px" />
      <button type="button" class="btn btn-action btn-sm d-none d-lg-inline-flex" data-bs-toggle="sidebar-folded"
        aria-pressed="false" aria-label="Pin sidebar">
        <!-- Download SVG icon from http://tabler.io/icons/icon/pin -->
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
          focusable="false" class="icon">
          <path d="M15 4.5l-4 4l-4 1.5l-1.5 1.5l7 7l1.5 -1.5l1.5 -4l4 -4"></path>
          <path d="M9 15l-4.5 4.5"></path>
          <path d="M14.5 4l5.5 5.5"></path>
        </svg>
      </button>
    </div>
    <!-- END NAVBAR LOGO -->

    <!-- BEGIN SIDEBAR USER -->
    <div class="navbar-footer">
      <ul class="navbar-nav">
        <li class="nav-item dropup">
          <a href="#" class="nav-link" data-bs-toggle="dropdown" aria-label="Open user menu">
            <span style="background-image: url(/static/avatar.jpg)" class="avatar avatar-sm"></span>
            <div class="nav-link-title">
              {{ auth()->user()->nombres }} {{ auth()->user()->apellidos }}
              <div class="small text-secondary" style="text-transform: capitalize">{{ auth()->user()->rol }}</div>
            </div>
          </a>
          <div class="dropdown-menu">
            <a class="dropdown-item"
              href="{{ route('profile') }}"><!-- Download SVG icon from http://tabler.io/icons/icon/user -->
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                focusable="false" class="icon dropdown-item-icon">
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
              </svg>
              Mi perfil</a>
            <a class="dropdown-item"
              href="{{ route('settings') }}"><!-- Download SVG icon from http://tabler.io/icons/icon/user -->
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065" />
                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
              </svg>
              Ajustes</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="{{ route('login') }}">Cerrar sesión</a>
          </div>
        </li>
      </ul>
    </div>
    <!-- END SIDEBAR USER -->

    <nav class="navbar-collapse collapse" id="sidebar-menu" aria-label="Sidebar">
      <!-- BEGIN NAVBAR MENU -->
      <ul class="navbar-nav pt-lg-3">
        <!-- END NAVBAR ITEM -->
        <li class="nav-section-title">Menú</li>
        <!-- BEGIN NAVBAR ITEM -->
        <li class="nav-item">
          <a class="nav-link" href="./">
            <span class="nav-link-title"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-arch"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 21l18 0" /><path d="M4 21v-15a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v15" /><path d="M9 21v-8a3 3 0 0 1 6 0v8" /></svg> Congresos</span>
          </a>
        </li>
      </ul>
      <!-- END NAVBAR MENU -->

      <div class="navbar-side">
        <ul class="navbar-nav">
        </ul>
      </div>
    </nav>
  </div>
</aside>