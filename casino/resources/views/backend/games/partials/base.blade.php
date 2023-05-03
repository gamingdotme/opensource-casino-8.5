<div class="row">
    @if (!$edit || $game->name !== '')
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">@lang('app.name')</label>
                <input type="text" class="form-control" id="name"
                       name="name" placeholder="@lang('app.name')" {{ $edit ? 'disabled' : '' }} value="{{ $edit ? $game->name : '' }}" required>
            </div>
        </div>
    @endif
    @if (!$edit || $game->title !== '')
        <div class="col-md-6">
            <div class="form-group">
                <label for="title">@lang('app.title')</label>
                <input type="text" class="form-control" id="title"
                       name="title" placeholder="@lang('app.title')" value="{{ $edit ? $game->title : '' }}" required>
            </div>
        </div>
    @endif



    <div class="col-md-12">
        <div class="form-group">
            <label for="category">@lang('app.categories')</label>
            <select name="category[]" id="category" class="form-control select2" multiple="multiple" style="width: 100%;" required>
                @foreach ($categories as $key=>$category)
                    <option value="{{ $category->id }}" {{ ($edit && in_array($category->id, $cats))? 'selected="selected"' : '' }}>{{ $category->title }}</option>
                    @foreach ($category->inner as $inner)
                        <option value="{{ $inner->id }}" {{ ($edit && in_array($inner->id, $cats))? 'selected="selected"' : '' }}>{{ $inner->title }}</option>
                    @endforeach
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="device">@lang('app.device')</label>
            <select name="device" id="device" class="form-control" required>
                <option value="0" {{ ($edit && !$game->device==0)? 'selected="selected"' : '' }}>@lang('app.mobile')</option>
                <option value="1" {{ ($edit && $game->device==1)? 'selected="selected"' : '' }}>@lang('app.desktop')</option>
                <option value="2" {{ ($edit && $game->device==2)? 'selected="selected"' : '' }}>@lang('app.mobile') + @lang('app.desktop')</option>
            </select>
        </div>
    </div>

    @if (!$edit || $game->bet !== '')
        <div class="col-md-6">
            <div class="form-group">
                <label for="bet">@lang('app.bet')</label>
                {!! Form::select('bet', $game->get_values('bet'), $edit ? $game->bet : '', ['class' => 'form-control', 'required' => true]) !!}
            </div>
        </div>
    @endif

    @if (!$edit || $game->denomination !== '')
        <div class="col-md-6">
            <div class="form-group">
                <label>@lang('app.denomination')</label>
                @php
                    $denominations = array_combine(\VanguardLTE\Game::$values['denomination'], \VanguardLTE\Game::$values['denomination']);
                @endphp
                {!! Form::select('denomination', $denominations, $edit ? $game->denomination : '1.00', ['class' => 'form-control']) !!}
            </div>
        </div>
    @endif

    @if (!$edit || $game->scaleMode !== '')
        <div class="col-md-6">
            <div class="form-group">
                <label for="scaleMode">@lang('app.scaling')</label>
                <select name="scaleMode" id="scaleMode" class="form-control" required>
                    <option value="showAll" {{ $edit && $game->scaleMode=='showAll'? 'selected="selected"' : '' }}>@lang('app.default')</option>
                    <option value="exactFit" {{ $edit && $game->scaleMode=='exactFit'? 'selected="selected"' : '' }}>@lang('app.full_screen')</option>
                </select>
            </div>
        </div>
    @endif

    @if (!$edit || $game->slotViewState !== '')
        <div class="col-md-6">
            <div class="form-group">
                <label for="slotViewState">@lang('app.ui')</label>
                <select name="slotViewState" id="slotViewState" class="form-control" required>
                    <option value="Normal" {{ $edit && $game->slotViewState=='Normal'? 'selected="selected"' : '' }}>@lang('app.visible_ui')</option>
                    <option value="HideUI" {{ $edit && $game->slotViewState=='HideUI'? 'selected="selected"' : '' }}>@lang('app.hide_ui')</option>
                </select>
            </div>
        </div>
    @endif

    <div class="col-md-6">
        <div class="form-group">
            <label for="view">@lang('app.view')</label>
            <select name="view" id="view" class="form-control">
                <option value="1" {{ $edit && $game->view? 'selected="selected"' : '' }}>@lang('app.active')</option>
                <option value="0" {{ $edit && !$game->view? 'selected="selected"' : '' }}>@lang('app.disabled')</option>
            </select>
        </div>
    </div>

    @if ($edit)
        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary" id="update-details-btn">
                @lang('app.edit_game')
            </button>
        </div>
    @endif
</div>