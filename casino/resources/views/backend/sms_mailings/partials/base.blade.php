<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('app.theme')</label>
            <input type="text" class="form-control" id="theme" name="theme" required value="{{ $edit ? $mailing->theme : old('theme') }}" >
        </div>
        <div class="form-group">
            <label>@lang('app.text')</label>
            <textarea class="form-control" name="message" rows="5">{{ $edit ? $mailing->message : old('message') }}</textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label>@lang('app.date_start')</label>
            <div class="input-group date">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input type="text" name="date_start" id="date_start" class="form-control pull-right datepicker" required value="{{ $edit ? $mailing->date_start : old('date_start') }}">
            </div>
        </div>

        <div class="form-group">
            <label>@lang('app.roles')</label>
            @php
                $allRoles = \VanguardLTE\Role::where('slug', '!=', 'admin')->get()->pluck('name');
            @endphp
            <select name="roles[]" id="roles" class="form-control select2" multiple="multiple" style="width: 100%">
                @foreach ($allRoles as $role)
                    <option value="{{ $role }}" {{ (in_array($role, $roles))? 'selected="selected"' : '' }}>{{ $role }}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="col-md-6">

    </div>
</div>
