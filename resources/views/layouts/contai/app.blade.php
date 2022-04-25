<?php
    $logo = \App\Settings::first()->value('logo');
?>
<!DOCTYPE html>
<html lang="zxx">

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
                                <i class="fa fa-phone"></i>
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
                    <form action="#">
                        <div class="hero__search__categories">
                            Search course
                            <span class="arrow_carrot-right"></span>
                        </div>
                        <input type="text" placeholder="Search for a course">
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

    <!-- Footer Section Begin -->
    <footer class="footer spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        {{-- <div class="footer__about__logo">
                            <a href="/"><img src="{{ asset($logo)}}" alt=""></a>
                        </div> --}}
                        <h6>About</h6>
                        <ul>
                            <li>Address: 60-49 Road 11378 New York</li>
                            <li>Phone: +65 11.188.888</li>
                            <li>Email: hello@colorlib.com</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 offset-lg-1">
                    <div class="footer__widget">
                        <h6>Useful Links</h6>
                        <ul>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Contact Us</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms and Conditions</a></li>
                        </ul>
                       
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="footer__widget">
                        <h6>Follow us on social media</h6>
                        <div class="footer__widget__social">
                            <a href="#"><i class="fa fa-facebook"></i></a>
                            <a href="#"><i class="fa fa-instagram"></i></a>
                            <a href="#"><i class="fa fa-twitter"></i></a>
                        </div>
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