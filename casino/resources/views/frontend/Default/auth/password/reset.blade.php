@extends('frontend.Default.layouts.app')

@section('page-title', trans('app.reset_your_password'))

@section('content')

  @include('backend.partials.messages')

  <!-- LOGIN BEGIN -->
  <div class="login" style="background-image: url('/frontend/Default/img/_src/redirected-bg.png')">
    <div class="login__block">
      <div class="login__left">

        <form class="login-form" action="<?= route('frontend.password.reset.post') ?>" id="register-form" method="POST">
          <input type="hidden" value="<?= csrf_token() ?>" name="_token">
          <div class="input__group">
            <input type="text" id="email" name="email" placeholder="@lang('app.email')" class="loginInput">
          </div>
		  <div class="input__group">
            <input type="password" id="password" name="password" placeholder="@lang('app.password')" class="loginInput">
          </div>
          <div class="input__group">
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="@lang('app.confirm_password')" class="loginInput">
          </div>
		  <input type="hidden" name="token" value="{{$token}}">
          <button type="submit" class="login-btn btn">@lang('app.log_in')</button>
        </form>
      </div>
    </div>
  </div>
  <!-- LOGIN END -->


@stop

@section('scripts')
  {!! JsValidator::formRequest('VanguardLTE\Http\Requests\Auth\RegisterRequest', '#register-form') !!}
@stop