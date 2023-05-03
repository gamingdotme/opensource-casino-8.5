@extends('backend.layouts.user')

@section('page-title', trans('app.edit_user'))
@section('page-heading', $user->present()->username)

@section('content')
<div class="row wow fadeIn">

<div class="col-lg-12">
    <section class="content-header">
        @include('backend.partials.messages')
    </section>
    <div class="element-wrapper">
        <div class="element-box">
            <div class="element-info">
                <div class="element-info-with-icon">
                    <div class="element-info-icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <div class="element-info-text">
                        <h5 class="element-inner-header">Profile</h5>
                    </div>
                </div>
            </div>
        <div class="row">
            @include('backend.user.partials.info-cashier')
            <div class="col-md-9">




                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="nav-item" @if(!Request::get('date')) class="active" @endif>
                            <a id="details-tab"
                            class="nav-link active"
                               data-toggle="tab"
                               href="#details">
                                @lang('app.edit_user')
                            </a>
                        </li>
                        <li class="nav-item" >
                            <a id="authentication-tab"
                            class="nav-link"
                               data-toggle="tab"
                               href="#login-details">
                                @lang('app.latest_activity')
                            </a>
                        </li>
                        <li class="nav-item" @if(Request::get('date')) class="active" @endif>
                            <a id="bonus-tab"
                            class="nav-link"
                               data-toggle="tab"
                               href="#bonus-details">
                                @lang('app.games_activity')
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content border-right border-left border-bottom p-3" id="nav-tabContent">
                        <div class="@if(!Request::get('date')) active @endif tab-pane" id="details">
                            {!! Form::open(['route' => ['backend.user.update.details', $user->id], 'method' => 'PUT', 'id' => 'details-form']) !!}
                            @include('backend.user.partials.edit')
                            {!! Form::close() !!}
                        </div>


                        <div class="tab-pane" id="login-details">
                            @if (count($userActivities))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.action')</th>
                                        <th>@lang('app.date')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($userActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->description }}</td>
                                            <td>{{ $activity->created_at->format(config('app.date_time_format')) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>

                        <div class="tab-pane @if(Request::get('date')) active @endif" id="bonus-details">

                            <form action="" method="GET">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>@lang('app.date')</label>
                                            <input type="text" class="form-control" name="date" value="{{ Request::get('date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <button type="submit" class="btn btn-primary">
                                                @lang('app.filter')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- numbers code was here -->
                        </div>

                    </div>

                </div>




            </div>
        </div>

</div>
</div>
</div>
</div>

@stop

@section('scripts')
    <script>
        $(function() {
            $('input[name="date"]').datepicker({
                format: 'yyyy-mm-dd',
            });
        });
    </script>
    {!! HTML::script('/back/js/as/app.js') !!}
    {!! HTML::script('/back/js/as/btn.js') !!}
    {!! HTML::script('/back/js/as/profile.js') !!}
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\UpdateDetailsRequest', '#details-form') !!}
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\UpdateLoginDetailsRequest', '#login-details-form') !!}
@stop
