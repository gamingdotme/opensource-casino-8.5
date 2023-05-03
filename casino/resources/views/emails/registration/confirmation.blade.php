<h1>{{ settings('app_name') }}</h1>
<p>@lang('app.thank_you_for_registering', ['app' => settings('app.name')])</p>

<p>@lang('app.confirm_email_on_link_below')</p>

<a href="{{ route('register.confirm-email', $token) }}">@lang('app.confirm_email')</a> <br/><br/>

<p>@lang('app.if_you_cant_click')</p>

<p>{{ route('register.confirm-email', $token) }}</p>

@lang('app.many_thanks'), <br/>
{{ settings('app_name') }}