<?php
    $logo = \App\Settings::first()->value('logo');
    $settings = \App\Settings::first();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta property="og:url" content="<?php echo e(url('/')); ?>">
    <meta property="og:title" content="<?php echo e(config('app.name')); ?>"> 
    <meta property="og:image" content="<?php echo e(asset($logo)); ?>"/> 
    <meta property="og:description" content="<?php echo e(config('app.name').' Learning Portal'); ?>"/> 
    <meta name="description" content="<?php echo e(config('app.name').' Learning Portal'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset($logo)); ?>">

    <title><?php echo $__env->yieldContent('title'); ?></title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/bootstrap.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/font-awesome.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/elegant-icons.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/nice-select.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/jquery-ui.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/owl.carousel.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/slicknav.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('contai/css/style.css')); ?>" type="text/css">

    <style>
        .pay-option{
            width:auto !important;
            border:0px;
            border-radius:10px;
            height:55px;
            background-position: center;
            border: black solid 0.9px;
            font-weight: normal;
        }
        .checkout__order button {
            font-weight: lighter;
        }
        .checkout__form h4 {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 10px;
            margin-top: 25px;
        }
        .checkout__order button {
            font-weight: normal;
            padding: 12px;
        }
        .whatsapp_float {
          position: fixed;
          width: 60px;
          height: 60px;
          bottom: 40px;
          right: 40px;
          background-color: #25d366;
          color: #FFF;
          border-radius: 50px;
          text-align: center;
          font-size: 30px;
          box-shadow: 2px 2px 3px #999;
          z-index: 100;
      }

      .whatsapp-icon {
          margin-top: 16px;
      }

      /* for mobile */
      @media  screen and (max-width: 767px) {
          .whatsapp-icon {
              margin-top: 10px;
          }

          .whatsapp_float {
              width: 40px;
              height: 40px;
              bottom: 20px;
              right: 10px;
              font-size: 22px;
          }
      }

    </style>
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
            <a href="#"><img src="<?php echo e(asset($logo)); ?>" alt=""></a>
        </div>
       
        <nav class="humberger__menu__nav mobile-menu">
            <?php echo $__env->make('layouts.contai.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                        <a href="/"><img src="<?php echo e(asset($logo)); ?>" alt=""></a>
                    </div>
                </div>
                <div class="col-lg-8">
                    <nav class="header__menu">
                        <?php echo $__env->make('layouts.contai.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </nav>
                </div>
                <?php if(!empty(\Session::get('facilitator'))): ?>
                <div class="col-lg-2">
                    <div class="header__cart">
                        <div class="hero__search__phone">
                            <div class="hero__search__phone__icon">
                                <img src="<?php echo e(asset('contai/img/wtnlogo.jpeg')); ?>" alt="">
                            </div>
                           
                            <div class="hero__search__phone__text">
                                <h6><?php echo e(\Session::get('facilitator_license')); ?></h6>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
                        <input type="text" name="search" value="<?php echo e(old('search')); ?>" placeholder="Search for a course">
                        <button type="submit" class="site-btn">SEARCH</button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <!-- Header Section End -->
    <section class="breadcrumb-section set-bg" data-setbg="<?php echo e(asset('contai/img/breadcrumb.jpg')); ?>">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>EMPLOYME NG Course Portal</h2>
                        <div class="breadcrumb__option">
                            <?php if(!empty(\Session::get('facilitator'))): ?>
                            <span>Instructor <?php echo e(\Session::get('facilitator_name')); ?>'s page</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->yieldContent('content'); ?>
    <?php $setting = App\Settings::first() ?>
  
    <!-- Footer Section Begin -->
    <footer class="footer spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h6>About</h6>
                        <ul>
                            <li><?php echo $setting->ADDRESS_ON_RECEIPT; ?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6 offset-lg-1">
                    <div class="footer__widget">
                        <h6>Useful Links</h6>
                        <ul>
                            <?php if(isset($setting->about_link) && !empty($setting->about_link)): ?>
                            <li><a href="<?php echo e($setting->about_link); ?>">About Us</a></li>
                            <?php endif; ?>
                            <?php if(isset($setting->contact_link) && !empty($setting->contact_link)): ?>
                            <li><a href="<?php echo e($setting->contact_link); ?>">Contact Us</a></li>
                            <?php endif; ?>
                            <?php if(isset($setting->privacy_link) && !empty($setting->privacy_link)): ?>
                            <li><a href="<?php echo e($setting->privacy_link); ?>">Privacy Policy</a></li>
                            <?php endif; ?>
                            <?php if(isset($setting->tac_link) && !empty($setting->tac_link)): ?>
                            <li><a href="<?php echo e($setting->tac_link); ?>">Terms and Conditions</a></li>
                            <?php endif; ?>
                        </ul>
                       
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="footer__widget">
                        <h6>Contact us</h6>
                        <div class="footer__widget__social">
                            <?php if(isset($setting->phone) && !empty($setting->phone)): ?>
                            <li>Phone: <?php echo e($setting->phone); ?></li>
                            <?php endif; ?>
                            <?php if(isset($setting->OFFICIAL_EMAIL) && !empty($setting->OFFICIAL_EMAIL)): ?>
                            <li>Email: <?php echo e($setting->OFFICIAL_EMAIL); ?></li>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="footer__widget">
                        <?php if(isset($setting->facebook_link) || isset($setting->instagram_link) || isset($setting->twitter_link)): ?>
                        <h6>Follow us on social media</h6>
                        <div class="footer__widget__social">
                            <?php if(isset($setting->facebook_link)): ?>
                            <a href="<?php echo e($setting->facebook_link); ?>" target="_blank"><i class="fa fa-facebook"></i></a>
                            <?php endif; ?>
                            <?php if(isset($setting->instagram_link)): ?>
                            <a href="<?php echo e($setting->instagram_link); ?>" target="_blank"><i class="fa fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if(isset($setting->twitter_link)): ?>
                            <a href="<?php echo e($setting->twitter_link); ?>" target="_blank"><i class="fa fa-twitter"></i></a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
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
                        <div class="footer__copyright__payment"><img src="<?php echo e(asset('contai/img/payment-item.png')); ?>" alt=""></div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Footer Section End -->
    <?php if($settings->allow_whatsapp_chat == 'yes'): ?>
    <a href="https://wa.me/2348037067223" class="whatsapp_float" target="_blank" rel="noopener noreferrer">
        <i class="fa fa-whatsapp whatsapp-icon"></i>
    </a>
    <?php endif; ?>
    <!-- Js Plugins -->
    <script src="<?php echo e(asset('contai/js/jquery-3.3.1.min.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/jquery.nice-select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/jquery-ui.min.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/jquery.slicknav.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/mixitup.min.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/owl.carousel.min.js')); ?>"></script>
    <script src="<?php echo e(asset('contai/js/main.js')); ?>"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
    
</body>

</html><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/layouts/contai/app.blade.php ENDPATH**/ ?>