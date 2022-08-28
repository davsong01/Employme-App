<?php
    $logo = \App\Settings::first()->value('logo');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ogani Template">
    <meta name="keywords" content="Ogani, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($logo)}}">

    <title>@yield('title')</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset('contai/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/elegant-icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/nice-select.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/jquery-ui.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/owl.carousel.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/slicknav.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('contai/css/style.css') }}" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Humberger Begin -->
    <div class="humberger__menu__overlay"></div>
    <div class="humberger__menu__wrapper">
       
        <div class="humberger__menu__logo">
            <a href="#"><img src="{{ asset($logo)}}" alt=""></a>
        </div>
       
        <nav class="humberger__menu__nav mobile-menu">
            @include('layouts.contai.nav')
        </nav>
        <div id="mobile-menu-wrap"></div>
    </div>
    <!-- Humberger End -->

    <!-- Header Section Begin -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-2">
                    <div class="header__logo">
                        <a href="/"><img src="{{ asset($logo)}}" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <nav class="header__menu">
                        @include('layouts.contai.nav')
                    </nav>
                </div>
                @if(!empty(\Session::get('facilitator')))
                <div class="col-lg-2">
                    <div class="header__cart">
                        <div class="hero__search__phone">
                            <div class="hero__search__phone__icon">
                                <img src="{{ asset('contai/img/wtnlogo.jpeg') }}" alt="">
                            </div>
                           
                            <div class="hero__search__phone__text">
                                <h6>{{ \Session::get('facilitator_license') }}</h6>
                            </div>
                            
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="humberger__open">
                <i class="fa fa-bars"></i>
            </div>
            <div class="hero__search">
                <div class="hero__search__form">
                    <form action="/" method="get">
                        <div class="hero__search__categories">
                            Search course
                            <span class="arrow_carrot-right"></span>
                        </div>
                        <input type="text" name="search" value="{{ old('search') }}" placeholder="Search for a course">
                        <button type="submit" class="site-btn">SEARCH</button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <!-- Header Section End -->
    <section class="breadcrumb-section set-bg" data-setbg="{{ asset('contai/img/breadcrumb.jpg')}}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>EMPLOYME NG Course Portal</h2>
                        <div class="breadcrumb__option">
                            @if(!empty(\Session::get('facilitator')))
                            <span>Instructor {{ \Session::get('facilitator_name') }}'s page</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @yield('content')
    <?php $setting = App\Settings::first() ?>
  
    <!-- Footer Section Begin -->
    <footer class="footer spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h6>About</h6>
                        <ul>
                            <li>{!! $setting->ADDRESS_ON_RECEIPT !!}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 offset-lg-1">
                    <div class="footer__widget">
                        <h6>Useful Links</h6>
                        <ul>
                            @if(isset($setting->about_link))
                            <li><a href="{{ $setting->about_link }}">About Us</a></li>
                            @endif
                            @if(isset($setting->contact_link))
                            <li><a href="{{ $setting->contact_link }}">Contact Us</a></li>
                            @endif
                            @if(isset($setting->privacy_link))
                            <li><a href="{{ $setting->privacy_link }}">Privacy Policy</a></li>
                            @endif
                            @if(isset($setting->tac_link))
                            <li><a href="{{ $setting->tac_link }}">Terms and Conditions</a></li>
                            @endif
                        </ul>
                       
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="footer__widget">
                        <h6>Contact us</h6>
                        <div class="footer__widget__social">
                             <li>Phone: {{ $setting->phone }}</li>
                            <li>Email: {{ $setting->OFFICIAL_EMAIL }}</li>
                        </div>
                    </div>
                    <div class="footer__widget">
                        @if(isset($setting->facebook_link) || isset($setting->instagram_link) || isset($setting->twitter_link))
                        <h6>Follow us on social media</h6>
                        <div class="footer__widget__social">
                            @if(isset($setting->facebook_link))
                            <a href="{{ $setting->facebook_link }}" target="_blank"><i class="fa fa-facebook"></i></a>
                            @endif
                            @if(isset($setting->instagram_link))
                            <a href="{{ $setting->instagram_link }}" target="_blank"><i class="fa fa-instagram"></i></a>
                            @endif
                            @if(isset($setting->twitter_link))
                            <a href="{{ $setting->twitter_link }}" target="_blank"><i class="fa fa-twitter"></i></a>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer__copyright">
                        <div class="footer__copyright__text">
                            <p>
                               
                                Copyright &copy;<script>
                                    document.write(new Date().getFullYear());
                                </script> All rights reserved | Designed by <a href="https://www.techdaves.com/"
                                    target="_blank">Techdave</a>
                            </p>
                        </div>
                        <div class="footer__copyright__payment"><img src="{{ asset('contai/img/payment-item.png')}}" alt=""></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="{{ asset('contai/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{ asset('contai/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('contai/js/jquery.nice-select.min.js')}}"></script>
    <script src="{{ asset('contai/js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('contai/js/jquery.slicknav.js')}}"></script>
    <script src="{{ asset('contai/js/mixitup.min.js')}}"></script>
    <script src="{{ asset('contai/js/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('contai/js/main.js')}}"></script>
</body>

</html>