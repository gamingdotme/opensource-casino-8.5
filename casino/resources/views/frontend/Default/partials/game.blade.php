<div class="grid-item grid-item--height2 grid-item--width2">
    <div class="grid__content games">
        <div class="games__item">
            <div class="games__content">
                <img src="{{ $game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" alt="{{ $game->title }}"> 
                <img src="/frontend/Default/img/_src/WelcomeBonus3.png" alt="{{ $game->title }}">
                @if($game->jackpot)
                    <span class="label label-d label--left">
											{{ number_format($game->jackpot->balance, 2,".","") }} {{ $currency }}
										</span>
                @endif
                @if($game->label)
                    <span class="label @if($game->label == 'New')label-b @elseif($game->label == 'Hot')label-g @else label-d @endif">{{ mb_strtoupper($game->label) }}</span>
                @endif
                @if(Auth::check())
                <a href="{{ route('frontend.game.go', $game->name) }}?api_exit=/" class="play-btn btn">Play</a>
                @else
                <a href="{{ route('frontend.game.go', $game->name) }}/prego?api_exit=/" class="play-btn btn">Demo</a>
                @endif
                <span class="game-name">{{ $game->title }}</span>
            </div>
        </div>
    </div>
</div>