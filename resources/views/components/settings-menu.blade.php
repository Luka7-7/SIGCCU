<nav aria-label="Business settings" class="list-group list-group-transparent">
    <a href="{{ route('settings') }}"
       class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('settings') ? 'active' : '' }}">
       Personales
    </a>

    <a href="{{ route('settings.academic') }}" 
       class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('settings.academic') ? 'active' : '' }}">
       Académicos
    </a>

    <a href="{{ route('settings.general') }}" 
       class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('settings.general') ? 'active' : '' }}">
       Generales
    </a>
</nav>