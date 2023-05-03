<tr>        
    <td>
		{{ number_format ($credit->credit, 2, ".", "") }}
	</td>
	<td>{{ number_format ($credit->price, 2, ".", "") }} @if(auth()->user()->shop) {{ auth()->user()->shop->currency }} @endif</td>
	<td>
		@if( !auth()->user()->hasRole('admin') )
			<a href="{{ route('backend.credit.buy', $credit->id) }}" class="btn btn-success">@lang('app.buy')</a>
		@endif
		@if( auth()->user()->hasRole('admin') )
			<a href="{{ route('backend.credit.edit', $credit->id) }}" class="btn btn-success">@lang('app.edit_credit')</a>
		@endif
	</td>

</tr>