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
                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company_user.dashboard') }}" aria-expanded="false">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span class="hide-menu">Company Admin Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('company.participants') }}" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span class="hide-menu">Participants</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('pretest.select') }}" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span class="hide-menu">Pre Test Results</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link waves-effect waves-dark" href="{{ route('posttest.results') }}" aria-expanded="false">
                        <i class="fas fa-users"></i>
                        <span class="hide-menu">Post Test Results</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
@endsection

@section('extra-scripts')
@include('dashboard.company.partials.company_extra_js')
@yield('extra-scripts')
@endsection
