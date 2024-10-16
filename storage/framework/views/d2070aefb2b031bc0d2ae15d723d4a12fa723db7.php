<nav class="navbar top-navbar navbar-expand-md navbar-dark">
    <div class="navbar-header" data-logobg="skin5">
        <!-- This is for the sidebar toggle which is visible on mobile only -->
        <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                class="ti-menu ti-close"></i></a>

        <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
            <!-- Logo icon -->
            <b class="logo-icon p-l-10"></b>
            <span class="logo-text">
                <img src="<?php echo e(asset(\App\Models\Settings::value('logo'))); ?>" alt="homepage" class="light-logo" />
            </span>
        </a>
        <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
            data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
    </div>
    <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
        <ul class="navbar-nav float-left mr-auto">
            <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light"
                    href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a>
            </li>

        </ul>
        <li class="navbar-nav" style="color:white">

            <body onload=display_ct();>
                <p id="ct" style="margin-top:20px; margin-left:5px"></p>
        </li>
        <li class="navbar-nav" style="color:white;margin-left:5px"><b style="color:yellow">| </b>Welcome,
            <?php echo e(Auth::user()->name); ?> <b style="color:yellow"> |</b> </li>
        <?php if((\Request::route()->getPrefix() != '/company')): ?>
        <li class="nav-item dropdown" style="list-style: none;">
            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href=""
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo e((filter_var(Auth::user()->profile_picture, FILTER_VALIDATE_URL) !== false) ? Auth::user()->profile_picture : asset('/avatars/'.Auth::user()->profile_picture)); ?>" alt="avatar" class="rounded-circle" width="50"
                    height="50"></a>
            
            <div class="dropdown-menu dropdown-menu-right user-dd animated">
                <?php if(auth()->guard()->guest()): ?>
                <a class="dropdown-item" href="<?php echo e(route('login')); ?>"><i class="ti-user m-r-5 m-l-5"></i> Login</a>
                <a class="dropdown-item" href="<?php echo e(route('register')); ?>"><i class="ti-wallet m-r-5 m-l-5"></i>
                    Register</a>
                <?php else: ?>
                    <a class="dropdown-item" href="<?php echo e(route('profiles.edit', Auth::user()->id)); ?>"><i
                            class="ti-settings m-r-5 m-l-5"></i> Account Setting</a>
                    
                    <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off m-r-5 m-l-5"></i>
                        Logout
                    </a>
                    
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo e(csrf_field()); ?>

                    </form>
                <?php endif; ?>
            </div>
        </li>
        <?php endif; ?>
    </div>
</nav><?php /**PATH /Applications/MAMP/htdocs/employme/resources/views/dashboard/layouts/nav.blade.php ENDPATH**/ ?>