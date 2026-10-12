@props(['height' => '100px'])

    <a href="/">
        <img 
            src="{{ asset('static/logo.png') }}" 
            alt="Logo SIGCCU" 
            class="navbar-brand-image" 
            style="height: {{ $height }};"
        >
    </a>