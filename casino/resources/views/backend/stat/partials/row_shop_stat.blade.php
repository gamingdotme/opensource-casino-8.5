<tr>
	<td>{{ $stat->user->username }}</td>


	<td>
		@if ($stat->type == 'add')
			<span class="text-green">{{ abs($stat->sum) }}	</span>
		@endif
	</td>
	<td>
		@if ($stat->type != 'add')
			<span class="text-red">{{ abs($stat->sum) }}</span>
		@endif
	</td>
	<td>{{ $stat->shop->name }}</td>
	<td>{{ date(config('app.date_time_format'), strtotime($stat->date_time)) }}</td>
	@if(isset($show_shop) && $show_shop)
		@if($stat->shop)
			<td><a href="{{ route('backend.shop.edit', $stat->shop->id) }}">{{ $stat->shop->name }}</a></td>
		@endif
	@endif
</tr>