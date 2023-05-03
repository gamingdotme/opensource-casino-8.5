<?php $__env->startSection('page-title', trans('app.progress')); ?>
<?php $__env->startSection('page-heading', trans('app.progress')); ?>

<?php $__env->startSection('content'); ?>

	<section class="content-header">
		<?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</section>

	<section class="content">

		<form action="" method="GET">
			<div class="box box-danger collapsed-box pin_show">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo app('translator')->get('app.filter'); ?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.sum_from'); ?></label>
								<input type="text" class="form-control" name="sum_from" value="<?php echo e(Request::get('sum_from')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.sum_to'); ?></label>
								<input type="text" class="form-control" name="sum_to" value="<?php echo e(Request::get('sum_to')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.spins_from'); ?></label>
								<input type="text" class="form-control" name="spins_from" value="<?php echo e(Request::get('spins_from')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.spins_to'); ?></label>
								<input type="text" class="form-control" name="spins_to" value="<?php echo e(Request::get('spins_to')); ?>">
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.bet_from'); ?></label>
								<input type="text" class="form-control" name="bet_from" value="<?php echo e(Request::get('bet_from')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.bet_to'); ?></label>
								<input type="text" class="form-control" name="bet_to" value="<?php echo e(Request::get('bet_to')); ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.type'); ?></label>
								<?php echo Form::select('type', ['' => __('app.all'), 'one_pay' => __('app.one_pay'), 'sum_pay' => __('app.sum_pay')], Request::get('type'), ['id' => 'type', 'class' => 'form-control']); ?>

							</div>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary">
						<?php echo app('translator')->get('app.filter'); ?>
					</button>
				</div>
			</div>
		</form>

		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title"><?php echo app('translator')->get('app.progress'); ?></h3>
                <div class="pull-right box-tools">
                    <?php if (\Auth::user()->hasPermission('progress.edit')) : ?>
                    <?php if($shop): ?>
                        <?php if( $shop->progress_active ): ?>
                            <a href="<?php echo e(route('backend.progress.status', 'disable')); ?>" class="btn btn-danger btn-sm"><?php echo app('translator')->get('app.disable'); ?></a>
                        <?php else: ?>
                            <a href="<?php echo e(route('backend.progress.status', 'activate')); ?>" class="btn btn-success btn-sm"><?php echo app('translator')->get('app.active'); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
			</div>
			<div class="box-body">
				<table class="table table-bordered table-striped">
					<thead>
					<tr>
						<th><?php echo app('translator')->get('app.rating'); ?></th>
						<th><?php echo app('translator')->get('app.sum'); ?></th>
						<th><?php echo app('translator')->get('app.type'); ?></th>
						<th><?php echo app('translator')->get('app.spins'); ?></th>
						<th><?php echo app('translator')->get('app.bet'); ?></th>

						<th><?php echo app('translator')->get('app.bonus'); ?></th>
						<th><?php echo app('translator')->get('app.day'); ?></th>
						<th><?php echo app('translator')->get('app.min'); ?></th>
						<th><?php echo app('translator')->get('app.max'); ?></th>
						<th><?php echo app('translator')->get('app.percent'); ?></th>
						<th><?php echo app('translator')->get('app.min_balance'); ?></th>
						<th><?php echo app('translator')->get('app.wager'); ?></th>
                        <th><?php echo app('translator')->get('app.days_active'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if(count($progress)): ?>
						<?php $__currentLoopData = $progress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php echo $__env->make('backend.progress.partials.row', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php else: ?>
						<tr><td colspan="13"><?php echo app('translator')->get('app.no_data'); ?></td></tr>
					<?php endif; ?>
					</tbody>
					<thead>
					<tr>
						<th><?php echo app('translator')->get('app.rating'); ?></th>
						<th><?php echo app('translator')->get('app.sum'); ?></th>
						<th><?php echo app('translator')->get('app.type'); ?></th>
						<th><?php echo app('translator')->get('app.spins'); ?></th>
						<th><?php echo app('translator')->get('app.bet'); ?></th>

						<th><?php echo app('translator')->get('app.bonus'); ?></th>
						<th><?php echo app('translator')->get('app.day'); ?></th>
						<th><?php echo app('translator')->get('app.min'); ?></th>
						<th><?php echo app('translator')->get('app.max'); ?></th>
						<th><?php echo app('translator')->get('app.percent'); ?></th>
						<th><?php echo app('translator')->get('app.min_balance'); ?></th>
						<th><?php echo app('translator')->get('app.wager'); ?></th>
                        <th><?php echo app('translator')->get('app.days_active'); ?></th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
	</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
		$(function() {
			//$('#progress-table').dataTable();
		});

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/progress/list.blade.php ENDPATH**/ ?>