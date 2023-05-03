@extends('layouts.errors')

@section('title', __('app.license_error'))

@section('content')

	<section class="content-header">
		@include('backend.partials.messages')
	</section>

    <div class="title">@lang('app.license_error')</div>
    <div class="reason">
		
				<form role="form" action="<?= route('frontend.new_license.post') ?>" method="POST" >
					<input type="hidden" value="<?= csrf_token() ?>" name="_token">
				
					<p>@lang('app.licensed_email_address')</p>
					<br />
						<input type="text" size="50" name="email" value="">
                    <br /><br />
						<button type="submit">OK</button>                				
                </form>
    </div>
@stop