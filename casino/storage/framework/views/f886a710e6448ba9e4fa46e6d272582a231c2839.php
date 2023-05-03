<tr>
    <td>
		<?php if(auth()->user()->hasRole('admin')): ?>
		<a href="<?php echo e(route('backend.game.edit', $game->id)); ?>">
		<?php endif; ?>

		<?php echo e($game->title); ?>


		<?php if(auth()->user()->hasRole('admin')): ?>
		</a>
		<?php endif; ?>
	</td>

	<?php if (\Auth::user()->hasPermission('games.rtp')) : ?>
		<td><?php echo e($game->stat_in); ?></td>
		<td><?php echo e($game->stat_out); ?></td>
		<td>
			<?php if(($game->stat_in - $game->stat_out) >= 0): ?>
				<span class="text-green">
			<?php else: ?>
				<span class="text-red">
			<?php endif; ?>
			<?php echo e(number_format(abs($game->stat_in-$game->stat_out), 2, '.', '')); ?>

			</span>
		</td>
		<td>
			<?php echo e($game->stat_in > 0 ? number_format(($game->stat_out / $game->stat_in) * 100, 2, '.', '') : '0.00'); ?>

		</td>
	<?php endif; ?>
	<?php if (\Auth::user()->hasPermission('games.show_count')) : ?>
	<td><?php echo e($game->bids); ?></td>
	<?php endif; ?>

	<?php if( auth()->user()->hasRole('admin') ): ?>
		<td><?php echo e($game->denomination); ?></td>
	<?php endif; ?>
	<td>
		<?php if(!$game->view): ?>
			<small><i class="fa fa-circle text-red"></i></small>
		<?php else: ?>
			<small><i class="fa fa-circle text-green"></i></small>
		<?php endif; ?>
	</td>
<td>

		<label class="checkbox-container">
			<input type="checkbox" name="checkbox[<?php echo e($game->id); ?>]">
			<span class="checkmark"></span>
		</label>


</td>
</tr><?php /**PATH /home/betshopus/casino/resources/views/backend/games/partials/row.blade.php ENDPATH**/ ?>