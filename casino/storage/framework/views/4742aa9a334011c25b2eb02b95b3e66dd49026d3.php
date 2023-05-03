<?php $__env->startSection('page-title', trans('app.edit_game')); ?>
<?php $__env->startSection('page-heading', $game->name); ?>

<?php $__env->startSection('content'); ?>

    <section class="content-header">
        <?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-3">

                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <center>
                            <img class="img-responsive" src="<?php echo e($edit ? '/frontend/Default/ico/' . $game->name . '.jpg' : ''); ?>" alt="<?php echo e($edit ? $game->name : ''); ?>">
                        </center>
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b><?php echo app('translator')->get('app.percent'); ?></b> <a class="pull-right"><?php echo e($game->shop? $game->shop->get_percent_label($game->shop->percent):'0'); ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo app('translator')->get('app.in'); ?></b> <a class="pull-right"><?php echo e($game->stat_in); ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo app('translator')->get('app.out'); ?></b> <a class="pull-right"><?php echo e($game->stat_out); ?></a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo app('translator')->get('app.total'); ?></b>
                                <a class="pull-right">
                                    <?php if(($game->stat_in - $game->stat_out) >= 0): ?>
                                        <span class="text-green">
		<?php else: ?>
                                                <span class="text-red">
		<?php endif; ?>
                                                    <?php echo e(number_format(abs($game->stat_in-$game->stat_out), 4, '.', '')); ?>

		</span>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo app('translator')->get('app.rtp'); ?></b> <a class="pull-right"><?php echo e($game->stat_in > 0 ? number_format(($game->stat_out / $game->stat_in) * 100, 2, '.', '') : '0.00'); ?></a>
                            </li>
                        </ul>

                        <a href="<?php echo e(route('backend.game.delete', $game->id)); ?>" class="btn btn-danger btn-block"
                           data-method="DELETE"
                           data-confirm-title="<?php echo app('translator')->get('app.please_confirm'); ?>"
                           data-confirm-text="<?php echo app('translator')->get('app.are_you_sure_delete_game'); ?>"
                           data-confirm-delete="<?php echo app('translator')->get('app.yes_delete_him'); ?>">
                            <b>DELETE</b></a>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-body">
                        <h4><?php echo app('translator')->get('app.latest_stats'); ?></h4>

                        <table class="table table-borderless table-striped">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get('app.user'); ?></th>
                                <th><?php echo app('translator')->get('app.win'); ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php if(count($game_stat)): ?>
                                <?php $__currentLoopData = $game_stat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('backend.game_stat', ['user' => $stat->user->username])); ?>">
                                                <?php echo e($stat->user->username); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($stat->win); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr><td colspan="2"><?php echo app('translator')->get('app.no_data'); ?></td></tr>
                            <?php endif; ?>

                            </tbody>
                        </table>

                    </div>
                </div>

            </div>

            <div class="col-md-9" id="colrighttemp">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a id="details-tab"
                               data-toggle="tab"
                               href="#details">
                                <?php echo app('translator')->get('app.game_details'); ?>
                            </a>
                        </li>

                            <li>
                                <a id="authentication-tab"
                                   data-toggle="tab"
                                   href="#login-details">
                                    <?php echo app('translator')->get('app.game_settings'); ?>
                                </a>
                            </li>

                            <li>
                                <a id="bonus-tab"
                                   data-toggle="tab"
                                   href="#bonus-details">
                                    <?php echo app('translator')->get('app.game_bonuses'); ?>
                                </a>
                            </li>

                    </ul>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="active tab-pane" id="details">
                            <?php echo Form::open(['route' => ['backend.game.update', $game->id], 'method' => 'POST', 'id' => 'details-form']); ?>

                            <?php echo $__env->make('backend.games.partials.base', ['profile' => false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php echo Form::close(); ?>

                        </div>


                        <div class="tab-pane" id="login-details">
                            <?php echo Form::open(['route' => ['backend.game.update', $game->id], 'method' => 'POST', 'id' => 'login-details-form']); ?>

                            <?php echo $__env->make('backend.games.partials.match', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php echo Form::close(); ?>

                        </div>

                        <div class="tab-pane" id="bonus-details">
                            <?php echo Form::open(['route' => ['backend.game.update', $game->id], 'method' => 'POST', 'id' => 'bonus-details-form']); ?>

                            <?php echo $__env->make('backend.games.partials.bonus', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php echo Form::close(); ?>

                        </div>

                    </div>

                </div>



            </div>
        </div>



    </section>









<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $('.changeAddSum').click(function(event){
            $('#AddSum').val($(event.target).data('value'));
            $('#gamebank_add').submit();
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/games/edit.blade.php ENDPATH**/ ?>