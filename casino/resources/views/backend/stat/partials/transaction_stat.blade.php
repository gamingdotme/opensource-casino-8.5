


@if( $transaction->system == 'shop')
    <tr>
    @if(auth()->user()->hasRole(['admin']))
        <td></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent']))
        <td></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        @if( $transaction->user && $transaction->user->hasRole(['distributor']))
            <td>{{ $transaction->user->username }} </td>
        @else
            <td></td>
        @endif
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        <td>@if( $transaction->shop ) {{ $transaction->shop->name }} @endif</td>
    @endif
        <td></td>
    <td></td>

        <td></td>
    @if(auth()->user()->hasRole(['admin', 'agent']))
        <td colspan="2"></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        @if( $transaction->type == 'add' )
            <td></td>
            <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
        @else
            <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
            <td></td>
        @endif
    @endif
    @if(auth()->user()->hasRole(['admin']))
        <td colspan="2"></td>
    @endif
    @if( $transaction->type == 'add' )
        <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
        <td></td>
    @else
        <td></td>
        <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
    @endif
        <td colspan="2"></td>
        <td>{{  $transaction->created_at->format(config('app.date_time_format')) }}</td>
    </tr>

@elseif( $transaction->system == 'jpg' || $transaction->system == 'bank')
    <tr>
    @if( $transaction->user && $transaction->user->hasRole(['admin']))
        <td>{{ $transaction->user->username }}</td>
    @else
        <td></td>
    @endif
        <td colspan="4"></td>
        <td>{{ $transaction->title }}</td>
        <td colspan="5"></td>
    @if( $transaction->type == 'add' )
        <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
        <td></td>
    @else
        <td></td>
        <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
    @endif
        <td colspan="4"></td>
        <td>{{  $transaction->created_at->format(config('app.date_time_format')) }}</td>
    </tr>

@elseif( in_array($transaction->system, ['progress','tournament','refund','happyhour','invite','daily_entry','welcome_bonus','sms_bonus','wheelfortune']) )
    <tr>
    @if(auth()->user()->hasRole(['admin']))
        <td></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent']))
        <td></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        <td></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        <td></td>
    @endif
    @if( $transaction->payeer && $transaction->payeer->hasRole(['cashier']))
        <td>{{ $transaction->payeer->username }} </td>
    @else
        <td></td>
    @endif
    <td>{{ $transaction->title }}</td>

    @if( $transaction->user && $transaction->user->hasRole(['user']))
        <td>{{ $transaction->user->username }} </td>
    @else
        <td></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent']))
        <td colspan="2"></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        <td colspan="2"></td>
    @endif
    @if(auth()->user()->hasRole(['admin']))
        <td colspan="2"></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
            <td></td>
        @if( $transaction->system == 'happyhour' && $transaction->sum2 )
            <td><span class="text-red">{{ abs($transaction->sum2) }}</span></td>
        @else
            <td></td>
        @endif
    @endif
    @if( $transaction->type == 'add' )
        <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
        <td></td>
    @else
        <td></td>
        <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
    @endif
    <td>{{  $transaction->created_at->format(config('app.date_time_format')) }}</td>
</tr>

@elseif( in_array($transaction->system, ['pincode','user','handpay','interkassa','coinbase','btcpayserver'])  )
 <tr>
    @if(auth()->user()->hasRole(['admin']))
        @if( $transaction->payeer && $transaction->payeer->hasRole(['admin']))
            <td>{{ $transaction->payeer->username }} </td>
        @elseif( $transaction->user && $transaction->user->hasRole(['admin']))
            <td>{{ $transaction->user->username }} </td>
        @else
            <td></td>
        @endif
    @endif

    @if(auth()->user()->hasRole(['admin', 'agent']))
        @if( $transaction->payeer && $transaction->payeer->hasRole(['agent']))
            <td>{{ $transaction->payeer->username }} </td>
        @elseif( $transaction->user && $transaction->user->hasRole(['agent']))
             <td>{{ $transaction->user->username }} </td>
        @else
            <td></td>
        @endif
    @endif

    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        @if( $transaction->user && $transaction->user->hasRole(['distributor']))
            <td>{{ $transaction->user->username }} </td>
        @else
            <td></td>
        @endif
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        <td></td>
    @endif
    @if( $transaction->payeer && $transaction->payeer->hasRole(['cashier']) )
         <td>{{ $transaction->payeer->username }} </td>
    @else
         <td></td>
    @endif
    <td>{{ $transaction->title }}</td>

    @if( $transaction->user && $transaction->user->hasRole(['user']))
         <td>{{ $transaction->user->username }} </td>
    @else
         <td></td>
    @endif

    @if(auth()->user()->hasRole(['admin', 'agent']))
        @if( $transaction->payeer && $transaction->payeer->hasRole(['agent']))
                @if( $transaction->type == 'add' )
                    <td></td>
                    <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
                @else
                    <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
                    <td></td>
                @endif
        @elseif( $transaction->user && $transaction->user->hasRole(['agent']))
            @if( $transaction->type == 'add' )
                <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
                <td></td>
            @else
                <td></td>
                <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
            @endif
        @else
            <td colspan="2"></td>
        @endif
    @endif

    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        @if( $transaction->user && $transaction->user->hasRole(['distributor']))
            @if( $transaction->type == 'add' )
                <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
                <td></td>
            @else
                <td></td>
                <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
            @endif
        @else
            <td colspan="2"></td>
        @endif
    @endif
    @if(auth()->user()->hasRole(['admin']))
         <td colspan="2"></td>
    @endif
    @if(auth()->user()->hasRole(['admin', 'agent', 'distributor']))
        @if( !($transaction->user && $transaction->user->hasRole(['user'])))
            <td colspan="2"></td>
        @elseif( $transaction->type == 'add' )
            <td></td>
            <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
        @else
            <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
            <td></td>
        @endif
    @endif
    @if( $transaction->user && $transaction->user->hasRole(['user']))
        @if( $transaction->type == 'add' )
             <td><span class="text-green">{{ abs($transaction->sum) }}</span></td>
             <td></td>
        @else
             <td></td>
             <td><span class="text-red">{{ abs($transaction->sum) }}</span></td>
        @endif
    @else
        <td colspan="2"></td>
    @endif
    <td>{{  $transaction->created_at->format(config('app.date_time_format')) }}</td>
</tr>
@endif

