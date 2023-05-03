<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        <?php $__currentLoopData = [1,3,5,7,9,10]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <?php $__currentLoopData = \VanguardLTE\Game::$values['random_keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $random_key=>$values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $key = 'lines_percent_config_bonus';
                        $array_key = 'line_bonus[line'.$index.']['.$random_key.']';
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
                        $key = 'lines_percent_config_bonus_bonus';
                        $array_key = 'line_bonus_bonus[line'.$index.'_bonus]['.$random_key.']';
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


<ul class="list-group list-group-unbordered">
    <li class="list-group-item">
        <div class="row">
            <?php $__currentLoopData = [1,2,3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $key = 'chanceFirepot'.$index;
                ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>ChanceFirepot <?php echo e($index); ?></label>
                        <?php echo Form::select($key, $game->get_values($key, true, $edit ? $game->$key: false), $edit ? $game->$key : '', ['class' => 'form-control', 'required' => true]); ?>

                    </div>
                </div>
                <?php
                    $key = 'fireCount'.$index;
                ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>FireCount <?php echo e($index); ?></label>
                        <?php echo Form::select($key, $game->get_values($key, true, $edit ? $game->$key: false), $edit ? $game->$key : '', ['class' => 'form-control', 'required' => true]); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

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
</div><?php /**PATH /home/betshopus/casino/resources/views/backend/games/partials/bonus.blade.php ENDPATH**/ ?>