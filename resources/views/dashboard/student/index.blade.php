@extends('dashboard.layouts.main')
@section('title', 'Dashboard')
@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                @if(Auth::user()->isImpersonating() )
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="{{ route('stop.impersonate') }}" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                @endif
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('/') }}" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">All Trainings</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Student Dashboard</span></a></li>
                <li class="sidebar-item"><a href="{{ route('payments.index', Auth::user()->id) }}" class="sidebar-link"><i
                    class="far fa-money-bill-alt"></i><span class="hide-menu">Payment History
                </span></a>
                </li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ route('my.wallet', auth()->user()->id) }}" aria-expanded="false"><i class="fa fa-money-bill"></i><span
                            class="hide-menu">&nbsp;Account TopUp History</span></a></li>
               
            </ul>
        </nav>
    </div>
</aside>
@endsection

