<x-layout.guest title="Página no encontrada · SIGCCU">

 <div class="text-center mb-4">
 <img src="{{ asset('static/404.png') }}" alt="" width="250">
  <h1 class="empty-title">Nos hemos salido del mapa...</h1>
  <p class="empty-subtitle text-secondary mb-3 mt-1">No te preocupes te ayudaremos a encontrar el camino</p>
  <a href="{{ route('login') }}" class="btn btn-primary">
  <span class="ti ti-chevron-left me-2" role="status"></span> Volver a la plataforma
  </a>
 </div>
</x-layout.guest>