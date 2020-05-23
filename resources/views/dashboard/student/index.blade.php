@extends('dashboard.layouts.main')
@section('title', 'Dashboard')
@section('dashboard')
<aside class="left-sidebar" data-sidebarbg="skin5">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="p-t-30">

                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ url('dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span
                            class="hide-menu">Student Dashboard</span></a></li>

                <!---end of class management links-->
                <!---Class management links-->

                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="fas fa-download"></i><span
                            class="hide-menu">My Study Materials </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('materials.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View study materials </span></a>
                        </li>
                    </ul>
                </li>
                
                @if(Auth::user()->program->hascrm == 1)

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
                </li>
                @endif

                <li class="sidebar-item"><a href="{{ route('tests.index')}}" class="sidebar-link"><i
                    class="fas fa-question"></i><span class="hide-menu">My Tests
                </span></a>
                </li>
                
                
                @if(Auth::user()->hasResult)
                <li class="sidebar-item"><a href="{{ route('results.show', Auth::user()->id) }}" class="sidebar-link"><i
                            class="fas fa-star-half-alt"></i><span class="hide-menu">My Result
                        </span></a>
                </li>
                @endif
                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                        href="javascript:void(0)" aria-expanded="false"><i class="far fa-money-bill-alt"></i><span
                            class="hide-menu">Payment History </span></a>
                    <ul style="margin-left:30px" aria-expanded="false" class="collapse  first-level">
                        <li class="sidebar-item"><a href="{{route('payments.index')}}" class="sidebar-link"><span
                                    class="hide-menu">- View payment
                                    history </span></a>
                        </li>
                    </ul>
                </li>
                @if(Auth::user()->balance > 0)
                <!--li class="sidebar-item blinking hide-menu"><i class="far fa-money-bill-alt"></i-->
                <li class="sidebar-item">
                    <form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal"
                        role="form" target="_blank">
                        <div class="row" style="margin-bottom:40px;">
                            <div class="col-md-12 col-md-offset-2">

                                <input type="hidden" name="email" value="{{Auth::user()->email}}"> {{-- required --}}

                                <input type="hidden" name="amount" value="{{Auth::user()->balance * 100}}">
                                {{-- required in kobo --}}
                                <input type="hidden" name="metadata"
                                    value="{{ json_encode($array = ['userid' => Auth::user()->id, 'pid' => Auth::user()->program_id]) }}">
                                {{-- For other necessary things you want to add to your payload. it is optional though --}}
                                <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                                {{-- required --}}
                                <input type="hidden" name="key" value="{{ config('paystack.secretKey') }}">
                                {{-- required --}}
                                {{ csrf_field() }} {{-- works only when using laravel 5.1, 5.2 --}}
                                <p>
                                    <button class="blinking btn btn-danger btn-lg btn-block" type="submit"
                                        value="Pay balance now!">
                                        <i class="fa fa-plus-circle fa-lg"></i> Pay balance now!
                                    </button>
                                </p>
                            </div>
                        </div>
                    </form>
                </li>
                @endif

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
@endsection