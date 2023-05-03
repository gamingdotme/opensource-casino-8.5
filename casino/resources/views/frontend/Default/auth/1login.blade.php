@extends('frontend.Default.layouts.app')

@section('page-title', trans('app.login'))
@section('add-main-class', 'main-login')

@section('content')

    <!-- MAIN -->

        <!-- LOGIN BEGIN -->
		
		
        <div class="login">
		
            <div class="login__block">
			
                <div class="login__left">
                    <form action="<?= route('frontend.auth.login.post') ?>" class="login-form" method="POST">
                        @csrf
                        <div class="input__group">
                            <input type="text" id="inputUser" placeholder="@lang('app.email_or_username')" class="loginInput" name="username">
                        </div>
                        <div class="input__group">
                            <input type="password" id="inputPass" placeholder="@lang('app.password')" class="loginInput" name="password">
                        </div>
                        <button type="submit" id="submit" class="login-btn btn">@lang('app.log_in')</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- LOGIN END -->
    <!-- /.MAIN -->

    @if(isset ($errors) && count($errors) > 0)
        <div class="notification">
            <div class="notification__message notification__message_failed _active">
                <img src="/frontend/Default/img/svg/!.svg" alt="">
                <p class="notification__title">Error</p>
                <p class="notification__text">
                    @foreach($errors->all() as $error)
                        {!!  $error  !!}<br>
                    @endforeach
                </p>
                <button class="notification__close">&times;</button>
            </div>
        </div>
    @endif

@stop

@section('scripts')
    <script type="text/javascript">
        setTimeout(function () {
            $('.notification__message_failed').removeClass('_active');
        }, 3000);
    </script>
  {!! JsValidator::formRequest('VanguardLTE\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop
