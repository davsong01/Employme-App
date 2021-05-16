<!DOCTYPE html>
<html dir="ltr" lang="{{ app()->getLocale() }}">
<title>@yield('title')</title>

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Welcome') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">
    <!--Calender links-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css" />
    <!--Working Datatables-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">
    <title>{{ config('app.name') }}</title>
    <!-- Custom CSS -->
    <link href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/extra-libs/calendar/calendar.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    @yield('css')


    <!---include font awesome-->
    <script src='https://kit.fontawesome.com/a076d05399.js'></script>

    <!--Include CK Editor-->
    <script src="https://cdn.ckeditor.com/4.14.0/standard-all/ckeditor.js"></script>

    <!---Calender scripts-->
    <script src="https://code.jquery.com/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>


    <script type="text/javascript">
        function display_c() {
            var refresh = 1000; // Refresh rate in milli seconds
            mytime = setTimeout('display_ct()', refresh)
        }

        function display_ct() {
            var x = new Date()
            var x1 = x.toUTCString(); // changing the display to UTC string
            document.getElementById('ct').innerHTML = x1;
            tt = display_c();
        }

    </script>

    <style>
        .progress2 { position:relative; width:100%; }

        .bar2 { background-color: #00ff00; width:0%; height:20px; }

        .percent2 { position:absolute; display:inline-block !important; left:50%; color: #040608;}

        .selecttraining{
            display:none;
        }

        .selectedemail{
            display:none;
        }

        .bulkemail{
            display:none;
        }

        .bulkrecipients{
            display:none;
        }

        .trix-toolbar .trix-button-row {
            display: flex;
            flex-wrap: wrap !important;
            justify-content: space-between;
        }
        .help-block {
            color: red !important;
        }

        .custombutton {
            color: #2962FF !important;
            background: none !important;
            border: none !important;
            cursor: url(cursor1.png) 4 12, auto !important;
        }

        .custombutton:focus,
        .custombutton:hover {
            cursor: pointer !important;
            color: #2962FF !important
        }

        .blinking {
            animation: blinkingText 2s infinite;
        }

        @keyframes blinkingText {
            0% {
                color: white;
            }

            49% {
                color: white;
            }

            50% {
                color: white;
            }

            99% {
                color: transparent;
            }

            100% {
                color: white;
            }
        }

        .top-container {
  background-color: #f1f1f1;
  padding: 30px;
  text-align: center;
}

.header {
  padding: 10px 16px;
  background: red;
  color: #f1f1f1;
}

.content {
  padding: 16px;
}

.sticky {
  position: fixed;
  top: 0;
  width: 100%;
}

.sticky + .content {
  padding-top: 102px;
}

</style>
</head>
<!--Preloader-->


<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        @if($colors->primary_color)
            <style>
                #navbarSupportedContent, #main-wrapper .left-sidebar[data-sidebarbg=skin5], #main-wrapper .left-sidebar[data-sidebarbg=skin5] ul ,.btn-primary {
                    background: @php echo $colors->primary_color @endphp !important;
                }
                .btn-primary {
                    border: @php echo $colors->primary_color @endphp !important;
                }
            </style>
        @endif
        @if ($colors->secondary_color)
            <style>
                .btn-primary:hover{
                    background: @php echo $colors->secondary_color @endphp !important;
                    border: @php echo $colors->secondary_color @endphp !important;
                }
                .sidebar-nav ul .sidebar-item.selected>.sidebar-link{
                    background: @php echo $colors->secondary_color @endphp !important;
                }
            </style>
        @endif
        <header class="topbar" data-navbarbg="skin5">
            @include('dashboard.layouts.nav')
        </header>
        @yield('dashboard')

        <div class="page-wrapper">

            @yield('content')

            @include('dashboard.layouts.footer')

        </div>

    </div>


    <!--Scripts-->
    <!--problmatic script-->
    {{-- <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script> --}}
    <!--End of problmatic script-->

    <script src="{{ asset('dist/js/jquery.ui.touch-punch-improved.js') }}"></script>
    <script src="{{ asset('dist/js/jquery-ui.min.js') }}"></script>

    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/sparkline/sparkline.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('dist/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('dist/js/sidebarmenu.js') }}"></script>
    <!--Custom JavaScript -->
    <script src="{{ asset('dist/js/custom.min.js') }}"></script>
    <!-- this page js -->
    {{-- <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}../"></script> --}}
    <script src="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('dist/js/pages/calendar/cal-init.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>


    <!--Script for States and LGA Dropdown-->
    <script src="{{asset('dist/js/lga.min.js')}}"></script>
    <!--End of states and LGA script-->

    <!--Select 2-->
    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>

    <script>
        $('#zero_config').DataTable({
             "pageLength": 50
        });
        // For select 2
        //***********************************//
        $(".select2").select2();


        $(document).ready( function () {
        $('#myTable').DataTable();
        } );

        $(".delete").on("submit", function () {
            return confirm("Are you sure?");
        });
    </script>
    @yield('extra-scripts')
</body>