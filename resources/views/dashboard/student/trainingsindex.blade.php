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
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('/') }}" aria-expanded="false"><i class="fa fa-home"></i><span
                            class="hide-menu">All Trainings</span></a></li>
                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">My Trainings</span></a></li>
                <li class="sidebar-item"><a href="{{ route('materials.index', ['p_id' => $program->id]) }}" class="sidebar-link"><i
                    class="fas fa-download"></i><span class="hide-menu">My Study Materials
                </span></a>
                </li>
                @if($program->hascrm == 1)
                <li class="sidebar-item"><a href="{{ route('complains.index', ['p_id' => $program->id])}}" class="sidebar-link"><i
                    class="fas fa-comments"></i><span class="hide-menu">CRM Tool</span></a>
                </li>
                @endif
                <li class="sidebar-item"><a href="{{ route('tests.index', ['p_id'=>$program->id])}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">My Tests</span></a>
                </li>
                <li class="sidebar-item"><a href="{{ route('tests.results', ['p_id' => $program->id])}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">My Completed Tests</span></a>
                </li>
                @if($program->hasresult == 1)
                <li class="sidebar-item"><a href="{{ route('results.show', ['id' => Auth::user()->id, 'p_id' => $program->id]) }}" class="sidebar-link"><i
                            class="fas fa-star-half-alt"></i><span class="hide-menu">My Result
                        </span></a>
                </li>
                @endif
                
                @if(auth()->user()->certificate)
                <li class="sidebar-item"><a href="{{ route('certificates.index', ['p_id' => $program->id]) }}" class="sidebar-link"><i
                            class="fas fa-certificate"></i><span class="hide-menu">My Certificate
                    </span></a>
                </li>
                @endif

                @if(isset($balance))
                    @if($balance > 0)
                    <!--li class="sidebar-item blinking hide-menu"><i class="far fa-money-bill-alt"></i-->
                    <li class="sidebar-item">
                        <form action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" method="POST"
                            role="form" target="_blank">
                            <div class="row" style="margin-bottom:40px;">
                                <div class="col-md-12 col-md-offset-2">

                                    <input type="hidden" name="email" value="{{Auth::user()->email}}"> {{-- required --}}

                                    <input type="hidden" name="amount" id="amount" value="{{ $balance * 100}}" required>
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="currency" value="{{  config('custom.curr_abbreviation') }}">
                                    <input type="hidden" name="metadata"
                                        value="{{ json_encode($array = ['user_id' => Auth::user()->id, 'p_id' => $program->id, 'type' =>'balance', 'name'=> auth()->user()->name ]) }}">
                                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                                    {{-- required --}}
                                     <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}
                                    <p>
                                        <button class="blinking btn btn-danger btn-lg btn-block" type="submit">Pay balance of {{ config('custom.default_currency'). $balance }} now!
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </li>
                    @endif
                @endif
                
                @if(!empty(request()->query('p_id')))
                     @if($program->checkBalance(request()->query('p_id')) > 0)
                    <!--li class="sidebar-item blinking hide-menu"><i class="far fa-money-bill-alt"></i-->
                    <li class="sidebar-item">
                        <form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal"
                            role="form" target="_blank">
                            <div class="row" style="margin-bottom:40px;">
                                <div class="col-md-12 col-md-offset-2">
                                    <input type="hidden" name="email" value="{{Auth::user()->email}}"> {{-- required --}}
                                    <input type="hidden" name="amount" id="amount" value="{{ $program->checkBalance(request()->query('p_id')) * 100}}" required>
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="currency" value="{{  config('custom.curr_abbreviation') }}">
                                    <input type="hidden" name="metadata"
                                        value="{{ json_encode($array = ['user_id' => Auth::user()->id, 'p_id' => request()->query('p_id'), 'type' =>'balance' , 'name'=> auth()->user()->name]) }}">
                                    <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                                    {{-- required --}}
                                     <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}
                                    <p>
                                        <button class="blinking btn btn-danger btn-lg btn-block" type="submit">Pay balance of {{ config('custom.default_currency'). $program->checkBalance(request()->query('p_id')) }} now!
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </li>
                    @endif
                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
@endsection