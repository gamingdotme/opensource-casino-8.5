<div class="row">
    <div class="col-md-6">

        <div class="form-group">
            <label>@lang('app.title')</label>
            <input type="text" class="form-control" name="title" required value="{{ $edit ? $info->title : '' }}">
        </div>
        <div class="form-group">
            <label>@lang('app.text')</label>
            <textarea name="text" class="form-control" id="editor" name="text"  >{{ $edit ? $info->text : '' }}</textarea>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>@lang('app.roles')</label>
            @php
                $allRoles = \VanguardLTE\Role::where('slug', '!=', 'user')->where('id', '<', auth()->user()->level())->pluck('name');
            @endphp
            <select name="roles[]" id="roles" class="form-control select2" multiple="multiple" style="width: 100%">
                @foreach ($allRoles as $role)
                    <option value="{{ $role }}" {{ (in_array($role, $roles))? 'selected="selected"' : '' }}>{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>@lang('app.days')</label>
            @php
                $days = array_combine(\VanguardLTE\Info::$values['days'], \VanguardLTE\Info::$values['days']);
            @endphp
            {!! Form::select('days', $days, $edit ? $info->days : old('days'), ['class' => 'form-control']) !!}
        </div>
    </div>
</div>
