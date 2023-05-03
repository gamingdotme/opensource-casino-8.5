@extends('backend.layouts.app')

@section('page-title', trans('app.edit_refund'))
@section('page-heading', $refund->min_pay . ' ' . $refund->max_pay)

@section('content')

  <section class="content-header">
    @include('backend.partials.messages')
  </section>

  <section class="content">

    <div class="box box-default">
      {!! Form::open(['route' => array('backend.refunds.update', $refund->id), 'files' => true, 'id' => 'refund-form']) !!}
      <div class="box-header with-border">
        <h3 class="box-title">@lang('app.edit_refund')</h3>
      </div>

      <div class="box-body">
        <div class="row">
          @include('backend.refunds.partials.base', ['edit' => true])
        </div>
      </div>

      <div class="box-footer">
        <button type="submit" class="btn btn-primary">
          @lang('app.edit_refund')
        </button>

      </div>
      {!! Form::close() !!}
    </div>
  </section>

@stop