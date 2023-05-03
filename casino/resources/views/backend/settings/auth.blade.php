@extends('backend.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-default">
            {!! Form::open(['route' => ['backend.settings.list.update', 'auth'], 'id' => 'general-settings-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.general_settings')</h3>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                @lang('app.reset_token_lifetime')
                            </label>
                            <input type="number" step="0.0000001" name="login_reset_token_lifetime" class="form-control" value="{{ settings('login_reset_token_lifetime', 30) }}">
                        </div>
                        <div class="form-group">
                            <label>
                                @lang('app.use_email')
                            </label>
                            {!! Form::select('use_email', ['0' => __('app.no'), '1' => __('app.yes')], settings('use_email'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.reset_authentication')
                            </label>
                            {!! Form::select('reset_authentication', ['0' => __('app.no'), '1' => __('app.yes')], settings('reset_authentication'), ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label>
                                @lang('app.maximum_number_of_attempts')
                                <small class="text-muted">@lang('app.max_number_of_incorrect_login_attempts')</small>
                            </label>
                            <input type="number" step="0.0000001" name="throttle_attempts" class="form-control" value="{{ settings('throttle_attempts', 10) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.lockout_time')</label>
                            <input type="number" step="0.0000001" name="throttle_lockout_time" class="form-control" value="{{ settings('throttle_lockout_time', 1) }}">
                        </div>
                        <div class="form-group">
                            <label>@lang('app.throttle_authentication')</label>
                            {!! Form::select('throttle_enabled', ['0' => __('app.no'), '1' => __('app.yes')], settings('throttle_enabled'), ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group">
                            <label>@lang('app.allow_registration')</label>
                            {!! Form::select('reg_enabled', ['0' => __('app.no'), '1' => __('app.yes')], settings('reg_enabled'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>@lang('app.forgot_password')</label>
                            {!! Form::select('forgot_password', ['0' => __('app.no'), '1' => __('app.yes')], settings('forgot_password'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_settings')
                </button>



            </div>
            {{ Form::close() }}
        </div>
    </section>

@stop
