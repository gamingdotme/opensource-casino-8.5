<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.username')</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="(@lang('app.optional'))" value="">
    </div>
</div>


<div class="col-md-6">
    <div class="form-group">
        <label>@lang('app.role')</label>
        {!! Form::select('role_id', Auth::user()->available_roles(), '',
            ['class' => 'form-control', 'id' => 'role_id', '']) !!}
    </div>
</div>


@if(auth()->user()->hasRole(['distributor']))
    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('app.shops')</label>
            @if( auth()->user()->hasRole(['admin', 'agent']) )
                {!! Form::select('shop_id', ['0' => '---'] + $shops, '0', ['class' => 'form-control', 'id' => 'shops']) !!}
            @else
                {!! Form::select('shop_id', $shops, '0', ['class' => 'form-control', 'id' => 'shops']) !!}
            @endif
        </div>
    </div>
@endif
@if( auth()->user()->hasRole(['manager', 'cashier']) )
    <input type="hidden" name="shop_id" value="{{ auth()->user()->shop_id }}">
@endif

@if( auth()->user()->hasRole(['cashier']) )
    <div class="col-md-6">
        <div class="form-group">
            <label>{{ trans('app.balance') }}</label>
            <input type="text" class="form-control" id="balance" name="balance" value="0">
        </div>
    </div>
@endif
<div class="col-md-6">
    <div class="form-group">
        <label>{{ trans('app.password') }}</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label>{{ trans('app.confirm_password') }}</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
    </div>
</div>