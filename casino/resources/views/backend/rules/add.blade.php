@extends('backend.layouts.app')

@section('page-title', trans('app.add_rule'))
@section('page-heading', trans('app.add_rule'))

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['route' => 'backend.rule.store', 'files' => true, 'id' => 'user-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">@lang('app.add_rule')</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.rules.partials.base', ['edit' => false])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
            @lang('app.add_rule')
        </button>
        </div>
		{!! Form::close() !!}
      </div>
    </section>

@stop

@section('scripts')
  <script>
    initSample();
  </script>
@stop