<tr>        
    <td>
        @permission('tournaments.edit')
            <a href="{{ route('backend.tournament.edit', $tournament->id) }}">{{ $tournament->id }}</a>
        @else
            {{ $tournament->id }}
        @endpermission
    </td>
    <td>
        @permission('tournaments.edit')
        <a href="{{ route('backend.tournament.edit', $tournament->id) }}">{{ $tournament->name }}</a>
        @else
            {{ $tournament->id }}
            @endpermission
    </td>
    <td>{{ $tournament->start }}</td>
    <td>{{ $tournament->end }}</td>
    <td>{{ \VanguardLTE\Tournament::$values['type'][$tournament->type] }}</td>
    <td>{{ number_format($tournament->sum_prizes, 2, '.', '') }}</td>
    <td>{{ number_format($tournament->bet, 2, '.', '') }}</td>
    <td>{{ $tournament->spins }}</td>
    <td>x{{ $tournament->wager }}</td>
    <td>
        @if( \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->start), false) >= 0 )
            <i class="fa fa-circle text-yellow"></i>
        @elseif(  \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($tournament->end), false) <= 0 )
            <i class="fa fa-circle text-red"></i>
        @else
            <i class="fa fa-circle text-green"></i>
        @endif
    </td>
</tr>