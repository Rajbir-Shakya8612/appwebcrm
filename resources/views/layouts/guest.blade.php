<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PlyVista') }}</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="robots" content="index, follow">
    <meta name="author" content="">
    <link rel="icon" href=""  type="image/x-icon">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />


   
</head>

<body class="text-dark">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center pt-6 bg-light">
        <div class="mb-4">
            <a href="/">
                <x-application-logo class="w-20 h-20 text-muted" />
            </a>
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-10 col-sm-12 mt-6">
                    <div class="card shadow-sm rounded">
                        <div class="card-body p-4">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
