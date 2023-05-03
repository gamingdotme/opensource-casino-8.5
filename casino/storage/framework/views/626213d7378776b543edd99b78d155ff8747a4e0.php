<?php $__env->startSection('page-title', trans('app.happyhours')); ?>
<?php $__env->startSection('page-heading', trans('app.happyhours')); ?>

<?php $__env->startSection('content'); ?>

	<section class="content-header">
		<?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</section>

	<section class="content">
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo app('translator')->get('app.happyhours'); ?></h3>

				<div class="pull-right box-tools">
                    <?php if (\Auth::user()->hasPermission('happyhours.edit')) : ?>
                    <?php if($shop): ?>
                        <?php if( $shop->happyhours_active ): ?>
                            <a href="<?php echo e(route('backend.happyhour.status', 'disable')); ?>" class="btn btn-danger btn-sm"><?php echo app('translator')->get('app.disable'); ?></a>
                        <?php else: ?>
                            <a href="<?php echo e(route('backend.happyhour.status', 'activate')); ?>" class="btn btn-success btn-sm"><?php echo app('translator')->get('app.active'); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (\Auth::user()->hasPermission('happyhours.add')) : ?>
					<a href="<?php echo e(route('backend.happyhour.create')); ?>" class="btn btn-primary btn-sm"><?php echo app('translator')->get('app.add'); ?></a>
                    <?php endif; ?>
				</div>

			</div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th><?php echo app('translator')->get('app.id'); ?></th>
						<th><?php echo app('translator')->get('app.multiplier'); ?></th>
						<th><?php echo app('translator')->get('app.wager'); ?></th>
						<th><?php echo app('translator')->get('app.time'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if(count($happyhours)): ?>
						<?php $__currentLoopData = $happyhours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $happyhour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php echo $__env->make('backend.happyhours.partials.row', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php else: ?>
						<tr><td colspan="4"><?php echo app('translator')->get('app.no_data'); ?></td></tr>
					<?php endif; ?>
					</tbody>
					<thead>
					<tr>
						<th><?php echo app('translator')->get('app.id'); ?></th>
						<th><?php echo app('translator')->get('app.multiplier'); ?></th>
						<th><?php echo app('translator')->get('app.wager'); ?></th>
						<th><?php echo app('translator')->get('app.time'); ?></th>
					</tr>
					</thead>
                            </table>
                        </div>
                    </div>
		</div>
	</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
	<script>
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/happyhours/list.blade.php ENDPATH**/ ?>