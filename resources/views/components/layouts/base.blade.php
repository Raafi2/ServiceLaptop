<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Service Laptop' }}</title>
    <meta name="description" content="Servis laptop tercatat rapi, statusnya bisa dicek kapan saja.">
    <link rel="icon" href="/LogoServiceLaptop.png" type="image/png">
    
    <link rel="stylesheet" href="/css/app.css">
    @stack('styles')
</head>
<body>
    {{ $slot }}

    <div id="ov"></div>
    <div id="toast" role="status" aria-live="polite"></div>

    <script src="/js/app.js"></script>
    <script>
        @if(session('success'))
            showToast("{{ session('success') }}");
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}");
        @endif
        @if($errors->any())
            showToast("{{ $errors->first() }}");
        @endif
    </script>
    @stack('scripts')
</body>
</html>
