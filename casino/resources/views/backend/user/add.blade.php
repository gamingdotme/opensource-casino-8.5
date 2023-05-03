@extends('backend.layouts.app')

@section('page-title', trans('app.add_user'))
@section('page-heading', trans('app.create_new_user'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        @if( Auth::user()->hasRole('cashier') )


            @if($happyhour && auth()->user()->hasRole('cashier'))
                <div class="alert alert-success">
                    <h4>@lang('app.happyhours')</h4>
                    <p> @lang('app.all_player_deposits') {{ $happyhour->multiplier }}</p>
                </div>
            @endif


            {!! Form::open(['route' => 'backend.user.massadd', 'files' => true, 'id' => 'mass-user-form']) !!}
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('app.add_user')</h3>
                </div>

                <div class="box-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.count')</label>
                                <select name="count" class="form-control">
                                    <option value="1">1</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('app.balance')</label>
                                <input type="text" class="form-control" id="title" name="balance" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        @lang('app.add_user')
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
        @endif

        {!! Form::open(['route' => 'backend.user.store', 'files' => true, 'id' => 'user-form']) !!}

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.add_user')</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    @include('backend.user.partials.create')

                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.add_user')
                </button>
            </div>
        </div>

        {!! Form::close() !!}

    </section>
@stop

@section('scripts')
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\CreateUserRequest', '#user-form') !!}

    <script>

        $("#role_id").change(function (event) {
            var role_id = parseInt($('#role_id').val());
            $("#parent > option").each(function() {
                var id = parseInt($(this).attr('role'));
                if( (id - role_id) != 1 ){
                    $(this).attr('hidden', true);
                } else{
                    $(this).attr('hidden', false);
                }
                $(this).attr('selected', false);
            });
            $('#parent option[value=""]').attr('selected', true);
        });

        $("#role_id").trigger('change');

    </script>
@stop