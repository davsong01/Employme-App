@extends('dashboard.layouts.main')
@section('title', 'Dashboard')
@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                @if(resolveAuthUser()->isImpersonating() )
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="{{ route('stop.impersonate') }}" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                @endif
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('/') }}" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">Home</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">My Trainings</span></a></li>
                <li class="sidebar-item"><a href="{{ route('participants.payments.index', resolveAuthUser()->id) }}" class="sidebar-link"><i
                    class="far fa-money-bill-alt"></i><span class="hide-menu">My Payment History
                </span></a>
                </li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ route('my.wallet', resolveAuthUser()->id) }}" aria-expanded="false"><i class="fa fa-money-bill"></i><span
                            class="hide-menu">&nbsp;Account TopUp History</span></a></li>
                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark"
                    href="javascript:void(0)"
                    aria-expanded="false">
                        <i class="fa fa-cog"></i>
                        <span class="hide-menu">Self Service</span>
                    </a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('participants.profiles.edit', resolveAuthUser()->id) }}" class="sidebar-link">
                                <span class="hide-menu">- Account Settings</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link">
                                <span class="hide-menu">- Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>
@endsection

