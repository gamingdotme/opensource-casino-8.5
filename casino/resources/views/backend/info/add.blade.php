@extends('backend.layouts.app')

@section('page-title', trans('app.add_info'))
@section('page-heading', trans('app.add_info'))

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
            {!! Form::open(['route' => 'backend.info.store', 'files' => true, 'id' => 'info-form']) !!}
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title">@lang('app.add_info')</h3>
        </div>

        <div class="box-body">

            @include('backend.info.partials.base', ['edit' => false, 'profile' => false])

        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary">
                @lang('app.add_info')
            </button>
        </div>
      </div>
            {!! Form::close() !!}
    </section>

@stop

@section('scripts')
    <script>
        initSample();
    </script>
@stop