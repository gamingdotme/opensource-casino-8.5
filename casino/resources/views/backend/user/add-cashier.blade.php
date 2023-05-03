@extends('backend.layouts.user')

@section('page-title', trans('app.add_user'))
@section('page-heading', trans('app.create_new_user'))

@section('content')



            <!--Grid row-->
            <div class="row wow fadeIn">

                <div class="col-lg-12">
                <section class="content-header">
        @include('backend.partials.messages')
    </section>
                    <div class="element-wrapper">
                        <div class="element-box">
                            <div class="element-info">
                                <div class="element-info-with-icon">
                                    <div class="element-info-icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="element-info-text">
                                        <h5 class="element-inner-header">New user</h5>
                                        <div class="element-inner-desc text-primary">Create new users</div>
                                    </div>
                                </div>
                            </div>

    @if( Auth::user()->hasRole('cashier') )


            @if($happyhour && auth()->user()->hasRole('cashier'))
                <div class="alert alert-success">
                    <h4>@lang('app.happyhours')</h4>
                    <p> @lang('app.all_player_deposits') {{ $happyhour->multiplier }}</p>
                </div>
            @endif


            {!! Form::open(['route' => 'backend.user.massadd', 'files' => true, 'id' => 'mass-user-form']) !!}
            <legend><span>@lang('app.add_user')</span></legend>
            <div class="row">
                <div class="col-sm-6">
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
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>@lang('app.balance')</label>
                        <input type="text" class="form-control" id="title" name="balance" value="0">
                    </div>
                </div>
            </div>

            <div class="form-buttons-w text-right">
                <button type="submit" class="btn btn-primary">
                @lang('app.add_user')
                </button>
            </div>

            {!! Form::close() !!}
        @endif

        {!! Form::open(['route' => 'backend.user.store', 'files' => true, 'id' => 'user-form']) !!}
        <legend><span>
            @lang('app.add_user')
        </span></legend>

                    @include('backend.user.partials.create')

                <div class="form-buttons-w text-right">
                <button type="submit" class="btn btn-primary">
                    @lang('app.add_user')
                </button>
            </div>

        {!! Form::close() !!}
    </div>
    </div>
    </div>
    </div>


    <section class="content">



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
