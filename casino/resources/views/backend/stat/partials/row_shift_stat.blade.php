<tr>
	@if(!auth()->user()->hasRole('cashier'))
	<td>{{ $stat->id }}</td>
	@endif
	<td>{{ $stat->user->username }}</td>
	<td>{{ date(config('app.date_time_format'), strtotime($stat->start_date)) }}</td>
	<td>{{ $stat->end_date ? date(config('app.date_time_format'), strtotime($stat->end_date)) : '' }}</td>
	@if(!auth()->user()->hasRole('cashier'))
		<td>{{ $stat->balance }}</td>
		<td>{{ $stat->balance_in }}</td>
		<td>{{ $stat->balance_out }}</td>
		<td>{{ number_format ((float)$stat->balance + (float)$stat->balance_in - (float)$stat->balance_out, 4, ".", "") }}</td>
	@endif

	@php
		$money = $stat->users;
		if($stat->end_date == NULL){
			$money = $stat->get_money();
		}
	@endphp

	<td>{{ $money }}</td>
	<td>{{ $stat->money_in }}</td>
	<td>{{ $stat->money_out }}</td>

	@php
		$total = (float)$stat->money_in - (float)$stat->money_out;
	@endphp

	<td>{{ number_format ($total, 4, ".", "") }}</td>
	<td>{{ $stat->transfers }}</td>

	@php
		$payout = (float)$stat->money_in > 0 ? ((float)$stat->money_out / (float)$stat->money_in) * 100 : 0;
	@endphp
	<td>{{ number_format ($payout, 4, ".", "") }}</td>

</tr>
