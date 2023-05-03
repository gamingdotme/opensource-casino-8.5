@extends('frontend.Default.layouts.app')

@section('page-title', trans('app.reset_password'))

@section('content')

  @include('backend.partials.messages')

  <!-- LOGIN BEGIN -->
  <div class="login" style="background-image: url('/frontend/Default/img/_src/redirected-bg.png')">
    <div class="login__block">
      <div class="login__left">

        <form class="login-form" action="<?= route('frontend.password.remind.post') ?>" id="register-form" method="POST">
          <input type="hidden" value="<?= csrf_token() ?>" name="_token">
          <div class="input__group">
            <input type="text" id="email" name="email" placeholder="@lang('app.email')" class="loginInput">
          </div>
          <button type="submit" class="login-btn btn">@lang('app.log_in')</button>
		  <span style="margin-top: 5px;">Back to login ? <a href="{{url('login')}}" >Click here</a></span> 
        </form>
      </div>
    </div>
  </div>
  <!-- LOGIN END -->


@stop

@section('scripts')
  {!! JsValidator::formRequest('VanguardLTE\Http\Requests\Auth\RegisterRequest', '#register-form') !!}
@stop