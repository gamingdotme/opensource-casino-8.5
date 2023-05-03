<tr>
	<td>
		<a href="{{ route('backend.statistics', ['system_str' => $stat->admin ? $stat->admin->username : $stat->system])  }}">
			{{ $stat->admin ? $stat->admin->username : $stat->system }}
		</a>
		@if( $stat->value ) {{ $stat->value }} @endif
	</td>
<td>
		@if ($stat->type == 'add')
			<span class="text-green">{{ abs($stat->summ) }}</span>
		@endif
</td>
	<td>
		@if ($stat->type != 'add')
			<span class="text-red">{{ abs($stat->summ) }}</span>
		@endif
	</td>
	<td>
		<a href="{{ route('backend.statistics', ['user' => $stat->user->username])  }}">
			{{ $stat->user->username }}
		</a>
	</td>

<td>{{ $stat->created_at->format(config('app.date_time_format')) }}</td>
	@if(isset($show_shop) && $show_shop)
		@if($stat->shop)
			<td><a href="{{ route('backend.shop.edit', $stat->shop->id) }}">{{ $stat->shop->name }}</a></td>
			@else
			<td>@lang('app.no_shop')</td>
		@endif
	@endif
</tr>
