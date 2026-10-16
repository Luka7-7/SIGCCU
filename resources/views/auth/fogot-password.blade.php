<x-layout.guest title="Recuperar mi cuenta · SIGCCU">

        <!-- Encabezado del Logo -->
        <div class="text-center mb-4">
            <x-logo />
        </div>

    <div class="card card-md">
        <div class="card-body">
            <h1 class="h2 text-center mb-4">Recuperar cuenta</h1>
             <p class="text-secondary mt-2">Para recuperar tu contraseña necesitaremos que ingreses el correo electrónico con el que te registraste en la plataforma.</p>
            <form class="mt-4" method="POST" action="">
                @csrf
                <!-- Email o Matrícula -->
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="Ej. usuario@uan.edu.mx">
                </div>

                <!-- Botón Enviar correo -->
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                   <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                   <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" />
                   <path d="M3 7l9 6l9 -6" />
                  </svg> Envíame el correo</button>

                  <div class="hr-text">¿La recordaste?</div>
                  <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">Inicia sesión</a>
                </div>
            </form>
    </div>
</x-layout.guest>