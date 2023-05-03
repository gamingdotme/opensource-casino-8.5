@extends('backend.layouts.app')

@section('page-title', trans('app.edit_progress'))
@section('page-heading', $progress->id)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">

        {!! Form::open(['route' => array('backend.progress.update', $progress->id), 'files' => true, 'id' => 'user-form']) !!}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.progress_details')</h3>
            </div>

            <div class="box-body">
                <div class="row">

                    @include('backend.progress.partials.base', ['edit' => true])

                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_progress')
                </button>
            </div>
        </div>
        {!! Form::close() !!}

    </section>

@stop

@section('scripts')
    <script>
        $(function() {
        });
    </script>
@stop