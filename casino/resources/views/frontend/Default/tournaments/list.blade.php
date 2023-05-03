@extends('frontend.Default.layouts.app')
@section('page-title', 'Tournaments')

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

		<div class="tournament-page">
			@if($tournament)
				<h1 class="tournament-page__title">
					<span class="accent">Active</span>
					tournaments
				</h1>
				<div class="tournament-page__banner tournament-banner">
					<div class="tournament-banner__content">
						<div class="tournament-banner__img">
							<img class="lazy" data-src="{{ '/storage/tournaments/' . $tournament->image }}">
						</div>
						<div class="tournament-banner__info">
							<div class="tournament-banner__info-top">
								<span class="tournament-banner__name">{{ $tournament->name }}</span>
								@if( $tournament->is_waiting() )
									<div class="tournament-banner__status _soon">waiting</div>
								@elseif( $tournament->is_completed() )
									<span class="tournament-banner__status _completed">Completed</span>
								@else
									<span class="tournament-banner__status _active">Active</span>
								@endif
							</div>
							<div class="tournament-banner__time">
								<div class="tournament-banner__time-item">
									@if( $tournament->is_waiting() )
										<span class="tournament-banner__time-top">Time to start</span>
										<span class="tournament-banner__time-val accent countdown" data-date="{{ $tournament->start }}"></span>
									@elseif( $tournament->is_completed() )
										<span class="tournament-banner__time-top">End</span>
										<span class="tournament-banner__time-val accent">00 00:00:00</span>
									@else
										<span class="tournament-banner__time-top">Time left</span>
										<span class="tournament-banner__time-val accent countdown" data-date="{{ $tournament->end }}"></span>
									@endif
								</div>
								<div class="tournament-banner__time-item">
									<span class="tournament-banner__time-top">Prize Fund:</span>
									<span class="tournament-banner__time-val">{{ number_format($tournament->sum_prizes, 2,".","") }} {{ $currency }}</span>
								</div>
							</div>
							<p class="tournament-banner__desc" >{!! mb_strimwidth(strip_tags($tournament->description), 0, 130, "...") !!} </p>
							<a href="{{  route('frontend.tournaments.view', $tournament->id) }}" class="tournament-banner__btn btn">More</a>
						</div>
					</div>
				</div>

			@endif


			@if($activeTournaments || $waitingTournaments || $completedTournaments)
				<h2 class="tournament-page__title">
					<span class="accent">other</span>
					tournaments
				</h2>
				<div class="tournament-cards">
					@foreach($activeTournaments AS $item)
						@if(!$tournament || ($tournament && $tournament->id != $item->id) )
							<div class="tournament-cards__item">
								<div class="tournament-cards__wrap">
									<div class="tournament-cards__img">
										<img class="lazy" data-src="{{ '/storage/tournaments/' . $item->image }}">
									</div>
									@if( $item->is_waiting() )
										<div class="tournament-cards__status _soon">waiting</div>
									@elseif( $item->is_completed() )
										<div class="tournament-cards__status _completed">Completed</div>
									@else
										<div class="tournament-cards__status _active">Active</div>
									@endif
									<p class="tournament-cards__title">{{ $item->name }}</p>
									<div class="tournament-cards__time">
										<div class="tournament-cards__time-item">
											@if( $item->is_waiting() )
												<span class="tournament-cards__time-text">Time to start:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="{{ $item->start }}"></span>
											@elseif( $item->is_completed() )
												<span class="tournament-cards__time-text">End:</span>
												<span class="tournament-cards__time-val accent">00 00:00:00</span>
											@else
												<span class="tournament-cards__time-text">Time left:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="{{ $item->end }}"></span>
											@endif
										</div>
										<div class="tournament-cards__time-item">
											<span class="tournament-cards__time-text">prize fund:</span>
											<span class="tournament-cards__time-val">{{ number_format($item->sum_prizes, 2,".","") }} {{ $currency }}</span>
										</div>
									</div>
									<a href="{{  route('frontend.tournaments.view', $item->id) }}" class="tournament-cards__btn">More</a>
								</div>
							</div>
						@endif
					@endforeach


					@foreach($waitingTournaments AS $item)
						@if(!$tournament || ($tournament && $tournament->id != $item->id) )
							<div class="tournament-cards__item">
								<div class="tournament-cards__wrap">
									<div class="tournament-cards__img">
										<img class="lazy" data-src="{{ '/storage/tournaments/' . $item->image }}">
									</div>
									@if( $item->is_waiting() )
										<div class="tournament-cards__status _soon">waiting</div>
									@elseif( $item->is_completed() )
										<div class="tournament-cards__status _completed">Completed</div>
									@else
										<div class="tournament-cards__status _active">Active</div>
									@endif
									<p class="tournament-cards__title">{{ $item->name }}</p>
									<div class="tournament-cards__time">
										<div class="tournament-cards__time-item">
											@if( $item->is_waiting() )
												<span class="tournament-cards__time-text">Time to start:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="{{ $item->start }}"></span>
											@elseif( $item->is_completed() )
												<span class="tournament-cards__time-text">End:</span>
												<span class="tournament-cards__time-val accent">00 00:00:00</span>
											@else
												<span class="tournament-cards__time-text">Time left:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="{{ $item->end }}"></span>
											@endif
										</div>
										<div class="tournament-cards__time-item">
											<span class="tournament-cards__time-text">prize fund:</span>
											<span class="tournament-cards__time-val">{{ number_format($item->sum_prizes, 2,".","") }} {{ $currency }}</span>
										</div>
									</div>
									<a href="{{  route('frontend.tournaments.view', $item->id) }}" class="tournament-cards__btn">More</a>
								</div>
							</div>
						@endif
					@endforeach

					@foreach($completedTournaments AS $item)
						@if(!$tournament || ($tournament && $tournament->id != $item->id) )
							<div class="tournament-cards__item">
								<div class="tournament-cards__wrap">
									<div class="tournament-cards__img">
										<img class="lazy" data-src="{{ '/storage/tournaments/' . $item->image }}">
									</div>
									@if( $item->is_waiting() )
										<div class="tournament-cards__status _soon">waiting</div>
									@elseif( $item->is_completed() )
										<div class="tournament-cards__status _completed">Completed</div>
									@else
										<div class="tournament-cards__status _active">Active</div>
									@endif
									<p class="tournament-cards__title">{{ $item->name }}</p>
									<div class="tournament-cards__time">
										<div class="tournament-cards__time-item">
											@if( $item->is_waiting() )
												<span class="tournament-cards__time-text">Time to start:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="{{ $item->start }}"></span>
											@elseif( $item->is_completed() )
												<span class="tournament-cards__time-text">End:</span>
												<span class="tournament-cards__time-val accent">00 00:00:00</span>
											@else
												<span class="tournament-cards__time-text">Time left:</span>
												<span class="tournament-cards__time-val accent countdown" data-date="{{ $item->end }}"></span>
											@endif
										</div>
										<div class="tournament-cards__time-item">
											<span class="tournament-cards__time-text">prize fund:</span>
											<span class="tournament-cards__time-val">{{ number_format($item->sum_prizes, 2,".","") }} {{ $currency }}</span>
										</div>
									</div>
									<a href="{{  route('frontend.tournaments.view', $item->id) }}" class="tournament-cards__btn">More</a>
								</div>
							</div>
						@endif
					@endforeach

				</div>
			@endif


		</div>
	</div>


@endsection

@section('footer')
	@include('frontend.Default.partials.footer')
@endsection

@section('scripts')
	@include('frontend.Default.partials.scripts')
@endsection