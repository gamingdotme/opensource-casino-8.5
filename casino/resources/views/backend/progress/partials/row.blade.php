<tr>
    <td>
        @if( auth()->user()->hasPermission('progress.edit') )
            <a href="{{ route('backend.progress.edit', $item->id) }}">{{ $item->rating }}</a>
        @else
            {{ $item->rating }}
        @endif
    </td>
	<td>{{ $item->sum }}</td>
	<td>{{ __('app.' . $item->type) }}</td>
	<td>{{ $item->spins }}</td>
	<td>{{ $item->bet }}</td>
	<td>{{ $item->bonus }}</td>
	<td>{{ $item->day }}</td>
	<td>{{ $item->min }}</td>
	<td>{{ $item->max }}</td>
	<td>{{ $item->percent }}</td>
	<td>{{ $item->min_balance }}</td>
	<td>x{{ $item->wager }}</td>
    <td>{{ $item->days_active }}</td>
</tr>
