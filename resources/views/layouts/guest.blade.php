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
    <link rel="icon" href="" type="image/x-icon">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


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
                {{-- <div class="text-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" height="60">
                    <h4 class="mt-3">Welcome Back</h4>
                    <p class="text-muted">Please login to your account</p>
                </div> --}}
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
