<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

  <title>{{ $title ?? 'Dashboard' }}</title>

  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
  <x-navbar />
  <div class="page">
    <div class="page-wrapper">
      <main class="page-body">
        {{ $slot }}
      </main>
    </div>
  </div>
</body>

</html>