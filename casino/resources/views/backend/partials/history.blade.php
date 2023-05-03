<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">@lang('app.history')</h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <p><b>@lang('app.before')</b></p>
                @if(count($activity) >= 2)
                    {{ $activity[count($activity)-2]->description }}
                    <p><b>{{ $activity[count($activity)-2]->created_at->format(config('app.date_time_format')) }}</b></p>
                @endif
            </div>
            <div class="col-md-6">
                <p><b>@lang('app.now')</b></p>
                @if(count($activity))
                    {{ $activity[count($activity)-1]->description }}
                    <p><b>{{ $activity[count($activity)-1]->created_at->format(config('app.date_time_format')) }}</b></p>
                @endif
            </div>
        </div>
    </div>
</div>

