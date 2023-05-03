@if($ticket->user)
<tr>
    <td>{{ $ticket->id }}</td>
    <td>
        <a href="{{ route('backend.support.view', $ticket->id) }}">
            {{ $ticket->theme }}
        </a>
    </td>
    <td>
        <a href="{{ route('backend.user.edit', $ticket->user_id) }}">
            {{ $ticket->user->username ?: trans('app.n_a') }}
        </a>
    </td>
    <td>
        @if($ticket->status == 'awaiting')
            <span class="label label-warning">Awaiting</span>
        @elseif($ticket->status == 'answered')
            <span class="label label-success">Answered</span>
        @else
            <span class="label label-danger">Closed</span>
        @endif
    </td>
    <td>
    {{ $ticket->updated_at->format(config('app.date_time_format')) }}
    </td>
    <td>
        @if($ticket->status != 'closed')
            <a href="{{ route('backend.support.close', $ticket->id) }}"
               class="btn btn-success btn-xs"
               data-method="PUT"
               data-confirm-title="@lang('app.please_confirm')"
               data-confirm-text="Are you sure?"
               data-confirm-delete="Yes, I do!">
                <b>Close</b></a>
        @endif
    </td>
</tr>
@endif