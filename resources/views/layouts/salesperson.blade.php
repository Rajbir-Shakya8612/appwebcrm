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
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/dragula@3.7.3/dist/dragula.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
       <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 bg-white shadow-lg max-h-screen w-64">
            <div class="flex flex-col justify-between h-full">
                <div class="flex-grow">
                    <div class="px-4 py-6 text-center border-b">
                        <h1 class="text-xl font-bold leading-none"><span class="text-blue-700">CRM</span> System</h1>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('salesperson.dashboard') }}" class="flex items-center bg-blue-100 rounded-xl font-bold text-sm text-blue-900 py-3 px-4">
                                    <i class="fas fa-home mr-3"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.leads.index') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-users mr-3"></i> Leads
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.sales') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-chart-line mr-3"></i> Sales
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.meetings') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-calendar-alt mr-3"></i> Meetings
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.plans') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-tasks mr-3"></i> Plans
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.tasks.index') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-tasks mr-3"></i> Tasks
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.performance') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-chart-bar mr-3"></i> Performance
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('salesperson.attendance') }}" class="flex bg-white hover:bg-blue-100 rounded-xl font-bold text-sm text-gray-900 py-3 px-4">
                                    <i class="fas fa-clock mr-3"></i> Attendance
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->name }}">
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs font-medium text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="ml-64">
            <!-- Top Navigation -->
            <header class="bg-white shadow">
                <div class="flex justify-between items-center px-6 py-4">
                    <div class="flex items-center">
                        <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bell text-xl"></i>
                                @if($pendingReminders > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $pendingReminders }}
                                    </span>
                                @endif
                            </button>
                        </div>
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button class="flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                                <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->name }}">
                                <span class="ml-2">{{ Auth::user()->name }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/dragula@3.7.3/dist/dragula.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
</body>
</html> 