<ul>
    <li class="{{ request()->is('/') ? 'active' : '' }}">
        <a href="/">All Trainings</a>
    </li>

    <li class="{{ request()->routeIs('packages') ? 'active' : '' }}">
        <a href="{{ route('packages') }}">Packages</a>
    </li>

    <li class="{{ request()->routeIs('upload-proof-of-payment') ? 'active' : '' }}">
        <a href="{{ route('upload-proof-of-payment') }}">Upload Proof of Payment</a>
    </li>

    {{-- <li class="{{ request()->routeIs('reset') ? 'active' : '' }}">
        <a href="{{ route('reset') }}">Reset All</a>
    </li> --}}

    @guest
        <li class="{{ request()->is('login') ? 'active' : '' }}">
            <a href="{{ url('/login') }}">Login</a>
        </li>
    @endguest

    @auth
        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ url('/dashboard') }}">My Dashboard</a>
        </li>
        <li>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </li>
    @endauth
</ul>