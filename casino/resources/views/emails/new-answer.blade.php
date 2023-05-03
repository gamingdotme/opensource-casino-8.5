                            @foreach($ticket->answers AS $answer)
                                @if($loop->last)
                                    @if($answer->user->hasRole('user|client'))
                                                {{ $answer->user->username }} - {{ $answer->created_at->format(config('app.date_time_format')) }}
                                                <br />
                                                {!!  $answer->message  !!}
												<br />
												https://store.goldsvet.org/backend/support/{{ $ticket->id }}
												<br /><br /><br /><br />
												<b>This email was sent from a notification-only address that cannot accept incoming email. Please do not reply to this message.</b>
                                    @else
                                                AGENT #{{  $ticket->temp_id }} - {{ $answer->created_at->format(config('app.date_time_format')) }}
                                                <br />
                                                {!!  $answer->message  !!}
												<br />
												https://store.goldsvet.org/support/{{ $ticket->id }}
												<br /><br /><br /><br />
												<b>This email was sent from a notification-only address that cannot accept incoming email. Please do not reply to this message.</b>
                                    @endif
                                @endif
                            @endforeach