<tr>    
    <td>{{ $stat->room->name }}</td>
	<td>{{ $stat->user->username }}</td>
	<td>
		@if ($stat->type == 'add')
		<span class="text-green">{{ abs($stat->sum) }}	</span>
		@else
		<span class="text-red">{{ abs($stat->sum) }}</span>
		@endif		

	</td>
	<td>{{ date(config('app.date_time_format'), strtotime($stat->date_time)) }}</td>
</tr>