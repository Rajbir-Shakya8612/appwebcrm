@php
    use Illuminate\Support\Facades\Auth;
    $userRole = Auth::user()->role ?? null;
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo class="h-9" />
        </a>

        <!-- Hamburger Button for Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Admin: Show all dashboards -->
                @if ($userRole == 1)
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('carpenters.dashboard', ['carpenterUniqueKey' => Auth::user()->user_unique_key]) ? 'active' : '' }}"
                            href="{{ route('carpenters.dashboard', ['carpenterUniqueKey' => Auth::user()->user_unique_key]) }}">
                            Carpenter Dashboard
                        </a>
                    </li> --}}
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('salespersons.dashboard', ['salesPersonUniqueKey' => Auth::user()->user_unique_key]) ? 'active' : '' }}"
                            href="{{ route('salespersons.dashboard', ['salesPersonUniqueKey' => Auth::user()->user_unique_key]) }}">
                            Sales Person Dashboard
                        </a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('salespersons.dealerList') ? 'active' : '' }}"
                            href="{{ route('salespersons.dealerList') }}">
                            Dealer List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('salespersons.carpenterList') ? 'active' : '' }}"
                            href="{{ route('salespersons.carpenterList') }}">
                            Carpenter List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professionals.index') ? 'active' : '' }}"
                            href="{{ route('professionals.index') }}">
                            Professionals List
                        </a>
                    </li>
                @endif

                <!-- Carpenter: Show only carpenter dashboard -->
               @if ($userRole == 5)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('carpenters.dashboard', ['carpenterUniqueKey' => Auth::user()->user_unique_key]) ? 'active' : '' }}"
                            href="{{ route('carpenters.dashboard', ['carpenterUniqueKey' => Auth::user()->user_unique_key]) }}">
                            Carpenter Dashboard
                        </a>
                    </li>
                @endif

                <!-- Dealer: Show only dealer dashboard -->
                @if ($userRole == 4)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dealers.dashboard') ? 'active' : '' }}"
                            href="{{ route('dealers.dashboard', ['dealerUniqueKey' => Auth::user()->user_unique_key]) }}">
                            Dealer Dashboard
                        </a>
                    </li>
                @endif

                <!-- Salesperson: Show only salesperson dashboard -->
                @if ($userRole == 3)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('salespersons.dashboard', ['salesPersonUniqueKey' => Auth::user()->user_unique_key]) ? 'active' : '' }}"
                            href="{{ route('salespersons.dashboard', ['salesPersonUniqueKey' => Auth::user()->user_unique_key]) }}">
                            Sales Person Dashboard
                        </a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('salespersons.dealerList') ? 'active' : '' }}"
                            href="{{ route('salespersons.dealerList') }}">
                            Dealer List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('salespersons.carpenterList') ? 'active' : '' }}"
                            href="{{ route('salespersons.carpenterList') }}">
                            Carpenter List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('professionals.index') ? 'active' : '' }}"
                            href="{{ route('professionals.index') }}">
                            Professionals List
                        </a>
                    </li>
                @endif
            </ul>

            <!-- Settings Dropdown -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
