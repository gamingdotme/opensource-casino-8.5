@extends('backend.layouts.app')

@section('page-title', trans('app.choose_payment_system'))
@section('page-heading', trans('app.choose_payment_system'))

@section('content')
    <section class="content-header">
        @include('backend.partials.messages')
    </section>
    <section class="content">

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Payment</h3>
                <p>You will be rediracted to paymant page in 5-7 second!</p>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        @if( is_array($data) )
                        <form action="{{ $data['action'] }}" method="{{ $data['method'] }}" id="internalForm">
                            {!! Form::token() !!}
                            @foreach($data['fields'] AS $field=>$value)
                                <input type="hidden" name="{{ $field }}" value="{{ $value }}">
                            @endforeach
                            <button type="submit" class="btn btn-success" >OK</button>
                        </form>
                            @else
                        {!! $data !!}
                            @endif
                    </div>
                </div>
            </div>
        </div>

    </section>
@stop

@section('scripts')
    <script type="text/javascript">
        setTimeout(function () {
            //$('form#internalForm').submit();
        }, 5000);
    </script>
@stop