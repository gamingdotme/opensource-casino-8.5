<tr>
    <td>
        @if( auth()->user()->hasPermission('pincodes.edit') )
            <a href="{{ route('backend.pincode.edit', $pincode->id) }}">{{ $pincode->code?:$pincode->id }}</a>
        @else
            {{ $pincode->code?:$pincode->id }}
        @endif

    </td>
	<td>{{ $pincode->nominal }}</td>
    <td>
        @if( $pincode->status )
            <small><i class="fa fa-circle text-green"></i></small>
        @else
            <small><i class="fa fa-circle text-red"></i></small>
        @endif
    </td>
</tr>
