<tr>
    <td>
		<a href="{{ route('backend.shop.edit', $shop->shop_id) }}">{{ $shop->name }}</a>
        <div class="clearfix mt-2"></div>
        <a href="{{ route('backend.profile.setshop', ['shop_id' => $shop->shop_id]) }}" class="badge badge-success">@lang('app.switch')</a>
	</td>
	<td>
		@if($shop->creator)
		<a href="{{ route('backend.user.edit', $shop->creator->id) }}" >{{ $shop->creator->username }}</a>
		@endif
	</td>
	<td><a href="{{ route('frontend.jpstv', $shop->shop_id) }}" target="_blank">{{ $shop->shop_id }}</a></td>
    <td>{{ $shop->balance }}</td>
	<td>{{ $shop->percent }}</td>
	<td>{{ $shop->frontend }}</td>
	<td>{{ $shop->currency }}</td>
	<td>{{ $shop->orderby }}</td>
	<td align="center">
		@if($shop->is_blocked)
			<small><i class="fa fa-circle text-danger"></i></small>
		@else
			<small><i class="fa fa-circle text-success"></i></small>
		@endif
	</td>
	<td>
		@if( Auth::user()->hasRole(['distributor']) )

		<a class="addPayment" href="#" data-toggle="modal" data-target="#openAddModal" data-id="{{ $shop->shop_id }}" >
		<button type="button" class="btn btn-success">@lang('app.add')</button>
	    </a>
		@else
			<button type="button" class="btn btn-success disabled">@lang('app.add')</button>
		@endif
	</td>
	<td>
		@if( Auth::user()->hasRole(['distributor']) )
		<a class="outPayment" href="#" data-toggle="modal" data-target="#openOutModal" data-id="{{ $shop->shop_id }}" >
        <button type="button" class="btn btn-danger pointer">@lang('app.out')</button>
		</a>
		@else
			<button type="button" class="btn btn-danger disabled pointer">@lang('app.out')</button>
		@endif
	</td>
</tr>
