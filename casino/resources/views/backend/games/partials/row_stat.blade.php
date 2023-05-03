<tr>
    <td>{{ $stat->game }}</td>
	<td>{{ $stat->user->username }}</td>
	<td><span class="text-green">{{ $stat->balance }}</span></td>
	<td>{{ $stat->bet }}</td>
	<td>{{ $stat->win }}</td>
	@if(auth()->user()->hasRole('admin'))
	<td>{{ $stat->in_game }}</td>
	<td>{{ $stat->in_jpg }}</td>
	<td>{{ $stat->in_profit }}</td>
	@endif
	<td>{{ $stat->denomination }}</td>

	<td>{{ $stat->slots_bank }}</td>
	<td>{{ $stat->fish_bank }}</td>
	<td>{{ $stat->table_bank }}</td>
	<td>{{ $stat->little_bank }}</td>
	<td>{{ $stat->bonus_bank }}</td>
	<td>{{ $stat->total_bank }}</td>

	<td>{{ $stat->date_time }}</td>
    @if(isset($show_shop) && $show_shop)
        @if($stat->shop)
            <td><a href="{{ route('backend.shop.edit', $stat->shop->id) }}">{{ $stat->shop->name }}</a></td>
        @else
            <td>@lang('app.no_shop')</td>
        @endif
    @endif
</tr>