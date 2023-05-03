@extends('backend.layouts.app')

@section('page-title', trans('app.edit_refund'))
@section('page-heading', $refund->min_pay . ' ' . $refund->max_pay)

@section('content')
<div class="row wow fadeIn">
    <div class="col-lg-12">
        <section class="content-header">
            @include('backend.partials.messages')
        </section>

        <div class="element-wrapper">
            <div class="element-box">
                <div class="element-info mb-3">
                    <div class="element-info-with-icon">
                        <div class="element-info-icon">
                            <div class="fa fa-pie-chart"></div>
                        </div>
                        <div class="element-info-text">
                            <h5 class="element-inner-header">@lang('app.edit_refund')</h5>
                            <div class="element-inner-desc text-primary">
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <section class="content">

    <div class="box box-default">
      {!! Form::open(['route' => array('backend.refunds.update', $refund->id), 'files' => true, 'id' => 'refund-form']) !!}


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

    </div>
</div>



@stop
