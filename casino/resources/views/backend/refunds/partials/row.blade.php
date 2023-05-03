<tr>        
    <td><a href="{{ route('backend.refunds.edit', $refund->id) }}">{{ $refund->min_pay }}</a></td>
	<td><a href="{{ route('backend.refunds.edit', $refund->id) }}">{{ $refund->max_pay }}</a></td>
	<td><a href="{{ route('backend.refunds.edit', $refund->id) }}">{{ $refund->percent }}</a></td>
	<td>{{ $refund->min_balance }}</td>
	<td>
		@if(!$refund->status)
			<small><i class="fa fa-circle text-red"></i></small>
		@else
			<small><i class="fa fa-circle text-green"></i></small>
		@endif
	</td>
</tr>