            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.api_key') <small><a href="javascript:;" id="generateKey">@lang('app.generate')</a></small></label>
            <input type="text" class="form-control" id="keygen" name="keygen" required value="{{ $edit ? $api->keygen : '' }}">
			  </div>
			</div>
			
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.api_ip')</label>
            <input type="text" class="form-control" id="ip" name="ip" value="{{ $edit ? $api->ip : '' }}">
			  </div>
			</div>
			
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.shops')</label>
            {!! Form::select('shop_id', $shops, $edit ? $api->shop_id : '', ['class' => 'form-control']) !!}
			  </div>
			</div>
			
            <div class="col-md-6">
              <div class="form-group">
                <label>@lang('app.status')</label>
            {!! Form::select('status', ['Disabled', 'Active'], $edit ? $api->status : '', ['class' => 'form-control']) !!}
			  </div>
			</div>