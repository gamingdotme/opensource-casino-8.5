<tr id="row_0" role="row" class="odd">
    <td>
        <a class="btn-link" href="{{ route('backend.user.edit', $user->id) }}">
            {{ $user->username ?: trans('app.n_a') }}
        </a>
    </td>

	@permission('users.balance.manage')
	<td class=" text-center">{{ $user->balance }}</td>
	<!-- <td>{{ $user->bonus }}</td> -->
	<!-- <td>{{ $user->wager }}</td> -->
	<td class=" hidden-lg-down d-none d-xl-table-cell d-lg-table-cell d-md-table-cell  text-center">{{ '' }}</td>
	<td class="  text-center">
		@if(
			(Auth::user()->hasRole('admin') && $user->hasRole(['agent'])) ||
			(Auth::user()->hasRole('agent') && $user->hasRole(['distributor'])) ||
			(Auth::user()->hasRole('cashier') && $user->hasRole('user'))
		)
		<a class="newPayment addPayment" href="#" data-toggle="modal" data-target="#openAddModal" data-id="{{ $user->id }}" >
		<button type="button" class="btn btn-primary btn inmodalIn pointer"><i class="fa fa-plus"></i></button>
		</a>
		@else
			<button type="button" class="btn btn-primary btn inmodalIn pointer disabled"><i class="fa fa-plus"></i></button>
        @endif
        @if(
    		$user->wager == 0 &&
    		(
				(Auth::user()->hasRole('admin') && $user->hasRole(['agent'])) ||
				(Auth::user()->hasRole('agent') && $user->hasRole(['distributor'])) ||
				(Auth::user()->hasRole('cashier') && $user->hasRole('user'))
			)
		)
		<a class="newPayment outPayment" href="#" data-toggle="modal" data-target="#openOutModal" data-id="{{ $user->id }}" >
		<button type="button" class="btn btn-danger btn pointer"><i class="fa fa-minus"></i></button>
		</a>
		@else
			<button type="button" class="btn btn-danger btn pointer disabled"><i class="fa fa-minus"></i></button>
		@endif
	</td>

    @endpermission

	@if(isset($show_shop) && $show_shop)
		@if($user->shop)
			<td><a href="{{ route('backend.shop.edit', $user->shop->id) }}">{{ $user->shop->name }}</a></td>
			@else
			<td></td>
		@endif
	@endif
</tr>
