<p>@lang('app.request_for_password_reset_made')</p>

<p>@lang('app.click_on_link_below')</p>

<a href="{{ url('password/reset/' . $token) }}">@lang('app.reset_password')</a> <br/><br/>

<p>@lang('app.if_you_cant_click')</p>

<p>{{ url('password/reset/' . $token) }}</p>

@lang('app.many_thanks'), <br/>
{{ settings('app_name') }}