<div class="col-md-6">
    <div class="form-group">
        <label><?php echo app('translator')->get('app.multiplier'); ?></label>
        <?php
            $multipliers = array_combine(\VanguardLTE\HappyHour::$values['multiplier'], \VanguardLTE\HappyHour::$values['multiplier']);
        ?>
        <?php echo Form::select('multiplier', $multipliers, $edit ? $happyhour->multiplier : '', ['class' => 'form-control']); ?>

    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label><?php echo app('translator')->get('app.wager'); ?></label>
        <?php echo Form::select('wager', \VanguardLTE\HappyHour::$values['wager'], $edit ? $happyhour->wager : old('wager'), ['class' => 'form-control']); ?>

    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label><?php echo app('translator')->get('app.time'); ?></label>
        <?php
            $times = array_combine(\VanguardLTE\HappyHour::$values['time'], \VanguardLTE\HappyHour::$values['time']);
        ?>
        <?php echo Form::select('time', \VanguardLTE\HappyHour::$values['time'], $edit ? $happyhour->time : '', ['class' => 'form-control']); ?>

    </div>
</div>
<?php /**PATH /home/betshopus/casino/resources/views/backend/happyhours/partials/base.blade.php ENDPATH**/ ?>