
<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label for="title"><?php echo app('translator')->get('app.gamebank'); ?></label>
            <?php echo Form::select('gamebank', $game->gamebankNames, $edit ? $game->gamebank : '', ['class' => 'form-control', 'required' => true]); ?>

        </div>
    </div>

    <div class="col-md-4">
        <?php if(!$edit || $game->rezerv !== ''): ?>
            <div class="form-group">
                <label for="rezerv"><?php echo app('translator')->get('app.doubling'); ?></label>
                <?php echo Form::select('rezerv', $game->get_values('rezerv'), $edit ? $game->rezerv : '', ['class' => 'form-control', 'required' => true]); ?>

            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-4">
        <?php if(!$edit || $game->cask !== ''): ?>
            <div class="form-group">
                <label for="cask"><?php echo app('translator')->get('app.health'); ?></label>
                <?php echo Form::select('cask', $game->get_values('cask'), $edit ? $game->cask : '', ['class' => 'form-control', 'required' => true]); ?>

            </div>
        <?php endif; ?>
    </div>


</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="title"><?php echo app('translator')->get('app.jpg'); ?></label>
            <?php echo Form::select('jpg_id', ['' => '---'] + $jpgs, $edit ? $game->jpg_id : '', ['class' => 'form-control']); ?>

        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="title"><?php echo app('translator')->get('app.labels'); ?></label>
            <?php echo Form::select('label', ['' => '---'] + $game->labels, $edit ? $game->label : '', ['class' => 'form-control']); ?>

        </div>
    </div>
</div>


<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        <?php $__currentLoopData = [1,3,5,7,9,10]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <?php $__currentLoopData = \VanguardLTE\Game::$values['random_keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $random_key=>$values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $key = 'lines_percent_config_spin';
                        $array_key = 'line_spin[line'.$index.']['.$random_key.']';
                        $value = $game->get_line_value($game->$key, 'line'.$index, $random_key, true);
                    ?>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>L <?php echo e($index); ?> - <?php echo e($values[0]); ?>, <?php echo e($values[1]); ?></label>
                            <?php echo Form::select($array_key, $game->get_values('random_values', false, $edit ? $value: false), $edit ? $value : '', ['class' => 'form-control', 'required' => true]); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </li>
</ul>

<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        <?php $__currentLoopData = [1,3,5,7,9,10]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <?php $__currentLoopData = \VanguardLTE\Game::$values['random_keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $random_key=>$values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $key = 'lines_percent_config_spin_bonus';
                        $array_key = 'line_spin_bonus[line'.$index.'_bonus]['.$random_key.']';
                        $value = $game->get_line_value($game->$key, 'line'.$index.'_bonus', $random_key, true);
                    ?>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>L <?php echo e($index); ?> Bonus - <?php echo e($values[0]); ?>, <?php echo e($values[1]); ?></label>
                            <?php echo Form::select($array_key, $game->get_values('random_values', false, $edit ? $value: false), $edit ? $value : '', ['class' => 'form-control', 'required' => true]); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </li>
</ul>







<div class="row">

    <?php if($edit): ?>
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                <?php echo app('translator')->get('app.edit_game'); ?>
            </button>
        </div>
    <?php endif; ?>
</div><?php /**PATH /home/betshopus/casino/resources/views/backend/games/partials/match.blade.php ENDPATH**/ ?>