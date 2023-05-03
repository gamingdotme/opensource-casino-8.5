@extends('backend.layouts.app')

@section('page-title', trans('app.general_settings'))
@section('page-heading', trans('app.general_settings'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        <div class="box box-default">
            {!! Form::open(['route' => ['backend.settings.list.update', 'categories'], 'id' => 'general-settings-form']) !!}
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.general_settings')</h3>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                @lang('app.use_all_categories')
                            </label>
                            {!! Form::select('use_all_categories', ['0' => __('app.no'), '1' => __('app.yes')], settings('use_all_categories'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.use_my_games')
                            </label>
                            {!! Form::select('use_my_games', ['0' => __('app.no'), '1' => __('app.yes')], settings('use_my_games'), ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                @lang('app.use_new_categories')
                            </label>
                            {!! Form::select('use_new_categories', ['0' => __('app.no'), '1' => __('app.yes')], settings('use_new_categories'), ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            <label>
                                @lang('app.use_hot_categories')
                            </label>
                            {!! Form::select('use_hot_categories', ['0' => __('app.no'), '1' => __('app.yes')], settings('use_hot_categories'), ['class' => 'form-control']) !!}
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