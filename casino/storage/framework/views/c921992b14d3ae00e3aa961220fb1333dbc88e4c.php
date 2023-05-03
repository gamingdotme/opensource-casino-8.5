<header class="main-header">
    <!-- Logo -->
    <a class="logo" href="<?php echo e(url('/')); ?>">
        <span class="logo-mini"><b>G</b></span>
        <span class="logo-lg"><b><?php echo e(settings('app_name')); ?></b></span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only"><?php echo app('translator')->get('app.toggle_navigation'); ?></span>
        </a>

<div class="navbar-custom-menu">
   <ul class="nav navbar-nav">

<?php if(session()->exists('beforeUser')): ?>
      <li class="dropdown tasks-menu">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-repeat text-aqua"></i></a>
         <ul class="dropdown-menu">
            <li class="header"><b><?php echo e(auth()->user()->username); ?></b></li>
            <li>
               <ul class="menu">
                    <li><a href="<?php echo e(route('backend.user.back_login')); ?>"> Back Login</a></li>
               </ul>
            </li>
         </ul>
      </li>
<?php endif; ?>

    <?php
        $open_shift = \VanguardLTE\OpenShift::where(['shop_id' => auth()->user()->shop_id, 'end_date' => NULL])->first();
    ?>
	<?php if(Auth::user()->hasRole(['cashier'])): ?>
      <li class="dropdown tasks-menu">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-time text-aqua"></i></a>
         <ul class="dropdown-menu">
            <li class="header"><b><?php echo e(auth()->user()->username); ?></b></li>
            <li>
               <ul class="menu">
                        <?php if($open_shift): ?>
                           <li><a href="#" data-toggle="modal" data-target="#openShiftModal"> <?php echo app('translator')->get('app.start_shift'); ?></a></li>
                        <?php else: ?>
                           <li><a href="<?php echo e(route('backend.start_shift')); ?>"> <?php echo app('translator')->get('app.start_shift'); ?></a></li>
                        <?php endif; ?>
               </ul>
            </li>
         </ul>
      </li>
	<?php endif; ?>

    <li class="dropdown tasks-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-off text-aqua"></i></a>
        <ul class="dropdown-menu">
            <li class="header"><b><?php echo e(auth()->user()->username); ?></b></li>
            <li>
                <ul class="menu">

                    <?php if(config('session.driver') == 'database'): ?>
                        <li><a href="<?php echo e(route('backend.profile.sessions')); ?>"> <?php echo app('translator')->get('app.active_sessions'); ?></a></li>
                    <?php endif; ?>



                    <?php if( auth()->user()->hasRole(['admin', 'agent']) ): ?>
                        <li><a href="<?php echo e(route('backend.credit.list')); ?>"> <?php if(auth()->user()->hasRole('admin')): ?> <?php echo app('translator')->get('app.edit_credit'); ?> <?php else: ?> <?php echo app('translator')->get('app.buy_credit'); ?> <?php endif; ?> </a></li>
                    <?php endif; ?>

                    <li><a href="<?php echo e(route('backend.user.edit', auth()->user()->present()->id)); ?>"> <?php echo app('translator')->get('app.my_profile'); ?></a></li>
                    <li><a href="<?php echo e(route('backend.auth.logout')); ?>"> <?php echo app('translator')->get('app.logout'); ?></a></li>

                </ul>
            </li>
        </ul>
    </li>

   </ul>
</div>
    </nav>
</header>
<!-- Left side column. contains the logo and sidebar -->

<?php if(Auth::user()->hasRole(['cashier'])): ?>

    <div class="modal fade" id="openShiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(route('backend.start_shift')); ?>" method="GET" id="outForm">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo app('translator')->get('app.start_shift'); ?></h4>
                    </div>
                    <div class="modal-body">
					    <?php if($open_shift): ?>
                        <?php
                            $money = $open_shift->users;
                            if($open_shift->end_date == NULL){
                                $money = $open_shift->get_money();
                            }

                            $payout = $open_shift->money_in > 0 ? ($open_shift->money_out / $open_shift->money_in) * 100 : 0;
                            $date = \Carbon\Carbon::now()->format(config('app.date_time_format'));

                        ?>
                        <table class="table table-striped">
                            <tr><td>Start:</td><td> <?php echo e($open_shift->start_date); ?></td></tr>
                            <tr><td>Money: </td><td> <?php echo e($money); ?></td></tr>
                            <tr><td>In:</td><td> <?php echo e($open_shift->money_in); ?></td></tr>
                            <tr><td>Out: </td><td><?php echo e($open_shift->money_out); ?></td></tr>
                            <tr><td>Total: </td><td><?php echo e($open_shift->money_in - $open_shift->money_out); ?></td></tr>
                            <tr><td>Transfers:</td><td> <?php echo e($open_shift->transfers); ?></td></tr>
                            <tr><td>Pay Out:</td><td> <?php echo e($payout); ?></td></tr>
                        </table>
                        <?php else: ?>
                            <p><?php echo app('translator')->get('app.shift_not_opened'); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?php echo app('translator')->get('app.close'); ?></button>
                        <a href="<?php echo e(route('backend.start_shift.print')); ?>" target="_blank" class="btn btn-success"><?php echo app('translator')->get('app.print'); ?></a>
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get('app.ok'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php endif; ?>
<?php /**PATH /home/betshopus/casino/resources/views/backend/partials/navbar.blade.php ENDPATH**/ ?>