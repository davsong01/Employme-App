@extends('layouts.frontend')
@section('title')
    {{ config('app.name') }}. Proof of Payment
@endsection
@section ('status')
class="active"
@endsection
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
                        <li class="breadcrumb-item active" aria-current="page">Upload Proof of Payment</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="single_product_desc">
                    <!-- Product Meta Data -->
                    <div class="product-meta-data">
                        <div class="line"></div>
                       
                    </div>
                    <div><p>Please fill the form below to upload proof of payment</p></div>
                    <!-- Add to Cart Form -->
                    <form class="cart clearfix" action="{{ route('pop.store') }}" method="post" enctype="multipart/form-data" >
                         {{ csrf_field() }}
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <select name="training" id="training" class="form-control" required>
                                    <option value="">-- Select Training --</option>
                                    @foreach($trainings as $training)
                                    <option value="{{ $training->id }}">{{ $training->p_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if ($errors->has('training'))
                            <span class="help-block">
                                <strong>{{ $errors->first('training') }}</strong>
                            </span>
                        @endif

                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
                            </div>
                        </div>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Email" required>
                            </div>
                        </div>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="number" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Phone" required>
                            </div>
                        </div>
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <select name="bank" id="bank" placeholder="Bank paid into" class="form-control">
                                    <option value="">-- Bank paid into --</option>
                                    <option value="Access">Access</option>
                                    <option value="GTB">GTB</option>
                                </select>
                            </div>
                        </div>
                        @if ($errors->has('bank'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bank') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <input type="number" class="form-control" name="amount" id="amount" value="{{ old('amount') }}" placeholder="Amount" min=1 required>
                            </div>
                        </div>
                        @if ($errors->has('amount'))
                            <span class="help-block">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <label for="file">Upload proof of payment</label>
                                <input type="file" class="form-control" name="file" id="file" value="{{ old('file') }}" placeholder="Upload payment evidence" required>
                            </div>
                        </div>
                        @if ($errors->has('file'))
                            <span class="help-block">
                                <strong>{{ $errors->first('file') }}</strong>
                            </span>
                        @endif
                        <div class="cart-btn d-flex">
                            <div class="col-md-12 mb-3">
                                <label for="date">Date of Payment</label>
                                <input type="date" name="date" value="{{ old('date') }}" class="form-control"
                                    required>
                            </div>
                        </div>
                        @if ($errors->has('date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('date') }}</strong>
                            </span>
                        @endif
                        
                        <div class="cart-btn d-flex">
                            @if($locations->count() <= 0)
                            <input type= 'hidden' name="location" id="location"
                            <div class="col-md-12 mb-3">
                                @else
                                <select name="location" id="location" class="form-control">
                                    <option value="">-- Select Location --</option>
                                   @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location') == $location->id ? 'selected' : '' }}>
                                            {{ $location->title }}
                                        </option>
                                    @endforeach
                                </select>
                               
                            </div>
                            @endif
                        </div>
                        @if ($errors->has('location'))
                            <span class="help-block">
                                <strong>{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                         <div>
                            <button type="submit" class="btn btn-block login-btn mb-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Area End -->
@endsection