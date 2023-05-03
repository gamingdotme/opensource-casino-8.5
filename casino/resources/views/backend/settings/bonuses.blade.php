@extends('backend.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-default">
            {!! Form::open(['route' => ['backend.settings.list.update', 'bonuses'], 'id' => 'general-settings-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.general_settings')</h3>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>
                                @lang('app.happyhours')
                            </label>
                            {!! Form::select('happyhours_active', ['0' => __('app.no'), '1' => __('app.yes')], auth()->user()->shop && auth()->user()->shop->happyhours_active, ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.progress')
                            </label>
                            {!! Form::select('progress_active', ['0' => __('app.no'), '1' => __('app.yes')], settings('progress_active'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.invite')
                            </label>
                            {!! Form::select('invite_active', ['0' => __('app.no'), '1' => __('app.yes')], settings('invite_active'), ['class' => 'form-control']) !!}
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>
                                @lang('app.welcome_bonuses')
                            </label>
                            {!! Form::select('welcome_bonuses_active', ['0' => __('app.no'), '1' => __('app.yes')], settings('welcome_bonuses_active'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.sms_bonuses')
                            </label>
                            {!! Form::select('sms_bonuses_active', ['0' => __('app.no'), '1' => __('app.yes')], settings('sms_bonuses_active'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.wheelfortune')
                            </label>
                            {!! Form::select('wheelfortune_active', ['0' => __('app.no'), '1' => __('app.yes')], settings('wheelfortune_active'), ['class' => 'form-control']) !!}
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
