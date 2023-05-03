<td rowspan="{{ $distributor['rowspan'] }}">
    <a href="{{ $distributor['href'] }}">
        {{ $distributor['text'] }}
    </a>
    @if( isset($distributor['balance']) )
        <p>@lang('app.balance'): {{ number_format($distributor['balance'], 2, '.', '') }}</p>
    @endif
</td>
@if( $distributor['shops'] && count($distributor['shops']) )
        @foreach($distributor['shops'] AS $shop)
                <td rowspan="{{ $shop['rowspan'] }}">
                    <a href="{{ $shop['href'] }}">{{ $shop['text'] }}</a>
                    @if( isset($shop['balance']) )
                        <p>@lang('app.balance'): {{ number_format($shop['balance'], 2, '.', '') }}</p>
                    @endif
                </td>

                    @if(count($shop['managers']))
                        @foreach($shop['managers'] AS $manager)
                            <td rowspan="{{ $manager['rowspan'] }}">
                                <a href="{{ $manager['href'] }}">
                                    {{ $manager['text'] }}
                                </a>
                            </td>

                            @if( count($manager['cashiers']) )
                                @foreach($manager['cashiers'] AS $cashier)
                                    <td>
                                        <a href="{{ $cashier['href'] }}">
                                            {{ $cashier['text'] }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{ $cashier['href2'] }}">
                                            >> @lang('app.users')
                                        </a>
                                        @if( isset($cashier['balance']) )
                                            <p>@lang('app.balance'): {{ number_format($cashier['balance'], 2, '.', '') }}</p>
                                        @endif

                                    </td></tr><tr>

                                @endforeach
                            @else
                                <td colspan="2"></td></tr><tr>
                            @endif
                        @endforeach
                    @else
                        <td colspan="3"></td></tr><tr>
                    @endif

        @endforeach
@else
    <td colspan="5"></td></tr><tr>
@endif
