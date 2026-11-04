@props([
    'congress',
    'url' => '#',
    'badgeText' => null,
    'badgeColor' => 'secondary'
])

<div class="card d-flex flex-column h-100">
    <!-- Imagen Portada / Logo -->
    <a href="{{ $url }}">
        <img class="card-img-top object-cover" style="height: 160px;" 
             src="{{ $congress->logo_url ? asset('storage/' . $congress->logo_url) : asset('static/congress-photos/ciiara_2025.jpg') }}" 
             alt="{{ $congress->nombre }}" />
    </a>
    
    <div class="card-body d-flex flex-column">
        
        <!-- Badges -->
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <span class="badge bg-secondary-lt text-secondary">
                {{ $congress->eje_tematico ?? 'General' }}
            </span>

            @if($badgeText)
                <span class="badge bg-{{ $badgeColor }}-lt text-{{ $badgeColor }} ms-auto">
                    {{ $badgeText }}
                </span>
            @endif
        </div>

        <!-- Título -->
        <h3 class="card-title text-body font-semibold mb-3">
            <a href="{{ $url }}" class="text-reset">
                {{ $congress->edicion }} {{ $congress->nombre }} ({{ $congress->siglas }})
            </a>
        </h3>

        <!-- Metadatos (Sobreescribibles vía slot) -->
        <div class="mt-auto pt-3 border-top d-flex flex-column gap-2 text-secondary">
            @if(isset($metadata))
                {{ $metadata }}
            @else
                <div class="d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /></svg>
                    <span>
                        {{ \Carbon\Carbon::parse($congress->fecha_inicio)->format('d M Y') }} – 
                        {{ \Carbon\Carbon::parse($congress->fecha_fin)->format('d M Y') }}
                    </span>
                </div>
            @endif
        </div>

        <!-- Botones de Acción (Sobreescribibles vía slot) -->
        <div class="pt-3 mt-3">
            @if(isset($actions))
                {{ $actions }}
            @else
                <a href="{{ $url }}" class="btn btn-outline-primary w-100">
                    Ver detalles
                </a>
            @endif
        </div>

    </div>
</div>