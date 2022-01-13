@extends('dashboard.student.trainingsindex')
@section('css')
<style>
    a:hover,a:focus{
    text-decoration: none;
    outline: none;
}
#accordion .panel{
    border: none;
    border-radius: 0;
    box-shadow: none;
    margin: 0 20px 5px 0;
    position: relative;
}
#accordion .panel-heading{
    padding: 0;
    border: none;
    border-radius: 0;
    margin-bottom: 0;
    position: relative;
}
#accordion .panel-heading:before{
    content: "";
    display: block;
    width: 100%;
    height: 100%;
    background: #854e1f;
    position: absolute;
    top: 0;
    left: 0;
    transform: skew(-30deg, 0deg);
    transform-origin: left bottom 0;
}
#accordion .panel-title a{
    display: block;
    padding: 15px 70px 15px 20px;
    margin: 0;
    background: #854e1f;
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    letter-spacing: 1px;
    border-radius: 0;
    position: relative;
}
#accordion .panel-title a:after,
#accordion .panel-title a.collapsed:after{
    content: "\f068";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    width: 55px;
    height: 100%;
    line-height: 50px;
    text-align: center;
    position: absolute;
    top: 0;
    right: 0;
}
#accordion .panel-title a.collapsed:after{ content: "\f067"; }
#accordion .panel-body{
    padding: 15px 20px;
    margin: 5px 20px 0;
    /* background: #0aa5c1; */
    
    border: none;
    outline: 2px solid #fff;
    outline-offset: -8px;
    font-size: 17px;
    color: #fff;
    line-height: 27px;
}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">
 
.testimonial{
    text-align: center;
    margin: 20px 30px 40px;
}
.testimonial .pic{
    display: inline-block;
    width: 100px;
    height: 100px;
    border-radius: 25px;
    border: 4px solid #6b2014;
    box-shadow: 0 7px rgba(0, 0, 0, 0.1), 0 5px #e4ac01;
    margin-bottom: 15px;
    overflow: hidden;
}
.testimonial .pic img{
    width: 100%;
    height: auto;
}
.testimonial .description{
    padding: 0 20px 20px;
    font-size: 15px;
    color: #333;
    line-height: 30px;
    border-radius: 25px;
    border-bottom: 4px solid #6b2014;
    box-shadow: 0 7px rgba(0, 0, 0, 0.1), 0 5px #e4ac01;
    margin-bottom: 25px;
}
.testimonial .title{
    display: block;
    margin: 0 0 7px 0;
    font-size: 20px;
    font-weight: 600;
    color: #6b2014;
    letter-spacing: 1px;
    text-transform: uppercase;
}
.testimonial .post{
    display: block;
    font-size: 15px;
    color: #e4ac01;
    text-transform: capitalize;
}
.owl-theme .owl-controls{ margin-top: 0; }
.owl-theme .owl-controls .owl-page span{
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: #6b2014;
    opacity: 0.8;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.45);
    transition: all 0.3s ease 0s;
}
.owl-theme .owl-controls .owl-page.active span{ background: #e4ac01; }
@media only screen and (max-width: 479px){
    .testimonial{ padding: 20px 15px 40px; }
    .testimonial .description{ padding: 0 5px 20px; }
}
</style>
@endsection
@section('title', 'Select Facilitator' )
@section('content')
@if(is_null(auth()->user()->facilitator_id))
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4>Select your preferred facilitator for {{ $program->p_name }} from the list of our available  facilitators</h4>
                        <p>Please note that, You can only do this once</p>
                    </div>
                        @foreach($facilitators as $facilitator)
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
               
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingThree">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree{{ $facilitator->id }}" aria-expanded="false" aria-controls="collapseThree">
                                            {{ $facilitator->name }}
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseThree{{ $facilitator->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                    <div class="panel-body">
                                        {{-- <div class="row">
                                            <div class="col-md-12"> --}}
                                                <div id="testimonial-slider" class="owl-carousel">
                                                    <div class="testimonial">
                                                        <div class="pic">
                                                            <img src="{{ asset('profiles/'. $facilitator->profile_picture  )}}" alt="{{$facilitator->profile_picture }}">
                                                        </div>
                                                        <div class="description">
                                                            {!! $facilitator->profile !!}
                                                                <form action="{{ route('savefacilitator') }}" method="POST">
                                                                    {{ csrf_field() }}
                                                                    <input type="hidden" name="facilitator_id" value="{{ $facilitator->id }}" required>
                                                                    <input required type="hidden" name="program_id" value="{{ $program->id }}">
                                                                    <button class="btn btn-dark" style="width: 100%;">Select facilitator</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                            {{-- </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            
        </div>
    </div>
</div>
@endif
@endsection