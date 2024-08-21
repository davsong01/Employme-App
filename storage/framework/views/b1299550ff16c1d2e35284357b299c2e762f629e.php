
<!DOCTYPE html>
<html dir="ltr" lang="<?php echo e(app()->getLocale()); ?>">
<title><?php echo $__env->yieldContent('title'); ?></title>

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Welcome')); ?></title>
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

    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset(\App\Settings::value('favicon'))); ?>">
    <title><?php echo e(config('app.name')); ?></title>
    <!-- Custom CSS -->
    <link href="<?php echo e(asset('assets/libs/fullcalendar/dist/fullcalendar.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/extra-libs/calendar/calendar.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('dist/css/style.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/libs/select2/dist/css/select2.min.css')); ?>">
    <?php echo $__env->yieldContent('css'); ?>


    <!---include font awesome-->
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
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
        @keyframes  blinkingText {
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
        <?php if($colors->primary_color): ?>
            <style>
                #navbarSupportedContent, #main-wrapper .left-sidebar[data-sidebarbg=skin5], #main-wrapper .left-sidebar[data-sidebarbg=skin5] ul ,.btn-primary {
                    background: <?php echo $colors->primary_color ?> !important;
                }
                .btn-primary {
                    border: <?php echo $colors->primary_color ?> !important;
                }
            </style>
        <?php endif; ?>
        <?php if($colors->secondary_color): ?>
            <style>
                .btn-primary:hover{
                    background: <?php echo $colors->secondary_color ?> !important;
                    border: <?php echo $colors->secondary_color ?> !important;
                }
                .sidebar-nav ul .sidebar-item.selected>.sidebar-link{
                    background: <?php echo $colors->secondary_color ?> !important;
                }
            </style>
        <?php endif; ?>
        <header class="topbar" data-navbarbg="skin5">
            <?php echo $__env->make('dashboard.layouts.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </header>
        <?php echo $__env->yieldContent('dashboard'); ?>

        <div class="page-wrapper">

            <?php echo $__env->yieldContent('content'); ?>

            <?php echo $__env->make('dashboard.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>

    </div>

    <script src="<?php echo e(asset('dist/js/jquery.ui.touch-punch-improved.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/js/jquery-ui.min.js')); ?>"></script>

    <!-- Bootstrap tether Core JavaScript -->
    <script src="<?php echo e(asset('assets/libs/popper.js/dist/umd/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/libs/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?php echo e(asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/extra-libs/sparkline/sparkline.js')); ?>"></script>
    <!--Wave Effects -->
    <script src="<?php echo e(asset('dist/js/waves.js')); ?>"></script>
    <!--Menu sidebar -->
    <script src="<?php echo e(asset('dist/js/sidebarmenu.js')); ?>"></script>
    <!--Custom JavaScript -->
    <script src="<?php echo e(asset('dist/js/custom.min.js')); ?>"></script>
    <!-- this page js -->
    
    <script src="<?php echo e(asset('assets/libs/fullcalendar/dist/fullcalendar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/js/pages/calendar/cal-init.js')); ?>"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>


    <!--Script for States and LGA Dropdown-->
    <script src="<?php echo e(asset('dist/js/lga.min.js')); ?>"></script>
    <!--End of states and LGA script-->

    <!--Select 2-->
    <script src="<?php echo e(asset('assets/libs/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/libs/select2/dist/js/select2.min.js')); ?>"></script>

    <script>
        $('#zero_config').DataTable({
             "ordering": true,
              "pageLength": 100,
              "lengthMenu": [ 100, 250, 500, 1000, 2000, "All" ],
               "scrollY": true,
                // "scrollX": true,
                "scroller": true
        });
        
        $('#transTable').DataTable({
             "ordering": false,
            //   "pageLength": 100,
               "scrollY": true,
                "scrollX": true,
                "scroller": true
        });
        
        // For select 2
        //***********************************//
        $(".select2").select2();


        $(document).ready( function () {
         $('#myTable').DataTable( {
             "ordering": false,
              "pageLength": 50,
               "scrollY": true,
                "scrollX": true,
                "scroller": true
        } );   
        // $('#myTable').DataTable();
        } );
        

        $(".delete").on("submit", function () {
            return confirm("Are you sure?");
        });
    </script>
    <?php echo $__env->yieldContent('extra-scripts'); ?>
    
</body><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/layouts/main.blade.php ENDPATH**/ ?>