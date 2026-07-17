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
                    <div class="mb-4"><p>Please fill the form below to upload proof of payment</p></div>
                    <!-- Add to Cart Form -->
                    <form class="cart clearfix" action="{{ route('pop.store') }}" method="post" enctype="multipart/form-data">
                         {{ csrf_field() }}
                        <div class="row g-3">
                            <div class="col-12">
                                <select name="training" id="training" class="form-select" required>
                                    <option value="">-- Select Training --</option>
                                    @foreach($trainings as $training)
                                    <option value="{{ $training->id }}">{{ $training->p_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('training'))
                                    <div class="text-danger small mt-1">{{ $errors->first('training') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
                                @if ($errors->has('name'))
                                    <div class="text-danger small mt-1">{{ $errors->first('name') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Email" required>
                                @if ($errors->has('email'))
                                    <div class="text-danger small mt-1">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <input type="number" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Phone" required>
                                @if ($errors->has('phone'))
                                    <div class="text-danger small mt-1">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <select name="bank" id="bank" placeholder="Bank paid into" class="form-select">
                                    <option value="">-- Bank paid into --</option>
                                    <option value="Access">Access</option>
                                    <option value="GTB">GTB</option>
                                </select>
                                @if ($errors->has('bank'))
                                    <div class="text-danger small mt-1">{{ $errors->first('bank') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <input type="number" class="form-control" name="amount" id="amount" value="{{ old('amount') }}" placeholder="Amount" min="1" required>
                                @if ($errors->has('amount'))
                                    <div class="text-danger small mt-1">{{ $errors->first('amount') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label for="file">Upload proof of payment</label>
                                <input type="file" class="form-control" name="file" id="file" value="{{ old('file') }}" placeholder="Upload payment evidence" required>
                                @if ($errors->has('file'))
                                    <div class="text-danger small mt-1">{{ $errors->first('file') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <label for="date">Date of Payment</label>
                                <input type="date" name="date" value="{{ old('date') }}" class="form-control" required>
                                @if ($errors->has('date'))
                                    <div class="text-danger small mt-1">{{ $errors->first('date') }}</div>
                                @endif
                            </div>
                        
                            <div class="col-12">
                                @if($locations->count() <= 0)
                                    <input type="hidden" name="location" id="location">
                                @else
                                <select name="location" id="location" class="form-select">
                                    <option value="">-- Select Location --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('location') == $location->id ? 'selected' : '' }}>
                                            {{ $location->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @endif
                                @if ($errors->has('location'))
                                    <div class="text-danger small mt-1">{{ $errors->first('location') }}</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Area End -->
@endsection
