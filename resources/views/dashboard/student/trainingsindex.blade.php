@php
    use App\Models\Transaction;
@endphp
@extends('dashboard.layouts.main')
@section('title', 'Trainings')
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
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"  style="color:yellow !important; font-weight:bolder"
                        href="{{ url('/') }}" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">Home</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">My Trainings</span></a></li>
                @if($program->hasmock == 1)
                    <li class="sidebar-item"><a href="{{ route('participants.mocks.index', ['p_id' => $program->id])}}" class="sidebar-link"><i
                        class="fa fa-chalkboard"></i><span class="hide-menu">Pre Class Tests</span></a>
                    </li>    
                @endif
                <li class="sidebar-item"><a href="{{ route('participants.materials.index', ['p_id' => $program->id]) }}" class="sidebar-link"><i
                    class="fas fa-download"></i><span class="hide-menu">My Study Materials
                </span></a>
                </li>
                @if($program->hascrm == 1)
                <li class="sidebar-item"><a href="{{ route('participants.complains.index', ['p_id' => $program->id])}}" class="sidebar-link"><i
                    class="fas fa-comments"></i><span class="hide-menu">CRM Tool</span></a>
                </li>
                @endif
                @if(isset($facilitator) && !empty($facilitator))
                <li class="sidebar-item"><a href="{{ route('training.instructor', ['p_id'=>$program->id])}}" class="sidebar-link"><i
                    class="fas fa-chalkboard-teacher"></i><span class="hide-menu">Program Instructor</span></a>
                </li>
                @endif
                <li class="sidebar-item"><a href="{{ route('participants.tests.index', ['p_id'=>$program->id])}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">Post Class Tests</span></a>
                </li>
                <li class="sidebar-item"><a href="{{ route('tests.results', ['p_id' => $program->id])}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">My Completed Tests</span></a>
                </li>
                
                @if($program->hasresult == 1 )
                <li class="sidebar-item"><a href="{{ route('participants.results.show', ['result' => resolveAuthUser()->id, 'p_id' => $program->id]) }}" class="sidebar-link"><i class="fas fa-star-half-alt"></i><span class="hide-menu">My Result
                        </span></a>
                </li>
                @endif
                
                @if($program->show_certificate == 1 )
                    @php
                        $trans = Transaction::query()
                        ->select('id', 'show_certificate', 'program_id', 'user_id')
                        ->where('user_id', resolveAuthUser()->id)
                        ->where('program_id', $program->id)
                        ->first();
                        $show_certificate = !empty($trans) ? $trans->show_certificate : 0;
                    @endphp
                    @if($show_certificate == 1 && !empty($trans->certificate) )
                    <li class="sidebar-item"><a href="{{ route('participants.certificates.index', ['p_id' => $program->id]) }}" class="sidebar-link"><i
                                class="fas fa-certificate"></i><span class="hide-menu">My Certificate
                        </span></a>
                    </li>
                    @endif
                @endif

                @if(isset($balance) && $balance > 0)
                    @if($program->allow_flexible_payment == 'yes')
                    <li class="sidebar-item">
                        <a class="blinking btn btn-danger btn-lg btn-block" href="{{ route('balance.checkout', ['p_id' => $program->id, 'program' => $program] )}}" class="form-horizontal">Pay balance</a>
                    </li>
                    @else
                    <li class="sidebar-item">
                        <a class="blinking btn btn-danger btn-lg btn-block" href="{{ route('balance.checkout', ['p_id' => $program->id] )}}" class="form-horizontal">Pay balance of {{ $currency_symbol.number_format($balance) }} now</a>
                    </li>
                    @endif
                @endif

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

    <div class="modal" id="trainingcatalogue" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                        <div class="card-title">
                            <div>
                                <div class="card-content">
                                    <a class="pre-order-btn" href="{{ route('download.program.brochure',['p_id' => $program->id]) }}">DOWNLOAD TRAINING CATALOGUE</a>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

@endsection
@section('extra-scripts')
    @if($program->show_catalogue_popup == 'yes' && resolveAuthUser()->downloaded_catalogue == 'no')
    <script>
        $(document).ready(function(){       
            $('#trainingcatalogue').modal({
                backdrop: 'static',
                keyboard: false 
            });
            // $('#trainingcatalogue').modal('show');

        }); 
    </script>
    @endif
@endsection
