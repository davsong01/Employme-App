<?php 
    $menus = Auth::user()->menu_permissions ?? [];
    
    if($menus){
        $menus = explode(',',$menus);
    }else{
        $menus = [];
    }

?>
@extends('dashboard.layouts.main')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css">
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
                @if((Auth::user()->isImpersonating()) )
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        style="color:yellow !important; font-weight:bolder"
                        href="{{ route('stop.impersonate.facilitator') }}" aria-expanded="false"><i
                            class="fa fa-arrow-left"></i><span class="hide-menu">BACK TO ADMIN</span></a></li>
                @endif
                @if(Auth::user()->role_id == "Facilitator")
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                                class="hide-menu">Facilitator Dashboard</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ route('teachers.students', auth()->user()->id) }}" aria-expanded="false"><i
                                class="fa fa-users"></i><span class="hide-menu">My Students</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ route('teachers.programs', auth()->user()->id) }}" aria-expanded="false"><i
                                class="fas fa-chalkboard-teacher"></i><span class="hide-menu">My Programs</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ route('teachers.earnings', auth()->user()->id) }}" aria-expanded="false"><i
                                class="fas fa-wallet"></i><span class="hide-menu">My Earnings</span></a></li>
                @endif
                @if(Auth::user()->role_id == "Grader")
                @if(in_array(1, $menus))
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Grader Dashboard</span></a></li>
                @endif
                @endif
                @if(Auth::user()->role_id == "Admin")
                @if(in_array(1, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Admin Dashboard</span></a></li>
                @endif
                @endif
               
                @if(in_array(2, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('users.index')}}" aria-expanded="false"><i class="fas fa-users"></i><span
                            class="hide-menu">Student Management</span></a></li>
                @endif
                @if(in_array(3, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('teachers.index')}}" aria-expanded="false"><i class="fas fas fa-user"></i><span
                            class="hide-menu">View All Facilitators</span></a></li>
                @endif
                
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-chalkboard-teacher"></i><span
                            class="hide-menu">Training Management </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level"> 
                        @if(in_array(4, $menus))
                        <li class="sidebar-item"><a href="{{route('programs.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View all Trainings </span></a>
                        </li>
                        @endif
                        @if(in_array(5, $menus))
                        <li class="sidebar-item"><a href="{{route('programs.trashed')}}" class="sidebar-link"><span class="hide-menu">- Trashed Trainings </span></a>
                        </li>
                        @endif
            </ul>
            
            {{-- @endif --}}
            @if(in_array(6, $menus))
            <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{route('materials.index')}}" aria-expanded="false"><i class="fas fa-download"></i><span
                        class="hide-menu">View All study Materials</span></a></li>
            @endif
            @if(in_array(7, $menus))
            <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{ route('coupon.index') }}" aria-expanded="false"><i class="fa fa-gift"></i><span
                        class="hide-menu">Coupons</span></a></li>
            @endif
            @if(Auth::user()->role_id == "Admin")
                @if(in_array(8, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('payments.index')}}" aria-expanded="false"><i class="far fa-money-bill-alt"></i><span
                            class="hide-menu">Transactions</span></a></li>
                @endif
                @if(in_array(9, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('pop.index')}}" aria-expanded="false"><i class="fa fa-try"></i><span
                    class="hide-menu">Attempted Payments</span></a></li>
                @endif
            @endif
            @if(in_array(10, $menus))
            <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{route('complains.index')}}" aria-expanded="false"><i class="far fa-comments"></i><span
                        class="hide-menu">CRM Tool</span></a></li>
            @endif
            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                    href="javascript:void(0)" aria-expanded="false"><i class="fa fa-edit"></i><span
                        class="hide-menu">LMS </span></a>
                <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                    @if(in_array(11, $menus))
                    <li class="sidebar-item"><a href="{{route('modules.index')}}" class="sidebar-link"><span
                                class="hide-menu">- Modules</span></a>
                    </li>
                    @endif
                    @if(in_array(12, $menus))
                    <li class="sidebar-item"><a href="{{route('questions.index')}}" class="sidebar-link"><span class="hide-menu">- Questions</span></a>
                    </li>
                    @endif
                    @if(in_array(13, $menus))
                    <li class="sidebar-item"><a href="{{route('pretest.select')}}" class="sidebar-link"><span class="hide-menu">- Pre Test Grades</span></a>
                    @endif
                    @if(in_array(14, $menus))
                    <li class="sidebar-item"><a href="{{route('posttest.results')}}" class="sidebar-link"><span class="hide-menu">- Grades</span></a>
                    @endif
                        @if(Auth()->user()->role_id == "Admin")
                        @if(in_array(15, $menus))
                        <li class="sidebar-item"><a href="{{route('certificates.index')}}" class="sidebar-link"><span class="hide-menu">- Certificates</span></a>
                        </li>
                        @endif
                        @if(in_array(16, $menus))
                        <li class="sidebar-item"><a href="{{route('scoreSettings.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- Score Settings</span></a>
                        </li>
                        @endif
                    @endif
                </ul>
                @if(Auth::user()->role_id == "Admin")
                @if(in_array(17, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('users.mail')}}" aria-expanded="false"><i class="fa fa-envelope"></i><span
                            class="hide-menu">Email Participants</span></a></li>
                @endif
                @if(in_array(18, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('payment-modes.index')}}" aria-expanded="false"><i class="fa fa-credit-card"></i><span
                            class="hide-menu">Payment modes</span></a></li>
                @endif
                @if(in_array(19, $menus))
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('settings.edit', 1)}}" aria-expanded="false"><i class="fa fa-cog"></i><span
                        class="hide-menu">Settings</span></a></li>
                @endif
            @endif
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

@endsection