@extends('frontend.Default.layouts.app')

@section('page-title', trans('app.login'))

@section('content')

  @include('backend.partials.messages')

  <!-- LOGIN BEGIN -->
  <div class="login" style="background-image: url('/frontend/Default/img/_src/redirected-bg.png')">
    <div class="login__block">
      <div class="login__left">
        <form class="login-form" action="<?= route('frontend.auth.login.post') ?>" id="login-form" method="POST">
          <input type="hidden" value="<?= csrf_token() ?>" name="_token">
          <div class="input__group">
            <input type="text" id="username" name="username" placeholder="@lang('app.email_or_username')" class="loginInput">
          </div>
          <div class="input__group">
            <input type="password" id="password" name="password" placeholder="@lang('app.password')" class="loginInput">
          </div>
          <button type="submit" class="login-btn btn">@lang('app.log_in')</button>
		  <span style="margin-top: 5px;">Forgot password ? <a href="{{url('password/remind')}}" >Click here</a></span> 
		  <br><br>
		  <span style="margin-top: 5px;">Not registered yet? <a href="{{url('register')}}" >Click here</a></span> 


        </form>
      </div>
      <div class="login__right">
        <form name='PINform' autocomplete='off' class="form-pin" >

          <input type='button' class='PINbutton' name='1' value='1'/>
          <input type='button' class='PINbutton' name='2' value='2'/>
          <input type='button' class='PINbutton' name='3' value='3'/>
          <input type='button' class='PINbutton' name='4' value='4'/>
          <input type='button' class='PINbutton' name='5' value='5'/>
          <input type='button' class='PINbutton' name='6' value='6'/>
          <input type='button' class='PINbutton' name='7' value='7'/>
          <input type='button' class='PINbutton' name='8' value='8'/>
          <input type='button' class='PINbutton' name='9' value='9'/>
          <input type='button' class='buttonClear clear' value='C'  onClick=clearForm(this); />
          <input type='button' class='PINbutton' name='0' value='0'/>
          <input type='button' class='buttonClear backspace' value='X' onClick=backspace(this); />
        </form>
      </div>
    </div>
  </div>
  <!-- LOGIN END -->


@stop

@section('scripts')
  {!! JsValidator::formRequest('VanguardLTE\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop