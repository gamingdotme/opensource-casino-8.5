<?php $__env->startSection('page-title', trans('app.tournaments')); ?>
<?php $__env->startSection('page-heading', trans('app.tournaments')); ?>

<?php $__env->startSection('content'); ?>

	<section class="content-header">
		<?php echo $__env->make('backend.partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</section>

	<section class="content">


		<form action="" method="GET">
			<div class="box box-danger collapsed-box tournament_show">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo app('translator')->get('app.filter'); ?></h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.search'); ?></label>
								<input type="text" class="form-control" name="search" value="<?php echo e(Request::get('search')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label> <?php echo app('translator')->get('app.date_start'); ?></label>
								<div class="input-group">
									<button type="button" class="btn btn-default pull-right" id="daterange-btn">
										<span><i class="fa fa-calendar"></i> <?php echo e(Request::get('dates_view') ?: __('app.date_start_picker')); ?></span>
										<i class="fa fa-caret-down"></i>
									</button>
								</div>
								<input type="hidden" id="dates_view" name="dates_view" value="<?php echo e(Request::get('dates_view')); ?>">
								<input type="hidden" id="dates" name="dates" value="<?php echo e(Request::get('dates')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.date_end'); ?></label>
								<div class="input-group">
									<button type="button" class="btn btn-default pull-right" id="end_daterange-btn">
										<span><i class="fa fa-calendar"></i> <?php echo e(Request::get('end_dates_view') ?: __('app.date_end_picker')); ?></span>
										<i class="fa fa-caret-down"></i>
									</button>
								</div>
								<input type="hidden" id="end_dates_view" name="end_dates_view" value="<?php echo e(Request::get('end_dates_view')); ?>">
								<input type="hidden" id="end_dates" name="end_dates" value="<?php echo e(Request::get('end_dates')); ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.type'); ?></label>
								<?php echo Form::select('type',  ['' => '---'] + \VanguardLTE\Tournament::$values['type'], Request::get('type'), ['class' => 'form-control']); ?>

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
								<label><?php echo app('translator')->get('app.prize_from'); ?></label>
								<input type="text" class="form-control" name="prize_from" value="<?php echo e(Request::get('prize_from')); ?>">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.prize_to'); ?></label>
								<input type="text" class="form-control" name="prize_to" value="<?php echo e(Request::get('prize_to')); ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label><?php echo app('translator')->get('app.status'); ?></label>
								<?php echo Form::select('status', ['' => '---'] + \VanguardLTE\Tournament::$values['status'],Request::get('status'), ['class' => 'form-control']); ?>

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
				<h3 class="box-title"><?php echo app('translator')->get('app.tournaments'); ?></h3>
				<?php if (\Auth::user()->hasPermission('tournaments.add')) : ?>
				<div class="pull-right box-tools">
					<a href="<?php echo e(route('backend.tournament.create')); ?>" class="btn btn-block btn-primary btn-sm"><?php echo app('translator')->get('app.add'); ?></a>
				</div>
				<?php endif; ?>
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th><?php echo app('translator')->get('app.id'); ?></th>
							<th><?php echo app('translator')->get('app.name'); ?></th>
							<th><?php echo app('translator')->get('app.start'); ?></th>
							<th><?php echo app('translator')->get('app.end'); ?></th>
							<th><?php echo app('translator')->get('app.type'); ?></th>
							<th><?php echo app('translator')->get('app.prize'); ?></th>
							<th><?php echo app('translator')->get('app.bet'); ?></th>
							<th><?php echo app('translator')->get('app.spins'); ?></th>
							<th><?php echo app('translator')->get('app.wager'); ?></th>
							<th><?php echo app('translator')->get('app.status'); ?></th>
						</tr>
						</thead>
						<tbody>
						<?php if(count($tournaments)): ?>
							<?php $__currentLoopData = $tournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tournament): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php echo $__env->make('backend.tournaments.partials.row', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php else: ?>
							<tr><td colspan="10"><?php echo app('translator')->get('app.no_data'); ?></td></tr>
						<?php endif; ?>
						</tbody>
						<thead>
						<tr>
							<th><?php echo app('translator')->get('app.id'); ?></th>
							<th><?php echo app('translator')->get('app.name'); ?></th>
							<th><?php echo app('translator')->get('app.start'); ?></th>
							<th><?php echo app('translator')->get('app.end'); ?></th>
							<th><?php echo app('translator')->get('app.type'); ?></th>
							<th><?php echo app('translator')->get('app.prize'); ?></th>
							<th><?php echo app('translator')->get('app.bet'); ?></th>
							<th><?php echo app('translator')->get('app.spins'); ?></th>
							<th><?php echo app('translator')->get('app.wager'); ?></th>
							<th><?php echo app('translator')->get('app.status'); ?></th>
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

		$(function() {

			$('.datepicker').daterangepicker({
				"locale": {
					format: 'YYYY-MM-DD'
				},
				"startDate": "<?php echo e(date('Y-m-d')); ?>",
				"endDate": "<?php echo e(date('Y-m-d', time() + 31*24*60*60)); ?>"
			});

			$('.btn-box-tool').click(function(event){
				if( $('.tournament_show').hasClass('collapsed-box') ){
					$.cookie('tournament_show', '1');
				} else {
					$.removeCookie('tournament_show');
				}
			});

			if( $.cookie('tournament_show') ){
				$('.tournament_show').removeClass('collapsed-box');
				$('.tournament_show .btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
			}
		});
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/backend/tournaments/list.blade.php ENDPATH**/ ?>