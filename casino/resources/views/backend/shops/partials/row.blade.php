<tr>        
    <td>
		<a href="{{ route('backend.shop.edit', $shop->shop_id) }}">{{ $shop->name }}</a>
	</td>
	<td>
		<a href="{{ route('backend.profile.setshop', ['shop_id' => $shop->shop_id]) }}">@lang('app.switch')</a>
	</td>
	<td>
		@if($shop->creator)
		<a href="{{ route('backend.user.edit', $shop->creator->id) }}" >{{ $shop->creator->username }}</a>
		@endif
	</td>
	<td><a href="{{ route('frontend.jpstv', $shop->shop_id) }}" target="_blank">{{ $shop->shop_id }}</a></td>
    <td>{{ $shop->balance }}</td>
	<td>{{ $shop->get_percent_label($shop->percent) }}</td>
	<td>{{ $shop->max_win }}</td>
	<td>{{ $shop->frontend }}</td>
	<td>{{ $shop->currency }}</td>
	<td>{{ $shop->orderby }}</td>
	<td>
		@if($shop->is_blocked)
			<small><i class="fa fa-circle text-red"></i></small>
		@else
			<small><i class="fa fa-circle text-green"></i></small>
		@endif
	</td>
	<td>
		@if( Auth::user()->hasRole(['distributor']) )
		
		<a class="addPayment" href="#" data-toggle="modal" data-target="#openAddModal" data-id="{{ $shop->shop_id }}" >
		<button type="button" class="btn btn-block btn-success btn-xs"> @lang('app.add')</button>
	    </a>
		@elseif( Auth::user()->hasRole(['agent']) )
		<button type="button" class="btn btn-block btn-success hidden btn-xs"> @lang('app.add')</button>
		
		@else
			<button type="button" class="btn btn-block btn-success disabled btn-xs"> @lang('app.add')</button>
		@endif
	</td>
	<td>
		@if( Auth::user()->hasRole(['distributor']) )
		<a class="outPayment" href="#" data-toggle="modal" data-target="#openOutModal" data-id="{{ $shop->shop_id }}" >
	    <button type="button" class="btn btn-block btn-danger btn-xs"> @lang('app.out')</button>
		</a>
		@elseif( Auth::user()->hasRole(['agent']) )
		<button type="button" class="btn btn-block btn-danger hidden btn-xs"> @lang('app.out')</button>
		@else
			<button type="button" class="btn btn-block btn-danger disabled btn-xs"> @lang('app.out')</button>
		@endif
	</td>
</tr>