<?php 
    $user =  resolveAuthUser();
    $menus = $user->permissions();            

    $role = $user->role();
    $allmenus = app('app\Http\Controllers\Controller')->adminMenus('menu');
?>
@extends('dashboard.layouts.main')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                @if((resolveAuthUser()->isImpersonating()) )
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        style="color:yellow !important; font-weight:bolder"
                        href="{{ route('stop.impersonate.facilitator') }}" aria-expanded="false"><i
                            class="fa fa-arrow-left"></i><span class="hide-menu">BACK TO ADMIN</span></a></li>
                @endif
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                                class="hide-menu">Dashboard</span></a></li>
                {{-- Grader and Facilitator Dashboard only --}}
                {{-- @if(checkRoleHas(['Facilitator']))
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ route('teachers.students', resolveAuthUser()->id) }}" aria-expanded="false"><i
                                class="fa fa-users"></i><span class="hide-menu">My Students</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ route('teachers.programs', resolveAuthUser()->id) }}" aria-expanded="false"><i
                                class="fas fa-chalkboard-teacher"></i><span class="hide-menu">My Programs</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                            href="{{ route('teachers.earnings', resolveAuthUser()->id) }}" aria-expanded="false"><i
                                class="fas fa-wallet"></i><span class="hide-menu">My Earnings</span></a></li>
                @endif --}}
                {{-- End grader and facilitator menu --}}
                @foreach($allmenus as $allmenu)
                    {{-- Without children --}}
                    @if(empty($allmenu['children']))
                        @if(in_array($allmenu['route'], $menus))
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route($allmenu['route']) }}"
                                aria-expanded="false">
                                    <i class="{{ $allmenu['icon_class'] }}"></i>
                                    <span class="hide-menu">{{ $allmenu['name'] }}</span>
                                </a>
                            </li>
                        @endif
                    @else
                        {{-- With children --}}
                        @if(in_array($allmenu['route'], $menus))
                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow waves-effect waves-dark"
                                href="javascript:void(0)"
                                aria-expanded="false">
                                    <i class="{{ $allmenu['icon_class'] }}"></i>
                                    <span class="hide-menu">{{ $allmenu['name'] }}</span>
                                </a>
                                <ul style="margin-left:30px" aria-expanded="false" class="collapse first-level">
                                    @foreach($allmenu['children'] as $child)
                                        @if(in_array($child['route'], $menus))
                                            <li class="sidebar-item">
                                                <a href="{{ route($child['route']) }}" class="sidebar-link">
                                                    <span class="hide-menu">- {{ $child['name'] }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endif
                @endforeach

                <li class="sidebar-item">
                    <a class="sidebar-link has-arrow waves-effect waves-dark"
                    href="javascript:void(0)"
                    aria-expanded="false">
                        <i class="fa fa-cog"></i>
                        <span class="hide-menu">Self Service</span>
                    </a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="{{ route('profiles.edit', resolveAuthUser()->id) }}" class="sidebar-link">
                                <span class="hide-menu">- Account Settings</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link">
                                <span class="hide-menu">- Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

@endsection
@section('extra-scripts')
        @yield('extra-scripts')
@endsection