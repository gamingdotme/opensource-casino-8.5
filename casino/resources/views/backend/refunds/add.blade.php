@extends('backend.layouts.app')

@section('page-title', trans('app.add_refund'))
@section('page-heading', trans('app.add_refund'))

@section('content')

<section class="content-header">
@include('backend.partials.messages')
</section>

    <section class="content">
      <div class="box box-default">
		{!! Form::open(['route' => 'backend.refunds.store', 'files' => true, 'id' => 'refund-form']) !!}
        <div class="box-header with-border">
          <h3 class="box-title">@lang('app.add_refund')</h3>
        </div>

        <div class="box-body">
          <div class="row">
            @include('backend.refunds.partials.base', ['edit' => false, 'profile' => false])
          </div>
        </div>

        <div class="box-footer">
        <button type="submit" class="btn btn-primary">
            @lang('app.add_refund')
        </button>
        </div>
		{!! Form::close() !!}
      </div>
    </section>

@stop