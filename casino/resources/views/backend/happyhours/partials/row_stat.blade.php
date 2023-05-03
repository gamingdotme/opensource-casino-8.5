<tr>
    
	<td class="align-middle">@if( $stat->user ){{ $stat->user->username }} @else {{ $stat->system }} @endif</td>
	<td class="align-middle">{{ $stat->type }}</td>
	<td class="align-middle">{{ $stat->summ }}</td>
	<td class="align-middle">{{ $stat->created_at }}</td>
    
</tr>