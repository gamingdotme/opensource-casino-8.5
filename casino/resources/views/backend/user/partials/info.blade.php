
<div class="col-md-3">

    <div class="box box-primary">
        <div class="box-body box-profile">
            <img class="profile-user-img img-responsive img-circle" src="/back/img/{{ $user->present()->role_id }}.png" alt="{{ $user->present()->username }}">
            <h4 class="profile-username text-center">{{ $user->present()->username }}</h4>
            <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                    <b>@lang('app.balance')</b> <a class="pull-right">{{ $user->present()->balance }}</a>
                </li>

                @if( $user->hasRole('user') )
                    <li class="list-group-item">
                        <b>@lang('app.in')</b> <a class="pull-right">{{ number_format($user->present()->total_in,2) }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>@lang('app.out')</b> <a class="pull-right">{{ number_format($user->present()->total_out,2) }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>@lang('app.total')</b> <a class="pull-right">{{ number_format($user->present()->total_in - $user->present()->total_out,2) }}</a>
                    </li>
                @endif


            </ul>


            @if( $user->id != Auth::id() )
                @if(

                    (auth()->user()->hasRole('agent') && $user->hasRole('distributor'))
                    ||
                    (auth()->user()->hasRole('distributor') && $user->hasRole('manager'))
                    ||
                    (auth()->user()->hasRole('manager') && $user->hasRole('cashier'))
                    ||
                    (auth()->user()->hasRole('cashier') && $user->hasRole('user'))
                )
                    @permission('users.delete')
                    <a href="{{ route('backend.user.delete', $user->id) }}"
                       class="btn btn-danger btn-block"
                       data-method="DELETE"
                       data-confirm-title="@lang('app.please_confirm')"
                       data-confirm-text="@lang('app.are_you_sure_delete_user')"
                       data-confirm-delete="@lang('app.yes_delete_him')">
                        <b>@lang('app.delete')</b></a>
                    @endpermission
                @endif

                @permission('users.delete')
                @if(auth()->user()->hasRole('admin') && $user->hasRole(['agent','distributor']) )
                    <a href="{{ route('backend.user.hard_delete', $user->id) }}"
                       class="btn btn-danger btn-block"
                       data-method="DELETE"
                       data-confirm-title="@lang('app.please_confirm')"
                       data-confirm-text="@lang('app.are_you_sure_delete_user')"
                       data-confirm-delete="@lang('app.yes_delete_him')">
                        <b>@lang('app.hard_delete') {{ $user->role->name }}</b></a>
                @endif
                @endpermission

            @endif
        </div>
    </div>

    @if(!$user->hasRole('admin'))
    <div>

        @if(
                    (Auth::user()->hasRole('admin') && $user->hasRole(['agent'])) ||
                    (Auth::user()->hasRole('agent') && $user->hasRole(['distributor'])) ||
                    (Auth::user()->hasRole('cashier') && $user->hasRole('user'))
                )
            <button type="button" class="btn btn-block btn-success btn-xs newPayment addPayment" data-toggle="modal" data-target="#openAddModal" data-id="{{ $user->id }}">@lang('app.add')</button>
        @else
            <button type="button" class="btn btn-block btn-success disabled btn-xs">@lang('app.add')</button>
        @endif


        @if(
            (
				(Auth::user()->hasRole('admin') && $user->hasRole(['agent'])) ||
				(Auth::user()->hasRole('agent') && $user->hasRole(['distributor'])) ||
				(Auth::user()->hasRole('cashier') && $user->hasRole('user'))
			)
			&&
			!( $user->count_tournaments > 0 || $user->count_happyhours > 0 || $user->count_refunds > 0 ||
                $user->count_progress > 0 || $user->count_daily_entries > 0 || $user->count_invite > 0 ||
                $user->count_welcomebonus > 0 || $user->count_smsbonus > 0 || $user->count_wheelfortune > 0 ||
                $user->status == \VanguardLTE\Support\Enum\UserStatus::BANNED
            )

        )
            <button type="button" class="btn btn-block btn-danger btn-xs newPayment outPayment" data-toggle="modal" data-target="#openOutModal" data-id="{{ $user->id }}">@lang('app.out')</button>
        @else
            <button type="button" class="btn btn-block btn-danger disabled btn-xs">@lang('app.out')</button>
        @endif

    </div><br />
    @endif

    @if($user->hasRole('admin'))

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.google_2fa')</h3>
            </div>
            <div class="box-body">

                <div class="form-group">
                    <label>@lang('app.enable')</label>
                    {!! Form::select('google2fa_enable', [0 => __('app.disabled'), 1 => __('app.active')], $user->google2fa_enable, ['class' => 'form-control']) !!}
                    <input value="{{ $secret }}" type="hidden" name="secret_key">
                </div>

                @if(
                    ($user->google2fa_secret == null && $user->google2fa_enable) ||
                    ($user->google2fa_secret != null && !$user->google2fa_enable)
                )
                    <div class="form-group">
                        <label>@lang('app.code')</label>
                        <input type="text" name="google_2fa_code"  value="" class="form-control">
                    </div>
                @endif

                @if($QR_Image)
                    @if($user->google2fa_secret == '')
                        <p>Set up your two factor authentication by scanning the barcode below. Alternatively, you can use the code {{ $secret }}</p>
                        <div>
                            <img src="{{ $QR_Image }}">
                        </div>
                        <p>You must set up your Google Authenticator app before continuing. You will be unable to login otherwise</p>
                    @endif
                @endif

            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary" id="update-details-btn">
                    @lang('app.edit_user')
                </button>
            </div>
        </div>


    @endif





</div>
