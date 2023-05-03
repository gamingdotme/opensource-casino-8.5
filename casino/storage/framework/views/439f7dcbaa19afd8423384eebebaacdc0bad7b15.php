
<?php $__env->startSection('page-title', $tournament->name); ?>

<?php $__env->startSection('content'); ?>


	<?php
        if(Auth::check()){
            $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
        } else{
            $currency = '';
        }
	?>

	<div class="container">
		<?php echo $__env->make('frontend.Default.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<!-- tournament-block-->
		<div class="tournament-block">
			<div class="tournament-block__info">
				<div class="tournament-block__item">
					<div class="tournament-block__info-top">
						<h1 class="tournament-block__info-title">
							<?php echo e($tournament->name); ?>

						</h1>
						<div class="tournament-block__info-prize">
							Prize fund:
							<span class="tournament-block__info-val"><?php echo e(number_format($tournament->sum_prizes, 2,".","")); ?> <?php echo e($currency); ?></span>
						</div>
					</div>
					<div class="tournament-block__desc custom-scroll" data-simplebar>
						<p class="tournament-block__desc-title">
							description
						</p>
						<p class="tournament-block__desc-text">
							<?php echo htmlspecialchars_decode($tournament->description); ?>
						</p>
					</div>
				</div>
				<div class="tournament-block__item">
					<ul class="tournament-block__terms">
						<li class="tournament-block__terms-item">
							<span class="text">Status:</span>
							<span class="accent">
								<?php if( $tournament->is_waiting() ): ?>
									waiting
								<?php elseif( $tournament->is_completed() ): ?>
									Completed
								<?php else: ?>
									Active
								<?php endif; ?>
							</span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">Date of beginning:</span>
							<span class="accent"><?php echo e($tournament->start); ?></span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">Date of ending:</span>
							<span class="accent"><?php echo e($tournament->end); ?></span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">Type of tournament:</span>
							<span class="accent"><?php echo e(\VanguardLTE\Tournament::$values['type'][$tournament->type]); ?></span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">minimal bet:</span>
							<span class="accent"><?php echo e($tournament->bet); ?></span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">spins for qualification:</span>
							<span class="accent"><?php echo e($tournament->spins); ?></span>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<h2 class="leaderboard__title">Leaders</h2>
		<div class="leaderboard tournament<?php echo e($tournament->id); ?>">
			<div class="leaderboard__block">
				<div class="leaderboard__item">
					<div class="leaderboard__table">
						<div class="leaderboard__table-head">
							<span class="leaderboard__table-head-item">№</span>
							<span class="leaderboard__table-head-item">Login</span>
							<span class="leaderboard__table-head-item">points</span>
							<span class="leaderboard__table-head-item">prize</span>
						</div>
						<div class="leaderboard__table-body table1">
							<?php if( count($tournament->stats) ): ?>
								<?php $index=1; ?>
								<?php $__currentLoopData = $tournament->get_stats(0, 5, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<div class="leaderboard__table-row">
										<div class="leaderboard__table-body-item"><?php echo e($index); ?></div>
										<div class="leaderboard__table-body-item"><?php echo e($stat['username']); ?></div>
										<div class="leaderboard__table-body-item"><?php echo e($stat['points']); ?></div>
										<div class="leaderboard__table-body-item"><?php echo e($stat['prize']); ?></div>
									</div>
									<?php $index++; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<div class="tournament__table-row">
									<span class="tournament__table-item"><?php echo app('translator')->get('app.no_data'); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="leaderboard__item">
					<div class="leaderboard__table">
						<div class="leaderboard__table-head">
							<span class="leaderboard__table-head-item">№</span>
							<span class="leaderboard__table-head-item">Login</span>
							<span class="leaderboard__table-head-item">points</span>
							<span class="leaderboard__table-head-item">prize</span>
						</div>
						<div class="leaderboard__table-body table2">
							<?php if( count($tournament->stats) > 5 ): ?>
								<?php $index=6; ?>
								<?php $__currentLoopData = $tournament->get_stats(5, 5, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<div class="leaderboard__table-row">
										<div class="leaderboard__table-body-item"><?php echo e($index); ?></div>
										<div class="leaderboard__table-body-item"><?php echo e($stat['username']); ?></div>
										<div class="leaderboard__table-body-item"><?php echo e($stat['points']); ?></div>
										<div class="leaderboard__table-body-item"><?php echo e($stat['prize']); ?></div>
									</div>
									<?php $index++; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
								<div class="tournament__table-row">
									<span class="tournament__table-item"><?php echo app('translator')->get('app.no_data'); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<p class="leaderboard__place accent">
				YOUR PLACE IN THE RATINGS: <span class="tournament<?php echo e($tournament->id); ?>_place"><?php echo e($tournament->my_place() ?: '---'); ?></span>
			</p>
		</div>
		<div class="tournament-title">
			<p class="tournament-title__main">
				<span class="accent">GAMES TAKING</span>
				PART IN THE TOURNAMENT
			</p>
		</div>
		<div class="grid">
			<?php if( $tournament->games ): ?>
				<?php $__currentLoopData = $tournament->games; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if($game = $game->game): ?>
						<?php echo $__env->make('frontend.Default.partials.game', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php endif; ?>
		</div>
		<!--
		<div class="show-more">
			<a href="#" class="btn btn-more">All slot</a>
		</div>
		-->
	</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
	<?php echo $__env->make('frontend.Default.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
	<?php echo $__env->make('frontend.Default.partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.Default.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/tournaments/view.blade.php ENDPATH**/ ?>