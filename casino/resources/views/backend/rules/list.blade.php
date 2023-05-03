@extends('backend.layouts.app')

@section('page-title', trans('app.rules'))
@section('page-heading', trans('app.rules'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">@lang('app.rules')</h3>

            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@lang('app.title')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if (count($rules))
                            @foreach ($rules as $rule)
                                @include('backend.rules.partials.row')
                            @endforeach
                        @else
                            <tr><td colspan="3">@lang('app.no_data')</td></tr>
                        @endif
                        </tbody>
                        <thead>
                        <tr>
                            <th>@lang('app.id')</th>
                            <th>@lang('app.title')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>





@stop

@section('scripts')
    <script>
        $('#rules-table').dataTable();
        $("#status").change(function () {
            $("#users-form").submit();
        });
    </script>
@stop
