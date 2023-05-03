@extends('frontend.Tropicoblack.layouts.auth')

@section('page-title', trans('app.login'))

@section('content')
	   
	   
	  <div class="container d-flex h-100">
    <div class="card w-100 border-0 m-auto" id="login-card">
        <div class="card-body p-3">
            <form id="login-form" action="<?= route('frontend.auth.login.post') ?>" method="post">
                @csrf
               <div class="custom-input-group mb-2">
                    <span class="custom-input-group-icon"><i class="fas fa-user"></i></span>
                    <input id="login-form-username" value="" type="text" name="username" placeholder="@lang('app.email_or_username')"/>

                </div>
                                                <div class="custom-input-group mb-2">
                    <span class="custom-input-group-icon"><i class="fas fa-key"></i></span>
                    <input id="login-form-password" type="password" name="password" placeholder="@lang('app.password')"/>
                </div>
                                <button type="submit" form="login-form" value="@lang('app.log_in')" class="btn primary-btn w-100 mt-4">
                    Log In
                </button>
            </form>
        </div>
    </div>


</div>

	   
@stop

@section('scripts')
  {!! JsValidator::formRequest('VanguardLTE\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop
