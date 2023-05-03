<tr>
    <td>
		@if(auth()->user()->hasRole('admin'))
		<a href="{{ route('backend.game.edit', $game->id) }}">
		@endif

		{{ $game->title }}

		@if(auth()->user()->hasRole('admin'))
		</a>
		@endif
	</td>

	@permission('games.rtp')
		<td>{{ $game->stat_in }}</td>
		<td>{{ $game->stat_out }}</td>
		<td>
			@if(($game->stat_in - $game->stat_out) >= 0)
				<span class="text-green">
			@else
				<span class="text-red">
			@endif
			{{ number_format(abs($game->stat_in-$game->stat_out), 2, '.', '') }}
			</span>
		</td>
		<td>
			{{ $game->stat_in > 0 ? number_format(($game->stat_out / $game->stat_in) * 100, 2, '.', '') : '0.00' }}
		</td>
	@endpermission
	@permission('games.show_count')
	<td>{{ $game->bids }}</td>
	@endpermission

	@if( auth()->user()->hasRole('admin') )
		<td>{{ $game->denomination }}</td>
	@endif
	<td>
		@if(!$game->view)
			<small><i class="fa fa-circle text-red"></i></small>
		@else
			<small><i class="fa fa-circle text-green"></i></small>
		@endif
	</td>
<td>

		<label class="checkbox-container">
			<input type="checkbox" name="checkbox[{{ $game->id }}]">
			<span class="checkmark"></span>
		</label>


</td>
</tr>