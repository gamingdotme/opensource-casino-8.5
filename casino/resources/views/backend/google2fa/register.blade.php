@extends('backend.layouts.auth')

@section('page-title', trans('app.login'))

@section('content')


    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('backend.dashboard') }}"><span class="logo-lg"><b>{{ settings('app_name') }}</b></span></a>
        </div>

        @include('backend.partials.messages', ['hide_block' => true])

        <div class="login-box-body">

            <h4>Set up Google Authenticator</h4>


                <div class="row">
                    <div class="col-xs-12">

                        <div class="panel-body" style="text-align: center;">
                            <p>Set up your two factor authentication by scanning the barcode below. Alternatively, you can use the code {{ $secret }}</p>
                            <div>
                                <img src="{{ $QR_Image }}">
                            </div>
                            <p>You must set up your Google Authenticator app before continuing. You will be unable to login otherwise</p>
                            <div>

                                <a href="{{ route('backend.dashboard') }}"><button class="btn-primary">Enter code</button></a>
                            </div>
                        </div>

                    </div>
                </div>

        </div>
    </div>


@endsection