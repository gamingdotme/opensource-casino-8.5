<tr>
    <td>
        <?php if( auth()->user()->hasPermission('progress.edit') ): ?>
            <a href="<?php echo e(route('backend.progress.edit', $item->id)); ?>"><?php echo e($item->rating); ?></a>
        <?php else: ?>
            <?php echo e($item->rating); ?>

        <?php endif; ?>
    </td>
	<td><?php echo e($item->sum); ?></td>
	<td><?php echo e(__('app.' . $item->type)); ?></td>
	<td><?php echo e($item->spins); ?></td>
	<td><?php echo e($item->bet); ?></td>
	<td><?php echo e($item->bonus); ?></td>
	<td><?php echo e($item->day); ?></td>
	<td><?php echo e($item->min); ?></td>
	<td><?php echo e($item->max); ?></td>
	<td><?php echo e($item->percent); ?></td>
	<td><?php echo e($item->min_balance); ?></td>
	<td>x<?php echo e($item->wager); ?></td>
    <td><?php echo e($item->days_active); ?></td>
</tr>
<?php /**PATH /home/betshopus/casino/resources/views/backend/progress/partials/row.blade.php ENDPATH**/ ?>