<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Salesperson Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/salesperson.css') }}">
</head>

<body class="bg-light">
    <div class="min-vh-100">
        <!-- Modern Sidebar -->
        <aside id="sidebar" class="position-fixed top-0 start-0 bg-white shadow-lg h-100 d-flex flex-column"
            style="width: 260px; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-right: 2px solid rgba(0,0,0,0.1);">

            <!-- Logo Section -->
            <div class="text-center border-bottom py-4">
                <h1 class="h5 fw-bold text-primary">CRM System</h1>
            </div>

            <!-- Navigation Links -->
            <div class="p-3 flex-grow-1 overflow-auto">
                <ul class="list-unstyled">
                    <a href="{{ route('salesperson.dashboard') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('salesperson.leads') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.leads') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Leads</span>
                    </a>
                    <a href="{{ route('salesperson.sales') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.sales') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Sales</span>
                    </a>
                    <a href="{{ route('salesperson.meetings') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.meetings') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Meetings</span>
                    </a>
                    <a href="{{ route('salesperson.plans') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.plans') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Plans</span>
                    </a>
                    <a href="{{ route('salesperson.performance') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.performance') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Performance</span>
                    </a>
                    <a href="{{ route('salesperson.attendance') }}"
                        class="sidebar-link {{ Request::routeIs('salesperson.attendance') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i>
                        <span>Attendance</span>
                    </a>

                </ul>
            </div>

            <!-- Profile Section -->
            <div class="p-3 border-top d-flex align-items-center">
                <img class="rounded-circle shadow-sm" src="{{ Auth::user()->photo_url }}"
                    alt="{{ Auth::user()->name }}" style="width: 45px; height: 45px; object-fit: cover;">
                <div class="ms-3">
                    <p class="mb-0 fw-bold text-dark">{{ Auth::user()->name }}</p>
                    <p class="mb-0 small text-muted">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <div>
            <div id="main-content">
                <!-- Top Navigation -->
                <header class="bg-white shadow position-relative mx-4">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <h2 class="h5 text-dark d-flex align-items-center">
                            <button id="sidebarToggle" class="btn btn-primary me-3">
                                <i class="fas fa-bars"></i>
                            </button>
                            @yield('title', 'Dashboard')
                        </h2>
                        <div class="d-flex align-items-center">
                            <!-- Notifications -->
                            <div class="position-relative me-3">
                                <button class="btn text-dark">
                                    <i class="fas fa-bell fa-lg"></i>
                                </button>
                            </div>
                            <!-- Profile Dropdown -->
                            <div class="dropdown">
                                <button class="btn text-dark dropdown-toggle" type="button" id="profileDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img class="rounded-circle" src="{{ Auth::user()->photo_url }}"
                                        alt="{{ Auth::user()->name }}" style="width: 40px; height: 40px;">
                                    <span class="ms-2">{{ Auth::user()->name }}</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                                    <li><a class="dropdown-item" href="#">Profile</a></li>
                                    <li><a class="dropdown-item" href="#">Settings</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>


                <!-- Page Content -->
                <main class="p-4">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.body.classList.toggle('collapsed');
        });

        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.style.transform === 'translateX(-250px)') {
                sidebar.style.transform = 'translateX(0)';
            } else {
                sidebar.style.transform = 'translateX(-250px)';
            }
        });
    </script>
   
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="{{ asset('js/salespersoncalendar.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    @stack('scripts')
</body>

</html>
