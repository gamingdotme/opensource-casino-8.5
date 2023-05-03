<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label><?php echo app('translator')->get('app.name'); ?></label>
            <input type="text" class="form-control" name="name" value="<?php echo e($edit ? $tournament->name : old('name')); ?>" required <?php if($denied): ?> disabled <?php endif; ?>>
        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.start'); ?></label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="start" id="start" class="form-control pull-right datepicker" required value="<?php echo e($edit ? $tournament->start : old('start')); ?>" <?php if($denied): ?> disabled <?php endif; ?>>
            </div>
        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.end'); ?></label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="end" id="end" class="form-control pull-right datepicker" required value="<?php echo e($edit ? $tournament->end : old('end')); ?>" <?php if($denied): ?> disabled <?php endif; ?>>
            </div>
        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.type'); ?></label>
            <?php echo Form::select('type', \VanguardLTE\Tournament::$values['type'], $edit ? $tournament->type : old('type'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>

        <div class="form-group">
            <label><?php echo app('translator')->get('app.bet'); ?></label>
            <?php
                $bets = array_combine(\VanguardLTE\Tournament::$values['bet'], \VanguardLTE\Tournament::$values['bet']);
            ?>
            <?php echo Form::select('bet', $bets, $edit ? $tournament->bet : old('bet'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.spins'); ?></label>
            <?php
                $spins = array_combine(\VanguardLTE\Tournament::$values['spins'], \VanguardLTE\Tournament::$values['spins']);
            ?>
            <?php echo Form::select('spins', $spins, $edit ? $tournament->spins : old('spins'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>

        <div class="form-group">
            <label for="device"> <?php echo app('translator')->get('app.categories'); ?></label>
            <select class="form-control select2" name="categories[]" multiple="multiple" id="categories" style="width: 100%;" <?php if($denied): ?> disabled <?php endif; ?>>
                <option value="0" <?php echo e(((old('categories') && in_array(0, old('categories')) ) || ($edit && in_array(0, $cats) )) ? 'selected':''); ?>>All</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>"
                            <?php echo e(((old('categories') && in_array($category->id, old('categories')) )  || ($edit && in_array($category->id, $cats) ))
    ? 'selected':''); ?>

                    ><?php echo e($category->title); ?></option>
                    <?php $__currentLoopData = $category->inner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inner->id); ?>"
                                <?php echo e((( old('categories') && in_array($inner->id, old('categories')) || ( $edit && in_array($inner->id, $cats) )) ) ? 'selected':''); ?>><?php echo e($inner->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.games'); ?></label>
            <?php echo Form::select('games[]', $games, ($edit && $tournament->games_selected) ? $gams : old('games'), ['id' => 'games', 'class' => 'form-control select2', 'multiple' => 'multiple', 'style' => 'width: 100%;', 'disabled' => $denied ? true:false]); ?>

        </div>

        <div class="form-group">
            <label><?php echo app('translator')->get('app.bots'); ?></label>
            <?php
                $bots = array_combine(\VanguardLTE\Tournament::$values['bots'], \VanguardLTE\Tournament::$values['bots']);
            ?>
            <?php echo Form::select('bots', $bots, $edit ? $tournament->bots : old('bots'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.bots_time'); ?></label>
            <?php
                $bots_time = array_combine(\VanguardLTE\Tournament::$values['bots_time'], \VanguardLTE\Tournament::$values['bots_time']);
            ?>
            <?php echo Form::select('bots_time', $bots_time, $edit ? $tournament->bots_time : old('bots_time'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.bots_step'); ?></label>
            <?php echo Form::select('bots_step', \VanguardLTE\Tournament::$values['bots_step'], $edit ? $tournament->bots_step : old('bots_step'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.bots_limit'); ?></label>
            <?php
                $bots_limit = array_combine(\VanguardLTE\Tournament::$values['bots_limit'], \VanguardLTE\Tournament::$values['bots_limit']);
            ?>
            <?php echo Form::select('bots_limit', $bots_limit, $edit ? $tournament->bots_limit : old('bots'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.wager'); ?></label>
            <?php echo Form::select('wager', \VanguardLTE\Tournament::$values['wager'], $edit ? $tournament->wager : old('wager'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.image'); ?></label>
            <?php if($edit && $tournament->image != ''): ?>
                <img src="<?php echo e('/storage/tournaments/' . $tournament->image); ?>" style="width: 100%;">
            <?php endif; ?>
            <input type="file" class="form-control" name="image" value="<?php echo e($edit ? $tournament->image : old('image')); ?>" <?php if($denied): ?> disabled <?php endif; ?>>
        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.text'); ?></label>
            <textarea class="form-control" id="editor" name="description"  ><?php echo e($edit ? $tournament->description : old('description')); ?></textarea>
        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.repeat_days'); ?></label>
            <?php
                $repeat_days = array_combine(\VanguardLTE\Tournament::$values['repeat_days'], \VanguardLTE\Tournament::$values['repeat_days']);
            ?>
            <?php echo Form::select('repeat_days', ['' => '---'] + $repeat_days, $edit ? $tournament->repeat_days : old('repeat_days'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>
        <div class="form-group">
            <label><?php echo app('translator')->get('app.repeat_number'); ?></label>
            <?php
                $repeat_number = array_combine(\VanguardLTE\Tournament::$values['repeat_number'], \VanguardLTE\Tournament::$values['repeat_number']);
            ?>
            <?php echo Form::select('repeat_number', ['' => '---'] + $repeat_number, $edit ? $tournament->repeat_number : old('repeat_number'), ['class' => 'form-control', 'disabled' => $denied ? true:false]); ?>

        </div>

    </div>
    <div class="col-md-6">
        <div id="prizes">

            <?php $count = 0; ?>

            <?php if(old('prize')): ?>
                <?php $__currentLoopData = old('prize'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prize): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $count++; ?>
                    <div class="prize_item">
                        <div class="form-group">
                            <label><?php echo app('translator')->get('app.prize'); ?> <?php echo e($count); ?></label>
                            <div class="input-group">
                                <input type="number" step="0.0000001" name="prize[]" class="form-control" required value="<?php echo e($prize); ?>" <?php if($denied): ?> disabled <?php endif; ?>>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info delete_prize">-</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <?php if($edit): ?>
                    <?php if($tournament->prizes ): ?>
                        <?php $__currentLoopData = $tournament->prizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prize): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $count++; ?>
                            <div class="prize_item">
                                <div class="form-group">
                                    <label><?php echo app('translator')->get('app.prize'); ?> <?php echo e($count); ?></label>
                                    <div class="input-group">
                                        <input type="number" step="0.0000001" name="prize[]" class="form-control" required value="<?php echo e($prize->prize); ?>" <?php if($denied): ?> disabled <?php endif; ?>>
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-info delete_prize">-</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($count < 10): ?>
                <?php for($i=0; $i<(10-$count); $i++): ?>
                    <div class="prize_item">
                        <div class="form-group">
                            <label><?php echo app('translator')->get('app.prize'); ?> <?php echo e($i+$count+1); ?></label>
                            <div class="input-group">
                                <input type="number" step="0.0000001" name="prize[]" class="form-control" required value="" <?php if($denied): ?> disabled <?php endif; ?>>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info delete_prize">-</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /home/betshopus/casino/resources/views/backend/tournaments/partials/base.blade.php ENDPATH**/ ?>