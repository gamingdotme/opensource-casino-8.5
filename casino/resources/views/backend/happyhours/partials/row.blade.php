<tr>
	<td>{{ $happyhour->id }}</td>
	<td><a href="{{ route('backend.happyhour.edit', $happyhour->id) }}">{{ $happyhour->multiplier }}</a></td>
	<td>x{{ $happyhour->wager }}</td>
	<td>{{ \VanguardLTE\HappyHour::$values['time'][$happyhour->time] }}</td>

</tr>
