
<?php $__env->startSection('page-title', 'Tournaments'); ?>

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

		<div class="tournament-page">
			<?php if($tournament): ?>
				<h1 class="tournament-page__title">
					<span class="accent">Active</span>
					tournaments
				</h1>
				<div class="tournament-page__banner tournament-banner">
					<div class="tournament-banner__content">
						<div class="tournament-banner__img">
							<img class="lazy" data-src="<?php echo e('/storage/tournaments/' . $tournament->image); ?>">
						</div>
						<div class="tournament-banner__info">
							<div class="tournament-banner__info-top">
								<span class="tournament-banner__name"><?php echo e($tournament->name); ?></span>
								<?php if( $tournament->is_waiting() ): ?>
									<div class="tournament-banner__status _soon">waiting</div>
								<?php elseif( $tournament->is_completed() ): ?>
									<span class="tournament-banner__status _completed">Completed</span>
								<?php else: ?>
									<span class="tournament-banner__status _active">Active</span>
								<?php endif; ?>
							</div>
							<div class="tournament-banner__time">
								<div class="tournament-banner__time-item">
									<?php if( $tournament->is_waiting() ): ?>
										<span class="tournament-banner__time-top">Time to start</span>
										<span class="tournament-banner__time-val accent countdown" data-date="<?php echo e($tournament->start); ?>"></span>
									<?php elseif( $tournament->is_completed() ): ?>
										<span class="tournament-banner__time-top">End</span>
										<span class="tournament-banner__time-val accent">00 00:00:00</span>
									<?php else: ?>
										<span class="tournament-banner__time-top">Time left</span>
										<span class="tournament-banner__time-val accent countdown" data-date="<?php echo e($tournament->end); ?>"></span>
									<?php endif; ?>
								</div>
								<div class="tournament-banner__time-item">
									<span class="tournament-banner__time-top">Prize Fund:</span>
									<span class="tournament-banner__time-val"><?php echo e(number_format($tournament->sum_prizes, 2,".","")); ?> <?php echo e($currency); ?></span>
								</div>
							</div>
							<p class="tournament-banner__desc" ><?php echo mb_strimwidth(strip_tags($tournament->description), 0, 130, "..."); ?> </p>
							<a href="<?php echo e(route('frontend.tournaments.view', $tournament->id)); ?>" class="tournament-banner__btn btn">More</a>
						</div>
					</div>
				</div>

			<?php endif; ?>


			<?php if($activeTournaments || $waitingTournaments || $completedTournaments): ?>
				<h2 class="tournament-page__title">
					<span class="accent">other</span>
					tournaments
				</h2>
				<div class="tournament-cards">
					<?php $__currentLoopData = $activeTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if(!$tournament || ($tournament && $tournament->id != $item->id) ): ?>
							<div class="tournament-cards__item">
								<div class="tournament-cards__wrap">
									<div class="tournament-cards__img">
										<img class="lazy" data-src="<?php echo e('/storage/tournaments/' . $item->image); ?>">
									</div>
									<?php if( $item->is_waiting() ): ?>
										<div class="tournament-cards__status _soon">waiting</div>
									<?php elseif( $item->is_completed() ): ?>
										<div class="tournament-cards__status _completed">Completed</div>
									<?php else: ?>
										<div class="tournament-cards__status _active">Active</div>
									<?php endif; ?>
									<p class="tournament-cards__title"><?php echo e($item->name); ?></p>
									<div class="tournament-cards__time">
										<div class="tournament-cards__time-item">
											<?php if( $item->is_waiting() ): ?>
												<span class="tournament-cards__time-text">Time to start:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="<?php echo e($item->start); ?>"></span>
											<?php elseif( $item->is_completed() ): ?>
												<span class="tournament-cards__time-text">End:</span>
												<span class="tournament-cards__time-val accent">00 00:00:00</span>
											<?php else: ?>
												<span class="tournament-cards__time-text">Time left:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="<?php echo e($item->end); ?>"></span>
											<?php endif; ?>
										</div>
										<div class="tournament-cards__time-item">
											<span class="tournament-cards__time-text">prize fund:</span>
											<span class="tournament-cards__time-val"><?php echo e(number_format($item->sum_prizes, 2,".","")); ?> <?php echo e($currency); ?></span>
										</div>
									</div>
									<a href="<?php echo e(route('frontend.tournaments.view', $item->id)); ?>" class="tournament-cards__btn">More</a>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


					<?php $__currentLoopData = $waitingTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if(!$tournament || ($tournament && $tournament->id != $item->id) ): ?>
							<div class="tournament-cards__item">
								<div class="tournament-cards__wrap">
									<div class="tournament-cards__img">
										<img class="lazy" data-src="<?php echo e('/storage/tournaments/' . $item->image); ?>">
									</div>
									<?php if( $item->is_waiting() ): ?>
										<div class="tournament-cards__status _soon">waiting</div>
									<?php elseif( $item->is_completed() ): ?>
										<div class="tournament-cards__status _completed">Completed</div>
									<?php else: ?>
										<div class="tournament-cards__status _active">Active</div>
									<?php endif; ?>
									<p class="tournament-cards__title"><?php echo e($item->name); ?></p>
									<div class="tournament-cards__time">
										<div class="tournament-cards__time-item">
											<?php if( $item->is_waiting() ): ?>
												<span class="tournament-cards__time-text">Time to start:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="<?php echo e($item->start); ?>"></span>
											<?php elseif( $item->is_completed() ): ?>
												<span class="tournament-cards__time-text">End:</span>
												<span class="tournament-cards__time-val accent">00 00:00:00</span>
											<?php else: ?>
												<span class="tournament-cards__time-text">Time left:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="<?php echo e($item->end); ?>"></span>
											<?php endif; ?>
										</div>
										<div class="tournament-cards__time-item">
											<span class="tournament-cards__time-text">prize fund:</span>
											<span class="tournament-cards__time-val"><?php echo e(number_format($item->sum_prizes, 2,".","")); ?> <?php echo e($currency); ?></span>
										</div>
									</div>
									<a href="<?php echo e(route('frontend.tournaments.view', $item->id)); ?>" class="tournament-cards__btn">More</a>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					<?php $__currentLoopData = $completedTournaments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if(!$tournament || ($tournament && $tournament->id != $item->id) ): ?>
							<div class="tournament-cards__item">
								<div class="tournament-cards__wrap">
									<div class="tournament-cards__img">
										<img class="lazy" data-src="<?php echo e('/storage/tournaments/' . $item->image); ?>">
									</div>
									<?php if( $item->is_waiting() ): ?>
										<div class="tournament-cards__status _soon">waiting</div>
									<?php elseif( $item->is_completed() ): ?>
										<div class="tournament-cards__status _completed">Completed</div>
									<?php else: ?>
										<div class="tournament-cards__status _active">Active</div>
									<?php endif; ?>
									<p class="tournament-cards__title"><?php echo e($item->name); ?></p>
									<div class="tournament-cards__time">
										<div class="tournament-cards__time-item">
											<?php if( $item->is_waiting() ): ?>
												<span class="tournament-cards__time-text">Time to start:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="<?php echo e($item->start); ?>"></span>
											<?php elseif( $item->is_completed() ): ?>
												<span class="tournament-cards__time-text">End:</span>
												<span class="tournament-cards__time-val accent">00 00:00:00</span>
											<?php else: ?>
												<span class="tournament-cards__time-text">Time left:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="<?php echo e($item->end); ?>"></span>
											<?php endif; ?>
										</div>
										<div class="tournament-cards__time-item">
											<span class="tournament-cards__time-text">prize fund:</span>
											<span class="tournament-cards__time-val"><?php echo e(number_format($item->sum_prizes, 2,".","")); ?> <?php echo e($currency); ?></span>
										</div>
									</div>
									<a href="<?php echo e(route('frontend.tournaments.view', $item->id)); ?>" class="tournament-cards__btn">More</a>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</div>
			<?php endif; ?>


		</div>
	</div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
	<?php echo $__env->make('frontend.Default.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
	<?php echo $__env->make('frontend.Default.partials.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.Default.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/tournaments/list.blade.php ENDPATH**/ ?>