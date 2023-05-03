<div class="game-item ng-scope">
                                <div class="game-item game-item--overflow ng-scope">
                                    <div class="game-item__block">
                                        <img class="game-item__img" src="{{ $game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" casino-lazy-src="{{ $game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : '' }}" alt="{{ $game->title }}" loading="true" style="opacity: 1;"> 
                                       
                                        <!-- ngIf: game | gameJackpotByCurrency : $root.data.user.currency : 'EUR' -->
                                    </div>
                                    <div class="game-item__labels">
                                        @if($game->label)
                                        <div class="game-item__label game-item__label--hot ng-binding ng-scope">{{ mb_strtoupper($game->label) }}</div>
                                        @endif
                                        <div class="game-item__label game-item__label--bitcoin ng-scope"></div>
                                    </div>
                                    <div class="game-item__label-live ng-scope"> <span class="game-item__label-live-txt">Active</span> </div>
                                    <div class="game-item__overlay ng-scope">
                                        <div class="game-item__actions">
                                            @if( isset(auth()->user()->username) )
                                                <a href="{{ route('frontend.game.go', $game->name) }}?api_exit=/" class="button button-primary ng-scope ng-binding">@lang('app.play_now')</a>
                                            @else
                                                <a href="{{ route('frontend.game.go', $game->name) }}/prego?api_exit=/" class="button button-primary ng-scope ng-binding">Demo</a>
											<br>
                                                <a href="javascript:;" class="button button-primary ng-scope ng-binding" ng-click="openModal($event, '#login-modal')">@lang('app.login')</a>
                                            @endif
                                            <!-- <button class="button button-primary ng-scope ng-binding">@lang('play_now')</button> -->
                                        </div>
                                    </div>
                                    <div class="game-item__panel">
                                        <p class="game-item__panel-title ng-binding">{{ $game->title }}</p>
                                        <!-- ngIf: $root.data.user.email && $root.data.device === 'mobile' -->
                                    </div>
                                </div>
                            </div>