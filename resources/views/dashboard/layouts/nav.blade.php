<nav class="navbar top-navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="navbar-header d-flex align-items-center gap-2 px-3" data-logobg="skin5">
        <!-- This is for the sidebar toggle which is visible on mobile only -->
        <a class="nav-toggler waves-effect waves-light d-block d-lg-none" href="javascript:void(0)"><i
                class="ti-menu ti-close"></i></a>

        <a class="navbar-brand" href="{{ url('/') }}">
            <!-- Logo icon -->
            <b class="logo-icon p-l-10"></b>
            <span class="logo-text">
                <img src="{{ asset(\App\Models\Settings::value('logo')) }}" alt="homepage" class="light-logo" />
            </span>
        </a>
        <a class="topbartoggler d-block d-lg-none waves-effect waves-light" href="javascript:void(0)"
            data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
    </div>
    <div class="navbar-collapse collapse px-3" id="navbarSupportedContent" data-navbarbg="skin5">
        <ul class="navbar-nav me-auto mb-0">
            <li class="nav-item d-none d-lg-block"><a class="nav-link sidebartoggler waves-effect waves-light"
                    href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a>
            </li>

        </ul>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <span class="text-white small d-none d-md-inline" id="ct"></span>
            <span class="text-white small d-none d-md-inline">
                Welcome, {{ resolveAuthUser()->name }}
            </span>
            <div class="nav-item dropdown list-unstyled">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#"
                    id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ (filter_var(resolveAuthUser()->profile_picture, FILTER_VALIDATE_URL) !== false)
                        ? resolveAuthUser()->profile_picture
                        : asset('/avatars/'.resolveAuthUser()->profile_picture) }}"
                        alt="avatar" class="rounded-circle border border-2 border-white" width="42" height="42">
                </a>

                {{-- <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
            @guest
                <li><a class="dropdown-item" href="{{ route('login') }}"><i class="ti-user m-r-5 m-l-5"></i> Login</a></li>
                <li><a class="dropdown-item" href="{{ route('register') }}"><i class="ti-wallet m-r-5 m-l-5"></i> Register</a></li>
            @else
                <li><a class="dropdown-item" href="{{ route('profiles.edit', resolveAuthUser()->id) }}"><i class="ti-settings m-r-5 m-l-5"></i> Account Setting</a></li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout') }}" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            @endguest
                </ul> --}}
            </div>
        </div>
    </div>
</nav>
