@extends('backend.layouts.app')

@section('page-title', trans('app.edit_user'))
@section('page-heading', $user->present()->username)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        {!! Form::open(['route' => ['backend.user.update.details', $user->id], 'method' => 'PUT', 'id' => 'details-form']) !!}

        <div class="row">
            @include('backend.user.partials.info')
            <div class="col-md-9">



                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li @if(!Request::get('date') && !Request::get('page')) class="active" @endif>
                            <a id="details-tab"
                               data-toggle="tab"
                               href="#details">
                                @lang('app.edit_user')
                            </a>
                        </li>
                        @permission('users.activity')
                        <li @if(Request::get('page')) class="active" @endif>
                            <a id="authentication-tab"
                               data-toggle="tab"
                               href="#login-details">
                                @lang('app.latest_activity')
                            </a>
                        </li>
                        @endpermission

                    </ul>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="@if(!Request::get('date') && !Request::get('page')) active @endif tab-pane" id="details">
                            @include('backend.user.partials.edit')

                        </div>


                        @permission('users.activity')
                        <div class="tab-pane @if(Request::get('page')) active @endif" id="login-details">
                            @if (count($userActivities))
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>@lang('app.date')</th>
                                        <th>@lang('app.more_info')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($userActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->created_at->format(config('app.date_time_format')) }}</td>
                                            <td>
                                                <b> @lang('app.country')</b>: {{ $activity->country }} <br>
                                                <b> @lang('app.city')</b>: {{ $activity->city }} <br>
                                                <b> @lang('app.os')</b>: {{ $activity->os }} <br>
                                                <b> @lang('app.device')</b>: {{ $activity->device }} <br>
                                                <b> @lang('app.browser')</b>: {{ $activity->browser }} <br>
                                                <b> @lang('app.ip')</b>: {{ $activity->ip_address }} <br>
                                                <b> @lang('app.user_agent')</b>: {{ $activity->user_agent }} <br>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                {!! $userActivities->links() !!}
                            @else
                                <p class="text-muted font-weight-light"><em>@lang('app.no_activity_from_this_user_yet')</em></p>
                            @endif
                        </div>
                        @endpermission

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

                        </div>


                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}


        @if(!$user->hasRole('admin'))
            @include('backend.user.partials.modals', ['user' => $user])
        @endif


    </section>

@stop

@section('scripts')
    <script>
        $(function() {
            $('input[name="date"]').datepicker({
                format: 'yyyy-mm-dd',
            });
        });
        $('.outPayment').click(function(event){
            $('#outAll').val('');
        });
        $('#doOutAll').click(function () {
            $('#outAll').val('1');
            $('form#outForm').submit();
        });
    </script>
    {!! HTML::script('/back/js/as/app.js') !!}
    {!! HTML::script('/back/js/as/btn.js') !!}
    {!! HTML::script('/back/js/as/profile.js') !!}
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\UpdateDetailsRequest', '#details-form') !!}
    {!! JsValidator::formRequest('VanguardLTE\Http\Requests\User\UpdateLoginDetailsRequest', '#login-details-form') !!}
@stop
