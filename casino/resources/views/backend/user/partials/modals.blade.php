<div class="modal fade" id="openAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.user.balance.update') }}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('app.balance') @lang('app.pay_in')</h4>
                </div>
                <div class="modal-body">
                    @if($happyhour && auth()->user()->hasRole('cashier'))
                        <div class="alert alert-success">
                            <h4>@lang('app.happyhours')</h4>
                            <p> @lang('app.all_player_deposits') {{ $happyhour->multiplier }}</p>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="OutSum">@lang('app.sum')</label>
                        <input type="text" class="form-control" id="OutSum" name="summ" placeholder="@lang('app.sum')" required>
                        <input type="hidden" name="type" value="add">
                        <input type="hidden" id="AddId" name="user_id" value="@if(isset($user)){{ $user->id }}@endif">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>

                    @if( auth()->user()->hasRole('admin') && auth()->user()->google2fa_secret != null && auth()->user()->google2fa_enable )
                        <div class="form-group">
                            <label>@lang('app.google_2fa')</label>
                            <input type="text" name="google_2fa_code"  value="" class="form-control">
                        </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('app.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('app.pay_in')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="openOutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('backend.user.balance.update') }}" method="POST" id="outForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('app.balance') @lang('app.pay_out')</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="OutSum">@lang('app.sum')</label>
                        <input type="text" class="form-control" id="OutSum" name="summ" required autofocus>
                        <input type="hidden" name="type" value="out">
                        <input type="hidden" id="outAll" name="all" value="0">
                        <input type="hidden" id="OutId" name="user_id" value="@if(isset($user)){{ $user->id }}@endif">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>

                    @if( auth()->user()->hasRole('admin') && auth()->user()->google2fa_secret != null && auth()->user()->google2fa_enable )
                        <div class="form-group">
                            <label>@lang('app.google_2fa')</label>
                            <input type="text" name="google_2fa_code"  value="" class="form-control">
                        </div>
                    @endif


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('app.close')</button>
                    <button type="button" class="btn btn-danger" id="doOutAll">@lang('app.pay_out') @lang('app.all')</button>
                    <button type="submit" class="btn btn-primary">@lang('app.pay_out')</button>
                </div>
            </form>
        </div>
    </div>
</div>