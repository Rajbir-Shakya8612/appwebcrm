<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

</head>

<body class="bg-light text-dark d-flex flex-column justify-content-center min-vh-100 p-3">

    <header class="w-100 text-center mb-4">
        @if (Route::has('login'))
            <nav class="d-flex justify-content-end gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-dark">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-dark">Register</a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <div class="d-flex justify-content-center w-100">
        <main class="container text-center">
            welcome page
        </main>
    </div>

    @if (Route::has('login'))
        <div class="mt-5 d-none d-lg-block"></div>
    @endif

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
