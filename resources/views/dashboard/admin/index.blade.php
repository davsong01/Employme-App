@extends('dashboard.layouts.main')

@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">
               
                @if(Auth::user()->isImpersonating() )
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    style="color:yellow !important; font-weight:bolder" href="{{ route('stop.impersonate.facilitator') }}" aria-expanded="false"><i class="fa fa-arrow-left"></i><span
                        class="hide-menu">BACK TO ADMIN</span></a></li>
                @endif
                @if(Auth::user()->role_id == "Facilitator")
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                        class="hide-menu">Facilitator Dashboard</span></a></li>
                @endif
                @if(Auth::user()->role_id == "Admin")
               
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Admin Dashboard</span></a></li>
                
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{ route('reconcile') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                        class="hide-menu">Reconcile</span></a></li>
                <!---Student management links-->
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('users.index')}}" aria-expanded="false"><i class="fas fa-users"></i><span
                            class="hide-menu">Student Management</span></a></li>

                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                href="{{route('pop.index')}}" aria-expanded="false"><i class="fa fa-check-circle"></i><span
                    class="hide-menu">Approve Payment</span></a></li>

                <!---end of student management links-->
                <!---teacher management links-->
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{route('teachers.index')}}" aria-expanded="false"><i class="fas fas fa-user"></i><span
                        class="hide-menu">View All Facilitators</span></a></li>

                <!---end of teacher management links-->
                <!---Class management links-->
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-chalkboard-teacher"></i><span
                            class="hide-menu">Training Management </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('programs.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View all Trainings </span></a>
                        </li>

                        <li class="sidebar-item"><a href="{{route('programs.trashed')}}" class="sidebar-link"><span
                                    class="hide-menu">- Trashed Programs </span></a>
                        </li>

                    </ul>
                </li>
                @endif
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('materials.index')}}" aria-expanded="false"><i class="fas fa-download"></i><span
                            class="hide-menu">View All study Materials</span></a></li>
                @if(Auth::user()->role_id == "Admin")
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('payments.index')}}" aria-expanded="false"><i class="far fa-money-bill-alt"></i><span
                            class="hide-menu">Transactions</span></a></li>
                @endif
            <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{route('complains.index')}}" aria-expanded="false"><i class="far fa-comments"></i><span
                        class="hide-menu">CRM Tool</span></a></li>

            <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                    href="javascript:void(0)" aria-expanded="false"><i class="fa fa-edit"></i><span
                        class="hide-menu">LMS </span></a>
                <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                    
                    <li class="sidebar-item"><a href="{{route('modules.index')}}" class="sidebar-link"><span
                                class="hide-menu">- Modules</span></a>
                    </li>
                    <li class="sidebar-item"><a href="{{route('questions.index')}}" class="sidebar-link"><span
                                class="hide-menu">- Questions</span></a>
                    </li>
                  
                    <li class="sidebar-item"><a href="{{route('pretest.select')}}" class="sidebar-link"><span
                    class="hide-menu">- Pre Test Grades</span></a>

                    <li class="sidebar-item"><a href="{{route('posttest.results')}}" class="sidebar-link"><span
                    class="hide-menu">- Grades</span></a>
                    
                    @if(Auth()->user()->role_id == "Admin")
                    <li class="sidebar-item"><a href="{{route('certificates.index')}}" class="sidebar-link"><span
                        class="hide-menu">- Certificates</span></a>

                    </li>
                    <li class="sidebar-item"><a href="{{route('scoreSettings.index')}}" class="sidebar-link"><span
                                class="hide-menu">- Score Settings</span></a>

                    </li>
                    @endif
                </ul>
                @if(Auth::user()->role_id == "Admin")
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                    href="{{route('users.mail')}}" aria-expanded="false"><i class="fa fa-envelope"></i><span
                        class="hide-menu">Email Participants</span></a></li>
                @endif
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
@endsection