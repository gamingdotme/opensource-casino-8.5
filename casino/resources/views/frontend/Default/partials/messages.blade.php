@if(isset ($messages) && count($messages) > 0)
    <div class="input__group">
        <span class="error-message" style="display: block; position: inherit;">{!!  $messages[array_rand($messages)];  !!}</span>
    </div>
@endif
@if(isset ($errors) && count($errors) > 0)
    @foreach($errors->all() as $error)
        <div class="input__group">
            <span class="error-message" style="display: block; position: inherit;">{!!  $error  !!}</span>
        </div>
    @endforeach
@endif
@if(settings('siteisclosed'))
    <div class="input__group">
        <span class="error-message" style="display: block; position: inherit;">@lang('app.site_is_turned_off')</span>
    </div>
@endif
