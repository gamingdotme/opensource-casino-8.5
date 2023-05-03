<tr>
	<td>{{ $jackpot->id }}</td>
	<td>
        @if( auth()->user()->hasPermission('jpgame.edit') )
            <a href="{{ route('backend.jpgame.edit', $jackpot->id) }}">{{ $jackpot->name }}</a>
        @else
            {{ $jackpot->name }}
        @endif
    </td>
	<td>{{ $jackpot->balance }}</td>
	<td>{{ \VanguardLTE\JPG::$values['start_balance'][$jackpot->start_balance] }}</td>
	<td>{{ \VanguardLTE\JPG::$values['pay_sum'][$jackpot->pay_sum] }}</td>
	<td>{{ $jackpot->percent }}</td>
	<td>@if($jackpot->user) <a href="{{ route('backend.user.edit', $jackpot->user->id) }}">{{ $jackpot->user->username }}</a> @else --- @endif</td>
    @if( auth()->user()->hasRole('admin') || auth()->user()->hasPermission('jpgame.edit') )
    <td>
		<label class="checkbox-container">
			<input type="checkbox" name="checkbox[{{ $jackpot->id }}]">
			<span class="checkmark"></span>
		</label>
	</td>
    @endif
</tr>
