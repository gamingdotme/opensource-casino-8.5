@extends('backend.layouts.auth')

@section('page-title', trans('app.login'))

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('backend.dashboard') }}"><span class="logo-lg"><b>{{ settings('app_name') }}</b></span></a>
        </div>

        @include('backend.partials.messages', ['hide_block' => true])

        <div class="login-box-body">

            <form role="form" action="{{ route('2fa') }}" method="POST" id="login-form" autocomplete="off">

                <input type="hidden" value="<?= csrf_token() ?>" name="_token">

                <div class="form-group has-feedback">
                    <input id="one_time_password" type="text" class="form-control" name="one_time_password" placeholder="One Time Password" required autofocus>
                </div>

                <div class="row">
                    <div class="col-xs-12">



                        <button type="submit" class="btn btn-primary btn-block btn-flat" id="btn-login">
                            @lang('app.log_in')
                        </button>

                    </div>
                </div>



            </form>

        </div>
    </div>

@endsection