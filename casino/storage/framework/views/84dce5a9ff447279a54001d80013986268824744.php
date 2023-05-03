<div class="grid-item grid-item--height2 grid-item--width2">
    <div class="grid__content games">
        <div class="games__item">
            <div class="games__content">
                <img src="<?php echo e($game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : ''); ?>" alt="<?php echo e($game->title); ?>"> 
                <img src="/frontend/Default/img/_src/WelcomeBonus3.png" alt="<?php echo e($game->title); ?>">
                <?php if($game->jackpot): ?>
                    <span class="label label-d label--left">
											<?php echo e(number_format($game->jackpot->balance, 2,".","")); ?> <?php echo e($currency); ?>

										</span>
                <?php endif; ?>
                <?php if($game->label): ?>
                    <span class="label <?php if($game->label == 'New'): ?>label-b <?php elseif($game->label == 'Hot'): ?>label-g <?php else: ?> label-d <?php endif; ?>"><?php echo e(mb_strtoupper($game->label)); ?></span>
                <?php endif; ?>
                <?php if(Auth::check()): ?>
                <a href="<?php echo e(route('frontend.game.go', $game->name)); ?>?api_exit=/" class="play-btn btn">Play</a>
                <?php else: ?>
                <a href="<?php echo e(route('frontend.game.go', $game->name)); ?>/prego?api_exit=/" class="play-btn btn">Demo</a>
                <?php endif; ?>
                <span class="game-name"><?php echo e($game->title); ?></span>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/partials/game.blade.php ENDPATH**/ ?>