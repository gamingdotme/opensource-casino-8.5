@extends('backend.layouts.app')

@section('page-title', trans('app.invite'))
@section('page-heading', trans('app.invite'))

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        @if( auth()->user()->hasPermission('invite.edit') )
            {!! Form::open(['route' => 'backend.invites.update']) !!}
        @endif
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('app.invite') }}</h3>
                <div class="pull-right box-tools">
                    @permission('invite.edit')
                    @if(auth()->user()->shop )
                        @if( auth()->user()->shop->invite_active)
                            <a href="{{ route('backend.invite.status', 'disable') }}" class="btn btn-danger btn-sm">@lang('app.disable')</a>
                        @else
                            <a href="{{ route('backend.invite.status', 'activate') }}" class="btn btn-success btn-sm">@lang('app.active')</a>
                        @endif
                    @endif
                    @endpermission
                </div>
            </div>

            <div class="box-body">
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>@lang('app.message')</label>
                            <textarea class="form-control" id="message" name="message" rows="5">{{ $invite->message }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.bonus')</label>
                            <input type="number" step="0.0000001" class="form-control" id="sum" name="sum"value="{{ $invite->sum  }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.bonus_friend')</label>
                            <input type="number" step="0.0000001" class="form-control" id="sum_ref" name="sum_ref" value="{{ $invite->sum_ref  }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.sum')</label>
                            <input type="number" step="0.0000001" class="form-control" id="min_amount" name="min_amount" value="{{ $invite->min_amount  }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.waiting_time')</label>
                            <input type="number" step="0.0000001" class="form-control" id="waiting_time" name="waiting_time" value="{{ $invite->waiting_time }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@lang('app.wager')</label>
                            {!! Form::select('wager', \VanguardLTE\Invite::$values['wager'], $invite->wager, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">@lang('app.type')</label>
                            {!! Form::select('type', \VanguardLTE\Invite::$values['type'], $invite->type, ['class' => 'form-control', 'id' => 'type']) !!}
                        </div>
                    </div>


                </div>
            </div>

            @if( auth()->user()->hasPermission('invite.edit') )
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">
                    @lang('app.edit_invite')
                </button>
            </div>
            @endif
        </div>
        @if( auth()->user()->hasPermission('invite.edit') )
        {!! Form::close() !!}
        @endif
    </section>

@stop
