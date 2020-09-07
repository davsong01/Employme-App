<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>@yield('title')</title>

    <!-- Favicon  -->
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('assets/images/favicon.png') }}">

    <!-- Core Style CSS -->
    <link rel="stylesheet" href="{{ asset('ecommerce/css/core-style.css') }}">
    <link rel="stylesheet" href="{{ asset('ecommerce/style.css') }}">

</head>

<body>
    <!-- Search Wrapper Area Start -->
    <div class="search-wrapper section-padding-100">
        <div class="search-close">
            <i class="fa fa-close" aria-hidden="true"></i>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="search-content">
                        <form action="{{ route('welcome') }}" method="GET">
                            <input type="search" name = "search" value="{{ request()->query('search') }}" placeholder="Type your keyword...">
                            <button type="submit"><img src="img/core-img/search.png" alt=""></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Search Wrapper Area End -->

    <!-- ##### Main Content Wrapper Start ##### -->
    <div class="main-content-wrapper d-flex clearfix">

        <!-- Mobile Nav (max width 767px)-->
        <div class="mobile-nav">
            <!-- Navbar Brand -->
            <div class="amado-navbar-brand">
                <a href="/"><img src="{{ asset('assets/images/logo-text.png') }}"
                        alt="logo" class="light-logo" /> </a>
            </div>
            <!-- Navbar Toggler -->
            <div class="amado-navbar-toggler">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Header Area Start -->
        <header class="header-area clearfix">
            <!-- Close Icon -->
            <div class="nav-close">
                <i class="fa fa-close" aria-hidden="true"></i>
            </div>
            <!-- Logo -->
            <div class="logo">
                <a href="/"><img src="{{ asset('assets/images/logo-text.png') }}"
                        alt="logo" class="light-logo" /> </a>
            </div>
            <!-- Amado Nav -->
            <nav class="amado-nav">
                <ul>
                    <li class="active"><a href="/">All Trainings</a></li>
                    <li><a href="#" class="search-nav"><img src="img/core-img/search.png" alt="">Search Training</a></li>
                    <li><a href="/login">My Account</a></li>
               
                </ul>
            </nav>          
           
        </header>
        <!-- Header Area End -->

        <!-- Product Catagories Area Start -->
        @yield('content')
        
        <!-- Product Catagories Area End -->
    </div>
    <!-- ##### Main Content Wrapper End ##### -->

    <!-- ##### Footer Area Start ##### -->
    <footer style="background:black; padding:30px">
        <div class="container">
            <div class="row align-items-center">
                <!-- Single Widget Area -->
                <div class="col-12 col-lg-12">
                    <div class="single_widget_area">                        
                        <!-- Copywrite Text -->
                        <p class="copywrite" style="text-align: center;">
                            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                            Copyright &copy;<script>
                                document.write(new Date().getFullYear());
                            </script> All rights reserved | Site by <a style="color:white; font-size:16px" href="https://techdaves.com" target="_blank">Techdaves</a>
                            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </footer>
    <!-- ##### Footer Area End ##### -->

    <!-- ##### jQuery (Necessary for All JavaScript Plugins) ##### -->
    <script src="{{ asset('ecommerce/js/jquery/jquery-2.2.4.min.js') }}"></script>
    <!-- Popper js -->
    <script src="{{ asset('ecommerce/js/popper.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('ecommerce/js/bootstrap.min.js') }}"></script>
    <!-- Plugins js -->
    <script src="{{ asset('ecommerce/js/plugins.js') }}"></script>
    <!-- Active js -->
    <script src="{{ asset('ecommerce/js/active.js') }}"></script>

</body>

</html>