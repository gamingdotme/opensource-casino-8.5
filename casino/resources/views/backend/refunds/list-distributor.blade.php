@extends('backend.layouts.user')

@section('page-title', trans('app.refunds'))
@section('page-heading', trans('app.refunds'))

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
                            <h5 class="element-inner-header">@lang('app.refunds')</h5>
                            <div class="element-inner-desc text-primary">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 table-responsive p-0">
                    <div id="transactions_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4  p-0 m-0 ">
                        <table class="table table-striped table-bordered table-sm dataTable no-footer">
                            <thead>
                            <tr>
                                <th>@lang('app.min_pay')</th>
                                <th>@lang('app.max_pay')</th>
                                <th>@lang('app.percent')</th>
                                <th>@lang('app.min_balance')</th>
                                <th>@lang('app.status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (count($refunds))
                                @foreach ($refunds as $refund)
                                    @include('backend.refunds.partials.row', ['base' => true])
                                @endforeach
                            @else
                                <tr><td colspan="5">@lang('app.no_data')</td></tr>
                            @endif
                            </tbody>
                            <thead>
                            <tr>
                                <th>@lang('app.min_pay')</th>
                                <th>@lang('app.max_pay')</th>
                                <th>@lang('app.percent')</th>
                                <th>@lang('app.min_balance')</th>
                                <th>@lang('app.status')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@stop
