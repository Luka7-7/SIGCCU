<x-layout.guest title="Página no encontrada · SIGCCU">

 <div class="text-center mb-4">
 <img src="{{ asset('static/404.png') }}" alt="" width="250">
  <h1 class="empty-title">Nos hemos salido del mapa...</h1>
  <p class="empty-subtitle text-secondary mb-3 mt-1">No te preocupes te ayudaremos a encontrar el camino</p>
  <a href="{{ route('login') }}" class="btn btn-primary">
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left">
   <path stroke="none" d="M0 0h24v24H0z" fill="none" />
   <path d="M15 6l-6 6l6 6" />
  </svg>Volver a la plataforma
  </a>
 </div>
</x-layout.guest>