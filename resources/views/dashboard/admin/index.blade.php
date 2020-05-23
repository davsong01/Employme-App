@extends('dashboard.layouts.main')

@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">

                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Admin Dashboard</span></a></li>
                <!---Student management links-->

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-users"></i><span
                            class="hide-menu">Student Management </span></a>
                    <ul aria-expanded="false" class="collapse  first-level" style="margin-left:30px">

                        <li class="sidebar-item"><a href="{{route('users.create')}}" class="sidebar-link"><span
                                    class="hide-menu">- Admit student </span></a>
                        </li>

                        <li class="sidebar-item"><a href="{{route('users.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View All students </span></a>
                        </li>
                    </ul>
                </li>

                <!---end of student management links-->
                <!---teacher management links-->
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-user"></i><span
                            class="hide-menu">Facilitator Management </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('teachers.create')}}" class="sidebar-link"><span
                                    class="hide-menu"> Add
                                    Facilitator </span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{route('teachers.index')}}" class="sidebar-link"><i
                                    class="mdi mdi-note-plus"></i><span class="hide-menu"> View All facilitators
                                </span></a>
                        </li>
                    </ul>
                </li>
                <!---end of teacher management links-->
                <!---Class management links-->
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-chalkboard-teacher"></i><span
                            class="hide-menu">Training Management </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('programs.create')}}" class="sidebar-link"><span
                                    class="hide-menu">- Add new Training </span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{route('programs.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View all Trainings </span></a>
                        </li>

                        <li class="sidebar-item"><a href="{{route('programs.trashed')}}" class="sidebar-link"><span
                                    class="hide-menu">- Trashed Programs </span></a>
                        </li>

                    </ul>
                </li>
                <!---end of class management links-->
                <!---Class management links-->
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-download"></i><span
                            class="hide-menu">Study Materials </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">

                        <li class="sidebar-item"><a href="{{route('materials.create')}}" class="sidebar-link"><span
                                    class="hide-menu">- Add study materials </span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{route('materials.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View study materials </span></a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-star-half-alt"></i><span
                            class="hide-menu">Student Grades </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">

                        <li class="sidebar-item"><a href="{{route('results.create')}}" class="sidebar-link"><span
                                    class="hide-menu">- Upload Result </span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{ route('results.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View all results </span></a>
                        </li>
                    </ul>
                </li>
                <!---end of class management links-->
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('details.index')}}" aria-expanded="false"><i
                            class="mdi mdi-border-inside"></i><span class="hide-menu">Retrieve Details</span></a></li>


                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{route('payments.index')}}" aria-expanded="false"><i
                            class="far fa-money-bill-alt"></i><span class="hide-menu">Payment History</span></a></li>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="far fa-comments"></i><span
                            class="hide-menu">CRM Management </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('complains.create')}}" class="sidebar-link"><span
                                    class="hide-menu">- Add New Complain</span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{route('complains.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View All Complains</span></a>
                        </li>
                    </ul>

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fa fa-edit"></i><span
                            class="hide-menu">LMS </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('modules.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- Modules</span></a>
                        </li>
                        <li class="sidebar-item"><a href="{{route('questions.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- Questions</span></a>
                        <li class="sidebar-item"><a href="{{route('results.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- Submissions</span></a>
                        <li class="sidebar-item"><a href="{{route('scoreSettings.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- Score Settings</span></a>

                        </li>
                    </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
@endsection