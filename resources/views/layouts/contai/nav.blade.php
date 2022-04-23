<ul>
    <li class="active"><a href="/">All Trainings</a></li>
    <li class=""><a href="{{ route('pop.create') }}">Upload Proof of Payment</a></li>
    @guest
    <li><a href="/login">Login</a></li>
    @endguest
    @auth
    <li  class=""><a href="/login">My Dashboard</a></li>
    <li  class=""><a class="" href="{{ route('logout') }}"
        onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a> 
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
        </form>
    
    </li>
    @endauth
</ul>