@extends('layouts.contai.app')
@section('title')
    {{ config('app.name') }} - Upload POP
@endsection
@section('content')
<section class="checkout spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12">
                @include('layouts.partials.alerts')
            </div>
        </div>
        <div class="checkout__form">
            <h4>Upload Proof of Payment</h4>
            <form action="{{ route('pop.store') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    
                    <div class="col-lg-12 col-md-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Name<span>*</span></p>
                                     <input type="text" class="form-control" id="name" name="name" 
                                        @auth
                                        value="{{ auth()->user()->name }}"  
                                        placeholder="Full Name"
                                        @endauth

                                        @guest 
                                        value="{{ old('name') }}" placeholder="Full Name"  
                                        @endguest required>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Email<span>*</span></p>
                                    <input type="email" id="email" name="email" 
                                        @auth
                                        value="{{ auth()->user()->email }}"  
                                        @endauth
                                        @guest 
                                        value="{{ old('email') }}" placeholder="Enter email"  
                                        @endguest required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Phone<span>*</span></p>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                        @auth
                                        value="{{ auth()->user()->phone }}"  
                                        
                                        @endauth

                                        @guest 
                                        value="{{ old('phone') }}"  
                                        @endguest required>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Select Course<span>*</span></p>
                                    <select name="training" id="training" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        @foreach($trainings as $training)
                                        <option value="{{ $training->id }}">{{ $training->p_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Bank paid into<span>*</span></p>
                                    <select name="bank" id="bank" class="form-control">
                                        <option value="">-- Select bank --</option>
                                        <option value="Access">Access</option>
                                        <option value="GTB">GTB</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Amount<span>*</span></p>
                                    <input type="number" class="form-control" name="amount" id="amount" value="{{ old('amount') }}" min=1 required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Upload proof of payment<span>*</span></p>
                                    <input type="file" class="form-control" name="file" id="file" value="{{ old('file') }}" required>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="checkout__input">
                                    <p>Date of payment<span>*</span></p>
                                    <input type="date" class="form-control" name="date" id="date" value="{{ old('date') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <button type="submit" class="site-btn checkout-button">UPLOAD</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection