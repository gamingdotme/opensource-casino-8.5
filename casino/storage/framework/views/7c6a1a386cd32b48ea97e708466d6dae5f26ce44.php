<tr>        
    <td>
        <?php if (\Auth::user()->hasPermission('tournaments.edit')) : ?>
            <a href="<?php echo e(route('backend.tournament.edit', $tournament->id)); ?>"><?php echo e($tournament->id); ?></a>
        <?php else: ?>
            <?php echo e($tournament->id); ?>

        <?php endif; ?>
    </td>
    <td>
        <?php if (\Auth::user()->hasPermission('tournaments.edit')) : ?>
        <a href="<?php echo e(route('backend.tournament.edit', $tournament->id)); ?>"><?php echo e($tournament->name); ?></a>
        <?php else: ?>
            <?php echo e($tournament->id); ?>

            <?php endif; ?>
    </td>
    <td><?php echo e($tournament->start); ?></td>
    <td><?php echo e($tournament->end); ?></td>
    <td><?php echo e(\VanguardLTE\Tournament::$values['type'][$tournament->type]); ?></td>
    <td><?php echo e(number_format($tournament->sum_prizes, 2, '.', '')); ?></td>
    <td><?php echo e(number_format($tournament->bet, 2, '.', '')); ?></td>
    <td><?php echo e($tournament->spins); ?></td>
    <td>x<?php echo e($tournament->wager); ?></td>
    <td>
        <?php if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->start), false) >= 0 ): ?>
            <i class="fa fa-circle text-yellow"></i>
        <?php elseif(  \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->end), false) <= 0 ): ?>
            <i class="fa fa-circle text-red"></i>
        <?php else: ?>
            <i class="fa fa-circle text-green"></i>
        <?php endif; ?>
    </td>
</tr><?php /**PATH /home/betshopus/casino/resources/views/backend/tournaments/partials/row.blade.php ENDPATH**/ ?>