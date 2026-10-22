@props([ 
    'type' => 'vertical',
    'veticalHeight' => '100px',
    'horizontalHeight' => '100px',
    ])

@if($type !== 'vertical')
    <a href="/">
        <img 
            src="{{ asset('static/logo.png') }}" 
            alt="Logo SIGCCU" 
            class="navbar-brand-image" 
            style="height: 100px"
        >
    </a>
@else
    <a href="/">
            <img 
                src="{{ asset('static/logo_horizontal.png') }}" 
                alt="Logo SIGCCU" 
                class="navbar-brand-image" 
                style="height: 60px"
            >
        </a>
@endif