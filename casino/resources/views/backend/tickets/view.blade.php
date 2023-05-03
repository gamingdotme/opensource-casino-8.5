@extends('backend.layouts.app')

@section('page-title', $ticket->theme)
@section('page-heading', $ticket->theme)

@section('content')

    <section class="content-header">
        @include('backend.partials.messages')
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning direct-chat direct-chat-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $ticket->theme }}</h3>
                        @if( auth()->user()->hasRole('admin') && !$ticket->admin )
                            /Role={{ $ticket->user->role->name }} / Shop={{ $ticket->shop ? $ticket->shop->name : 'No Shop' }} / IP={{ $ticket->ip_address }} / Country={{ $ticket->country }} / City={{ $ticket->city }} / OS={{ $ticket->os }} / Device={{ $ticket->device }} / Browser={{ $ticket->browser }}
                        @endif
                        <p>{!! $ticket->text !!}</p>
                    </div>
                    <div class="box-body">
                        <div class="direct-chat-messages">
                            @foreach($ticket->answers AS $answer)

                                @if(!$answer->user->hasRole('admin'))
                                    <div class="direct-chat-msg">
                                        <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name pull-left">{{ $answer->user->username }}
                                                @if( auth()->user()->hasRole('admin') )
                                                /Role={{ $answer->user->role->name }} / Shop={{ $ticket->shop->name }} / IP={{ $answer->ip_address }} / Country={{ $answer->country }} / City={{ $answer->city }} / OS={{ $answer->os }} / Device={{ $answer->device }} / Browser={{ $answer->browser }}
                                                @endif
                                            </span>
                                            <span class="direct-chat-timestamp pull-right">
                                                {{ $answer->created_at->format(config('app.date_time_format')) }}
                                            </span>
                                        </div>
                                        <img class="direct-chat-img" src="/back/img/{{ $answer->user->role_id }}.png">
                                        <div class="direct-chat-text">
                                            {!!  $answer->message  !!}
                                        </div>
                                    </div>
                                @else
                                    <div class="direct-chat-msg right">
                                        <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name pull-right">AGENT #{{ $ticket->temp_id }}</span>
                                            <span class="direct-chat-timestamp pull-left">
                                                {{ $answer->created_at->format(config('app.date_time_format')) }}
                                            </span>
                                        </div>
                                        <img class="direct-chat-img" src="/back/img/{{ $answer->user->role_id }}.png">
                                        <div class="direct-chat-text">
                                            {!!  $answer->message  !!}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="box-footer">
                        @if(  $ticket->status != 'closed')
                        {!! Form::open(['route' => ['backend.support.answer', $ticket->id], 'method' => 'POST']) !!}
                        <textarea name="message" class="form-control textarea" id="editor" ></textarea>
						<br />
                        <button type="submit" class="btn btn-success">OK</button>
                        {!! Form::close() !!}
                         @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop

@section('scripts')
    <script>
        initSample();
    </script>
@stop