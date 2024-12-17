<!DOCTYPE html>
<html dir="ltr" lang="{{ app()->getLocale() }}">
<title>@yield('title')</title>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Welcome') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js"></script>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset(\App\Models\Settings::value('favicon')) }}">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('assets/libs/fullcalendar/dist/fullcalendar.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/extra-libs/calendar/calendar.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/style.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modal.css') }}" />

    <style>
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table-image {
            width: 85px;
            border-radius: 5px;
            object-fit: cover;
        }
        .btn {
            border-radius: 5px;
            margin: 2px 0;
        }

        .actions-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .export-link {
            color: brown;
            font-weight: bold;
        }

        .export-link:hover {
            text-decoration: underline;
            color: darkred;
        }

        .dropdown {
            position: relative;
            display: block;
        }
        .dropdown-button {
            background-color: #17a2b8;
            color: white;
            padding: 4px 4px;
            font-size: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dropdown-button:hover {
            background-color: #138496; /* Slightly darker shade for hover */
        }
        /* Dropdown content (hidden by default) */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        /* Links inside the dropdown */
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        /* Change color of dropdown links on hover */
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show the dropdown content when the button is clicked */
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>

    @yield('css')
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>

    <script src="https://code.jquery.com/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>

    <script type="text/javascript">
        function display_c() {
            var refresh = 1000;
            mytime = setTimeout('display_ct()', refresh)
        }

        function display_ct() {
            var x = new Date()
            var x1 = x.toUTCString();
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

        .pay-option{
            width:auto !important;
            border:0px;
            border-radius:10px;
            background-position: center;
            border: black solid 0.9px;
            font-weight: normal;
            padding: 25px;
            margin: 10px;
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

        .select2-container--default .select2-selection--multiple {
            width: 100% !important; /* Force full width */
        }

        .select2-container {
            width: 100% !important; /* Force full width */
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: black; /* Text color for selected items */
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            color: black; /* Text color for the rendered selections */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: black; /* Text color for the single selected item */
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: black; /* Text color for the placeholder */
        }

        .select2-container--default .select2-results__option {
            color: black; /* Text color for the dropdown options */
        }
        
        .badge {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 45px;
            height: 45px;
            background-color: #4CAF50;
            border-radius: 50%;
            color: white;
            font-size: 10px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .transaction-count {
            text-align: center;
        }
        .search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 20px;
        }

        .rounded {
            border-radius: 20px !important;
        }
        .btn-search {
            border-radius: 20px;
            transition: background-color 0.3s;
        }
        .btn-search:hover {
            background-color: #0056b3;
        }

        .btn.active {
            background-color: #0056b3;
            color: white;
            border: 4px solid black;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            transform: scale(1.05); 
            transition: all 0.3s;
        }

        .btn:not(.active):hover {
            transform: scale(1.05); 
        }

        .button-container .btn {
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease; 
        }

        .button-container .btn:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button-container .btn:disabled {
            opacity: 0.6;
        }

        .button-container .fa-unlock {
            margin-right: 0.25rem; 
        }

        .select2-container--default .select2-selection--multiple {
            line-height: 27px;
            overflow: scroll;
            height: 150px;
        }
        .view {
            margin: 0 10px;
            border-radius: 10%;
        }
        fieldset {
            border: 1px solid #ddd;
            padding: 10px 15px;
            margin-bottom: 15px;
        }
        legend {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #0056b3;
        }
        /* Optional: Custom styling for checkboxes */
        .permission-checkbox {
            transform: scale(1.1); /* Slightly enlarge checkboxes */
        }

        /* Optional: Style for the parent group header */
        .permissions-container .h5 {
            font-weight: 600; /* Make parent headings slightly bolder */
            margin-bottom: 10px;
        }

        .select-all-permissions + label {
            font-size: 0.9rem;
            color: #6c757d; /* Muted text for a professional look */
        }

        /* Optional: Add hover effect for labels */
        .form-check-label:hover {
            color: #0056b3; /* Hover effect for better interactivity */
            cursor: pointer;
        }

        .rounded2{
            border-radius: 5px !important;
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper">
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

    <script src="{{ asset('dist/js/jquery.ui.touch-punch-improved.js') }}"></script>
    <script src="{{ asset('dist/js/jquery-ui.min.js') }}"></script>

    <script src="{{ asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/sparkline/sparkline.js') }}"></script>
    <script src="{{ asset('dist/js/waves.js') }}"></script>
    <script src="{{ asset('dist/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('dist/js/custom.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>

    <script src="{{asset('dist/js/lga.min.js')}}"></script>
    {{-- <script src="{{ asset('ecommerce/js/popper.min.js') }}"></script> --}}

    <script src="{{ asset('assets/libs/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>

    <script>
        $('#zero_config').DataTable({
            "ordering": true,
            "pageLength": 100,
            "lengthMenu": [
                [100, 250, 500, 1000, 2000, -1], 
                [100, 250, 500, 1000, 2000, "All"] 
            ],

            "scrollX": true,   
            "scroller": true,  
            "responsive": true,
            "autoWidth": false 
        });

        $('#transTable').DataTable({
            "ordering": false,
            "scrollY": true,
            "scrollX": true,
            "scroller": true
        });
        
        $(".select2").select2();

        $(document).ready( function () {
            $('#myTable').DataTable( {
                "ordering": false,
                "pageLength": 50,
                "scrollY": true,
                    "scrollX": true,
                    "scroller": true
            } );   
        } );
        
        $(".delete").on("submit", function () {
            return confirm("Are you sure?");
        });
    </script>
    @yield('extra-scripts')
    <script>
        CKEDITOR.replace("ckeditor", {
                uiColor: '#9AB8F3'
            });
        };
    </script>
</body>