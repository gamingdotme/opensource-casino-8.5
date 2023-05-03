<div class="row">
    <?php if(!$edit || $game->name !== ''): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label for="name"><?php echo app('translator')->get('app.name'); ?></label>
                <input type="text" class="form-control" id="name"
                       name="name" placeholder="<?php echo app('translator')->get('app.name'); ?>" <?php echo e($edit ? 'disabled' : ''); ?> value="<?php echo e($edit ? $game->name : ''); ?>" required>
            </div>
        </div>
    <?php endif; ?>
    <?php if(!$edit || $game->title !== ''): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label for="title"><?php echo app('translator')->get('app.title'); ?></label>
                <input type="text" class="form-control" id="title"
                       name="title" placeholder="<?php echo app('translator')->get('app.title'); ?>" value="<?php echo e($edit ? $game->title : ''); ?>" required>
            </div>
        </div>
    <?php endif; ?>



    <div class="col-md-12">
        <div class="form-group">
            <label for="category"><?php echo app('translator')->get('app.categories'); ?></label>
            <select name="category[]" id="category" class="form-control select2" multiple="multiple" style="width: 100%;" required>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" <?php echo e(($edit && in_array($category->id, $cats))? 'selected="selected"' : ''); ?>><?php echo e($category->title); ?></option>
                    <?php $__currentLoopData = $category->inner; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inner->id); ?>" <?php echo e(($edit && in_array($inner->id, $cats))? 'selected="selected"' : ''); ?>><?php echo e($inner->title); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="device"><?php echo app('translator')->get('app.device'); ?></label>
            <select name="device" id="device" class="form-control" required>
                <option value="0" <?php echo e(($edit && !$game->device==0)? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.mobile'); ?></option>
                <option value="1" <?php echo e(($edit && $game->device==1)? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.desktop'); ?></option>
                <option value="2" <?php echo e(($edit && $game->device==2)? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.mobile'); ?> + <?php echo app('translator')->get('app.desktop'); ?></option>
            </select>
        </div>
    </div>

    <?php if(!$edit || $game->bet !== ''): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label for="bet"><?php echo app('translator')->get('app.bet'); ?></label>
                <?php echo Form::select('bet', $game->get_values('bet'), $edit ? $game->bet : '', ['class' => 'form-control', 'required' => true]); ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if(!$edit || $game->denomination !== ''): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo app('translator')->get('app.denomination'); ?></label>
                <?php
                    $denominations = array_combine(\VanguardLTE\Game::$values['denomination'], \VanguardLTE\Game::$values['denomination']);
                ?>
                <?php echo Form::select('denomination', $denominations, $edit ? $game->denomination : '1.00', ['class' => 'form-control']); ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if(!$edit || $game->scaleMode !== ''): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label for="scaleMode"><?php echo app('translator')->get('app.scaling'); ?></label>
                <select name="scaleMode" id="scaleMode" class="form-control" required>
                    <option value="showAll" <?php echo e($edit && $game->scaleMode=='showAll'? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.default'); ?></option>
                    <option value="exactFit" <?php echo e($edit && $game->scaleMode=='exactFit'? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.full_screen'); ?></option>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <?php if(!$edit || $game->slotViewState !== ''): ?>
        <div class="col-md-6">
            <div class="form-group">
                <label for="slotViewState"><?php echo app('translator')->get('app.ui'); ?></label>
                <select name="slotViewState" id="slotViewState" class="form-control" required>
                    <option value="Normal" <?php echo e($edit && $game->slotViewState=='Normal'? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.visible_ui'); ?></option>
                    <option value="HideUI" <?php echo e($edit && $game->slotViewState=='HideUI'? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.hide_ui'); ?></option>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-6">
        <div class="form-group">
            <label for="view"><?php echo app('translator')->get('app.view'); ?></label>
            <select name="view" id="view" class="form-control">
                <option value="1" <?php echo e($edit && $game->view? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.active'); ?></option>
                <option value="0" <?php echo e($edit && !$game->view? 'selected="selected"' : ''); ?>><?php echo app('translator')->get('app.disabled'); ?></option>
            </select>
        </div>
    </div>

    <?php if($edit): ?>
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                <?php echo app('translator')->get('app.edit_game'); ?>
            </button>
        </div>
    <?php endif; ?>
</div><?php /**PATH /home/betshopus/casino/resources/views/backend/games/partials/base.blade.php ENDPATH**/ ?>