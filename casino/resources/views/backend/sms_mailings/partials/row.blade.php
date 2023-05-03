<tr>
	<td>{{ $mailing->id }}</td>
	<td>
		<a href="{{ route('backend.sms_mailing.edit', $mailing->id) }}">{{ $mailing->theme }}</a>
	</td>
	<td>{{ str_replace('|', ', ', $mailing->roles) }}</td>
	<td>{{ str_replace('|', ', ', $mailing->statuses) }}</td>
	<td>{{ $mailing->date_start }}</td>
	<td>{{ $mailing->sms_messages? $mailing->sms_messages->where('sent',1)->count() . '/' . $mailing->sms_messages->count(): '0/0' }}</td>
	<td>

		@if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($mailing->date_start), false) >= 0 )
			<i class="fa fa-circle text-yellow"></i>
		@else
			@if( $mailing->sms_messages && $mailing->sms_messages->where('sent',1)->count() == $mailing->sms_messages->count() )
				<i class="fa fa-circle text-red"></i>
			@else
				<i class="fa fa-circle text-green"></i>
			@endif
		@endif

	</td>
</tr>