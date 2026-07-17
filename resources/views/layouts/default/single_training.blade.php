@extends('layouts.frontend')
@section('title', 'All Trainings')
@section('content')
<!-- Product Details Area Start -->
<div class="single-product-area section-padding-100 clearfix">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    @include('layouts.partials.alerts')
                    <ol class="breadcrumb" style="margin-top: 0px;">
                        <li class="breadcrumb-item"><a href="/">All Trainings</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $training->p_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-7">
                <div class="" style="margin-bottom: 20px;">
                    <div id="product_details_slider" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="">
                                <img src="{{ '/'.$training->image }}" alt="Training image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="single_product_desc">
                     @if($training->p_end < date('Y-m-d') || $training->close_registration == 1)
                     <div class="product-meta-data">
                        <div class="line"></div>
                        <p class="product-price">{{ \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount) }}</p>
                        <a>
                            <h6 style="font-size: 22px !important;">{{ $training->p_name }}</h6>
                        </a>
                       <p style="color:red">SORRY! Registration for this program has closed</p>
                    <div>
                        <a href="{{ url('/') }}"><button type="submit" class="btn btn-block login-btn mb-4">VIEW OTHER TRAININGS</button></a>
                    </div>
                    </div>
                    @else
                    <!-- Product Meta Data -->
                    <div class="product-meta-data">
                        <div class="line"></div>
                        <p class="product-price">{{\App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY'). number_format($training->p_amount) }}</p>
                        <a>
                            <h6 style="font-size: 22px !important;">{{ $training->p_name }}</h6>
                        </a>
                        
                    </div>
                    <div><p>Please Enter your details below and make payment</p></div>
                    <!-- Add to Cart Form -->
                    <form class="cart clearfix" action="{{ route('pay') }}" method="post">
                        <div class="row g-3">
                            <div class="col-12">
                                <select name="amount" id="amount" class="form-select" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="{{ $training->p_amount * 100}}">Full Payment ({{ \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount) }})</option>
                                    @if($training->early_bird_status == 0 && $training->e_amount > 0)
                                    <option value="{{ $training->e_amount * 100}}">Earlybird ({{ \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->e_amount) }})</option>
                                    @endif
                                    @if($training->haspartpayment == 1)
                                    <option value="{{ ($training->p_amount/2) *100}}">Part Payment ({{ \App\Models\Settings::select('DEFAULT_CURRENCY')->first()->value('DEFAULT_CURRENCY').number_format($training->p_amount/2) }})</option>
                                    @endif
                                </select>
                                @if ($errors->has('amount'))
                                    <div class="text-danger small mt-1">{{ $errors->first('amount') }}</div>
                                @endif
                            </div>

                        @if($locations->count() <= 0)
                            <input type="hidden" name="location" id="location">
                        @else
                            <div class="col-12">
                                <select name="location" id="location" class="form-select">
                                    <option value="">Select Location (Optional)</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location') == $location->id ? 'selected' : '' }}>
                                            {{ $location->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('location'))
                                    <div class="text-danger small mt-1">{{ $errors->first('location') }}</div>
                                @endif
                            </div>
                        @endif

                            <div class="col-12">
                                <input type="text" class="form-control" id="name" name="name" 
                                @auth
                                value="{{ resolveAuthUser()->name }}"  
                                placeholder="Full Name"
                                @endauth

                                @guest 
                                 value="{{ old('name') }}" placeholder="Full Name"  
                                 @endguest 
                                
                                required>
                                @if ($errors->has('name'))
                                    <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <input type="email" name="email" class="form-control" id="email" 
                                @auth
                                value="{{ resolveAuthUser()->email }}"  
                                placeholder="Email"
                                @endauth

                                @guest 
                                 value="{{ old('email') }}" placeholder="Email"  
                                 @endguest 
                                
                                required>
                                @if ($errors->has('email'))
                                    <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <input type="text" class="form-control" name="phone" id="phone" 
                                @auth
                                value="{{ resolveAuthUser()->phone }}"  
                                placeholder="Phone Number"
                                @endauth

                                @guest 
                                 value="{{ old('phone') }}" placeholder="Phone Number"  
                                 @endguest 
                                
                                required>
                                @if ($errors->has('phone'))
                                    <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>
                        </div>
                        
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="currency" value="{{  \App\Models\Settings::select('CURR_ABBREVIATION')->first()->value('CURR_ABBREVIATION') }}">
                        {{-- <input type="hidden" name="metadata2" value="{{ json_encode($array = ['pid' => $training->id]) }}" >  --}}
                        
                        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
            
                        <input type="hidden" name="metadata" id="metadata">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> {{-- employ this in place of csrf_field only in laravel 5.0 --}}
                        <script>
                            function fetchMetaValues() { 
                              
                                let metadata = document.getElementById('metadata'); 
                               
                                let name = document.getElementById('name').value; 
                                let phone = document.getElementById('phone').value; 
                                let location = document.getElementById('location').value;
                               let fid = { 'name':name, 'phone': phone, 'location': location, 'pid': {{ $training->id }} }; 
                               metadata.value = JSON.stringify(fid); }
                               
                        </script>
                        <div>
                    <button type="submit" onclick="fetchMetaValues()" class="btn btn-primary w-100 mb-4">Make Payment</button>
                    </div>
                    <div>
                        <a href="{{ url('/') }}" class="btn btn-success w-100 mb-4">VIEW OTHER TRAININGS</a>
                    </div>                      
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Area End -->
@endsection  
