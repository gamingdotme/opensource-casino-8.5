@extends('backend.layouts.app')

@section('page-title', trans('app.terminal'))
@section('page-heading', trans('app.terminal'))

@section('content')

<section class="content-header">
    @include('backend.partials.messages')
</section>

<section class="content">
    <div class="subuheader">
        <div class="row">
            <div class="col-md-6">
                <div class="heading">@lang('app.atm')</div>
            </div>
            <div class="col-md-6 text-right">
                @if ($response['atms'])
                @if ($response['atms']->atm_status=='active')
                <a href="{{url('backend/atm/status/'.encoded('inactive'))}}" class="btn btn-success"
                    onclick="return confirm('Are you sure to inactive this ATM?')"><i class="fa fa-check-circle"></i>
                    Active</a>
                @else
                <a href="{{url('backend/atm/status/'.encoded('active'))}}" class="btn btn-warning"
                    onclick="return confirm('Are you sure to active this ATM?')"><i class="fa fa-times-circle"></i> In
                    Active</a>
                @endif
                @endif
            </div>
        </div>
    </div>
    <div class="tableList">
        <table class="table bg-white text-center">
            <thead>
                <tr>
                    <th scope="col">ATM Name</th>
                    <th scope="col">In</th>
                    <th scope="col">Out</th>
                    <th scope="col">Recycle</th>
                    <th scope="col">Rec 5{{$response['shop']->currency}}</th>
                    <th scope="col">Rec 10{{$response['shop']->currency}}</th>
                    <th scope="col">Rec 20{{$response['shop']->currency}}</th>
                    <th scope="col">Rec 50{{$response['shop']->currency}}</th>
                    <th scope="col">Rec 100{{$response['shop']->currency}}</th>
                    <th scope="col">Rec 200{{$response['shop']->currency}}</th>
                </tr>
            </thead>
            <tbody>
                @if ($response['atms'])
                <tr class="fw-bold">
                    <td>{{$response['atms']->atm_name}}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_in), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_out), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_recycle), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_rec_5), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_rec_10), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_rec_20), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_rec_50), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_rec_100), 2, '.', '') }}</td>
                    <td>{{ number_format(floatval($response['atms']->atm_rec_200), 2, '.', '') }}</td>
                </tr>
                @else
                <tr>
                    <td colspan="10">
                        <div class="noData">
                            No ATM found! <a href="{{url('backend/atm/create')}}" class="btn btn-danger btn-sm">Create
                                new ATM</a>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
        @if ($response['atms'])
        <div class="text-center">
            <a href="{{url('backend/atm/reset')}}" class="btn btn-primary"
                onclick="return confirm('Are you sure to reset ATP?')"><i class="fa fa-history"></i> Reset</a>
            <a href="{{url('backend/atm/newkey/'.encoded($response['atms']->api_key_id))}}"
                class="btn btn-success ml-1 mr-1" onclick="return confirm('Are you sure to generate new key?')"><i
                    class="fa fa-key"></i> New KEY</a>
            <a href="{{url('backend/atm/delete/'.encoded($response['atms']->id).'/'.encoded($response['atms']->api_key_id))}}"
                class="btn btn-danger " onclick="return confirm('Are you sure to delete ATP?')"><i
                    class="fa fa-trash"></i> Delete</a>
        </div>
        @endif
    </div>
</section>
@stop

@section('scripts')

@stop