<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Leaflet for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- FullCalendar -->
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css' rel='stylesheet'>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.js'></script>

    <script>
        const token = 'Bearer ' + localStorage.getItem('token');
        axios.defaults.headers.common['Authorization'] = token;
    </script>
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 60px;
            --primary-color: #6366f1;
            --secondary-bg: #f8fafc;
        }

        body {
            min-height: 100vh;
            background-color: var(--secondary-bg);
        }

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            background-color: #fff;
            border-right: 1px solid #e5e7eb;
            transition: all 0.3s ease-in-out;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s ease-in-out;
        }

        .top-header {
            height: var(--header-height);
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #64748b;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.2s;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color);
            background-color: #eef2ff;
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
        }

        .task-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding: 20px 0;
        }

        .task-column {
            min-width: 300px;
            background: #fff;
            border-radius: 12px;
            padding: 16px;
        }

        .task-card {
            background: #fff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            transition: all 0.2s;
        }

        .task-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .status-badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .timeline {
            position: relative;
            padding-left: 32px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -32px;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #e5e7eb;
        }

        .timeline-item::after {
            content: '';
            position: absolute;
            left: -37px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-open .sidebar {
                transform: translateX(0);
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="p-4">
                <div class="d-flex align-items-center mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" height="40">
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users') }}"
                            class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.tasks') }}"
                            class="nav-link {{ request()->routeIs('admin.tasks*') ? 'active' : '' }}">
                            <i class="bi bi-list-task"></i> Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.leads') }}"
                            class="nav-link {{ request()->routeIs('admin.leads*') ? 'active' : '' }}">
                            <i class="bi bi-person-lines-fill"></i> Leads
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.sales') }}"
                            class="nav-link {{ request()->routeIs('admin.sales*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up"></i> Sales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.attendance') }}"
                            class="nav-link {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.locations') }}"
                            class="nav-link {{ request()->routeIs('admin.locations*') ? 'active' : '' }}">
                            <i class="bi bi-geo-alt"></i> Locations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-file-earmark-text"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header mb-4">
                <button class="btn btn-link sidebar-toggler d-md-none">
                    <i class="bi bi-list"></i>
                </button>

                <div class="flex-grow-1"></div>

                <div class="d-flex align-items-center">
                    <!-- Search -->
                    <div class="me-3">
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-0 bg-light" placeholder="Search...">
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-link position-relative" type="button" id="notificationsDropdown"
                            data-bs-toggle="dropdown">
                            <i class="bi bi-bell text-muted"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                            <li>
                                <h6 class="dropdown-header">Notifications</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">New lead assigned</a></li>
                            <li><a class="dropdown-item" href="#">Sales target achieved</a></li>
                            <li><a class="dropdown-item" href="#">New user registered</a></li>
                        </ul>
                    </div>

                    <!-- Profile -->
                    <div class="dropdown">
                        <button class="btn btn-link d-flex align-items-center" type="button" id="profileDropdown"
                            data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->photo_url ?? asset('images/default-avatar.png') }}"
                                alt="Profile" class="rounded-circle me-2" width="32" height="32">
                            <span class="d-none d-md-block text-dark">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings') }}">Settings</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            @yield('content')
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebarToggler = document.querySelector('.sidebar-toggler');
            const wrapper = document.querySelector('.wrapper');

            sidebarToggler?.addEventListener('click', function() {
                wrapper.classList.toggle('sidebar-open');
            });

            // API Token Check
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            // Add token to all API requests
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')
                .content;

            // Handle API Errors
            axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response?.status === 401) {
                        localStorage.removeItem('token');
                        window.location.href = '/login';
                    }
                    return Promise.reject(error);
                }
            );
        });

        // Show loading spinner
        function showLoading() {
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Hide loading spinner
        function hideLoading() {
            Swal.close();
        }

        // Show toast notification
        function showToast(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            Toast.fire({
                icon: icon,
                title: title
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
