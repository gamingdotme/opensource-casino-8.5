
<div class="col-md-3">

    <div class="box box-primary">
        <div class="box-body box-profile">
            <img class="profile-user-img img-fluid rounded-circle" src="/back/img/{{ $user->present()->role_id }}.png" alt="{{ $user->present()->username }}">
            <h4 class="profile-username text-center"><small><i class="fa fa-circle text-{{ $user->present()->labelClass }}"></i></small> {{ $user->present()->username }}</h4>
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


</div>
