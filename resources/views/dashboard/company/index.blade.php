<?php 
    $menus = Auth::user()->permissions ?? [];
?>
@extends('dashboard.layouts.main')
@section('css')
@include('dashboard.company.partials.company_extra_css')
@endsection
@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="pt-3">
                @if(in_array(1, $menus))
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company_user.dashboard') }}" aria-expanded="false">
                        <i class="fa fa-desktop"></i>
                        <span class="hide-menu"> Dashboard</span>
                    </a>
                </li>
                @endif

                @if(in_array(2, $menus))
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company.participants') }}" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span class="hide-menu"> Participants</span>
                    </a>
                </li>
                @endif

                @if(in_array(4, $menus))
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company.pretest.select') }}" aria-expanded="false">
                        <i class="fa fa-list-alt"></i>
                        <span class="hide-menu"> Pre Test Results</span>
                    </a>
                </li>
                @endif

                @if(in_array(5, $menus))
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company.posttest.results') }}" aria-expanded="false">
                        <i class="fa fa-graduation-cap"></i>
                        <span class="hide-menu"> Post Test Results</span>
                    </a>
                </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false">
                        <i class="fa fa-sign-out-alt"></i>
                        <span class="hide-menu"> Logout</span>
                    </a>
                </li>
                    
                    <form id="logout-form" action="{{ route('company.logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>

            </ul>
        </nav>
    </div>
</aside>
@endsection

@section('extra-scripts')
@include('dashboard.company.partials.company_extra_js')
@yield('extra-scripts')
@endsection
