@extends('dashboard.layouts.main')
@section('title', 'Trainings')
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
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"  style="color:yellow !important; font-weight:bolder"
                        href="{{ url('/') }}" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">All Trainings</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">My Trainings</span></a></li>
                @if($program->hasmock == 1)
                    <li class="sidebar-item"><a href="{{ route('mocks.index', ['p_id' => $program->id])}}" class="sidebar-link"><i
                        class="fa fa-chalkboard"></i><span class="hide-menu">Pre Class Tests</span></a>
                    </li>    
                @endif
                <li class="sidebar-item"><a href="{{ route('materials.index', ['p_id' => $program->id]) }}" class="sidebar-link"><i
                    class="fas fa-download"></i><span class="hide-menu">My Study Materials
                </span></a>
                </li>
                @if($program->hascrm == 1)
                <li class="sidebar-item"><a href="{{ route('complains.index', ['p_id' => $program->id])}}" class="sidebar-link"><i
                    class="fas fa-comments"></i><span class="hide-menu">CRM Tool</span></a>
                </li>
                @endif
                @if(isset($facilitator) && !empty($facilitator))
                <li class="sidebar-item"><a href="{{ route('training.instructor', ['p_id'=>$program->id])}}" class="sidebar-link"><i
                    class="fas fa-chalkboard-teacher"></i><span class="hide-menu">Program Instructor</span></a>
                </li>
                @endif
                <li class="sidebar-item"><a href="{{ route('tests.index', ['p_id'=>$program->id])}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">Post Class Tests</span></a>
                </li>
                <li class="sidebar-item"><a href="{{ route('tests.results', ['p_id' => $program->id])}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">My Completed Tests</span></a>
                </li>
               
                @if($program->hasresult == 1 )
                <li class="sidebar-item"><a href="{{ route('results.show', ['id' => Auth::user()->id, 'p_id' => $program->id]) }}" class="sidebar-link"><i
                            class="fas fa-star-half-alt"></i><span class="hide-menu">My Result
                        </span></a>
                </li>
                @endif
                
                @if(auth()->user()->certificates->count() > 0 )
                <li class="sidebar-item"><a href="{{ route('certificates.index', ['p_id' => $program->id]) }}" class="sidebar-link"><i
                            class="fas fa-certificate"></i><span class="hide-menu">My Certificate
                    </span></a>
                </li>
                @endif

                @if(isset($balance) && $balance > 0)
                    <li class="sidebar-item">
                        <a class="blinking btn btn-danger btn-lg btn-block" href="{{ route('balance.checkout', ['p_id' => $program->id] )}}" class="form-horizontal">Pay balance of {{ number_format($balance) }} now</a>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
@endsection