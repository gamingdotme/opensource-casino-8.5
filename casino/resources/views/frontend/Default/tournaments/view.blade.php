@extends('frontend.Default.layouts.app')
@section('page-title', $tournament->name)

@section('content')


	@php
        if(Auth::check()){
            $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
        } else{
            $currency = '';
        }
	@endphp

	<div class="container">
		@include('frontend.Default.partials.header')

		<!-- tournament-block-->
		<div class="tournament-block">
			<div class="tournament-block__info">
				<div class="tournament-block__item">
					<div class="tournament-block__info-top">
						<h1 class="tournament-block__info-title">
							{{ $tournament->name }}
						</h1>
						<div class="tournament-block__info-prize">
							Prize fund:
							<span class="tournament-block__info-val">{{ number_format($tournament->sum_prizes, 2,".","") }} {{ $currency }}</span>
						</div>
					</div>
					<div class="tournament-block__desc custom-scroll" data-simplebar>
						<p class="tournament-block__desc-title">
							description
						</p>
						<p class="tournament-block__desc-text">
							@php echo htmlspecialchars_decode($tournament->description); @endphp
						</p>
					</div>
				</div>
				<div class="tournament-block__item">
					<ul class="tournament-block__terms">
						<li class="tournament-block__terms-item">
							<span class="text">Status:</span>
							<span class="accent">
								@if( $tournament->is_waiting() )
									waiting
								@elseif( $tournament->is_completed() )
									Completed
								@else
									Active
								@endif
							</span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">Date of beginning:</span>
							<span class="accent">{{ $tournament->start }}</span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">Date of ending:</span>
							<span class="accent">{{ $tournament->end }}</span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">Type of tournament:</span>
							<span class="accent">{{ \VanguardLTE\Tournament::$values['type'][$tournament->type] }}</span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">minimal bet:</span>
							<span class="accent">{{ $tournament->bet }}</span>
						</li>
						<li class="tournament-block__terms-item">
							<span class="text">spins for qualification:</span>
							<span class="accent">{{ $tournament->spins }}</span>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<h2 class="leaderboard__title">Leaders</h2>
		<div class="leaderboard tournament{{ $tournament->id }}">
			<div class="leaderboard__block">
				<div class="leaderboard__item">
					<div class="leaderboard__table">
						<div class="leaderboard__table-head">
							<span class="leaderboard__table-head-item">№</span>
							<span class="leaderboard__table-head-item">Login</span>
							<span class="leaderboard__table-head-item">points</span>
							<span class="leaderboard__table-head-item">prize</span>
						</div>
						<div class="leaderboard__table-body table1">
							@if( count($tournament->stats) )
								@php $index=1; @endphp
								@foreach($tournament->get_stats(0, 5, true) AS $stat)
									<div class="leaderboard__table-row">
										<div class="leaderboard__table-body-item">{{ $index }}</div>
										<div class="leaderboard__table-body-item">{{ $stat['username'] }}</div>
										<div class="leaderboard__table-body-item">{{ $stat['points'] }}</div>
										<div class="leaderboard__table-body-item">{{ $stat['prize'] }}</div>
									</div>
									@php $index++; @endphp
								@endforeach
							@else
								<div class="tournament__table-row">
									<span class="tournament__table-item">@lang('app.no_data')</span>
								</div>
							@endif
						</div>
					</div>
				</div>
				<div class="leaderboard__item">
					<div class="leaderboard__table">
						<div class="leaderboard__table-head">
							<span class="leaderboard__table-head-item">№</span>
							<span class="leaderboard__table-head-item">Login</span>
							<span class="leaderboard__table-head-item">points</span>
							<span class="leaderboard__table-head-item">prize</span>
						</div>
						<div class="leaderboard__table-body table2">
							@if( count($tournament->stats) > 5 )
								@php $index=6; @endphp
								@foreach($tournament->get_stats(5, 5, true) AS $stat)
									<div class="leaderboard__table-row">
										<div class="leaderboard__table-body-item">{{ $index }}</div>
										<div class="leaderboard__table-body-item">{{ $stat['username'] }}</div>
										<div class="leaderboard__table-body-item">{{ $stat['points'] }}</div>
										<div class="leaderboard__table-body-item">{{ $stat['prize'] }}</div>
									</div>
									@php $index++; @endphp
								@endforeach
							@else
								<div class="tournament__table-row">
									<span class="tournament__table-item">@lang('app.no_data')</span>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
			<p class="leaderboard__place accent">
				YOUR PLACE IN THE RATINGS: <span class="tournament{{ $tournament->id }}_place">{{ $tournament->my_place() ?: '---' }}</span>
			</p>
		</div>
		<div class="tournament-title">
			<p class="tournament-title__main">
				<span class="accent">GAMES TAKING</span>
				PART IN THE TOURNAMENT
			</p>
		</div>
		<div class="grid">
			@if( $tournament->games )
				@foreach ($tournament->games as $key=>$game)
					@if($game = $game->game)
						@include('frontend.Default.partials.game')
					@endif
				@endforeach
			@endif
		</div>
		<!--
		<div class="show-more">
			<a href="#" class="btn btn-more">All slot</a>
		</div>
		-->
	</div>


@endsection

@section('footer')
	@include('frontend.Default.partials.footer')
@endsection

@section('scripts')
	@include('frontend.Default.partials.scripts')
@endsection