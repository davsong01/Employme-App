<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EMPLOYMENG') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/inc/css/popup.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                            <img src = "{{ asset('assets/images/elearninglogo.jpg') }}" style="display:block; margin-left: auto; margin-right: auto; width:50%; margin-top:10px">
                    </div>
                </div>
               
            </div>     
    </div>
        <main class='py-4 container'>
            @include('layouts.partials.alerts');
        </main>
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script  src="{{ asset('assets/inc/js/popup.js') }}"></script>
</body>
</html>
