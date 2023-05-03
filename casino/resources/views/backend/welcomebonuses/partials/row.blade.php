<tr>
	<td><a href="{{ route('backend.welcome_bonus.edit', $welcome_bonus->id) }}">{{ mb_convert_case($welcome_bonus->pay, MB_CASE_TITLE) }}</a></td>
	<td>{{ $welcome_bonus->sum }}</td>
	<td>{{ \VanguardLTE\WelcomeBonus::$values['type'][$welcome_bonus->type] }}</td>
	<td>{{ $welcome_bonus->bonus }}</td>
	<td>x{{ $welcome_bonus->wager }}</td>

</tr>
