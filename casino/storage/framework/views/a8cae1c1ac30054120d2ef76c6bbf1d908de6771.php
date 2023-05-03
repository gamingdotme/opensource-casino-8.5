<?php $__env->startSection('page-title', trans('app.edit_happyhour')); ?>
<?php $__env->startSection('page-heading', $happyhour->title); ?>

<?php $__env->startSection('content'); ?>

<section class="content-header">
<?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</section>

    <section class="content">

      <div class="box box-default">
		<?php echo Form::open(['route' => array('backend.happyhour.update', $happyhour->id), 'files' => true, 'id' => 'user-form']); ?>

        <div class="box-header with-border">
          <h3 class="box-title"><?php echo app('translator')->get('app.edit_happyhour'); ?></h3>
        </div>

        <div class="box-body">
          <div class="row">
            <?php echo $__env->make('backend.happyhours.partials.base', ['edit' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
            <?php echo app('translator')->get('app.edit_happyhour'); ?>
        </button>
		<?php if (\Auth::user()->hasPermission('happyhours.delete')) : ?>
        <a href="<?php echo e(route('backend.happyhour.delete', $happyhour->id)); ?>"
           class="btn btn-danger"
           data-method="DELETE"
           data-confirm-title="<?php echo app('translator')->get('app.please_confirm'); ?>"
           data-confirm-text="<?php echo app('translator')->get('app.are_you_sure_delete_happyhour'); ?>"
           data-confirm-delete="<?php echo app('translator')->get('app.yes_delete_him'); ?>">
            <?php echo app('translator')->get('app.delete_happyhour'); ?>
        </a>
		<?php endif; ?>
        </div>
		<?php echo Form::close(); ?>

      </div>
    </section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/happyhours/edit.blade.php ENDPATH**/ ?>