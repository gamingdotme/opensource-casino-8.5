<div class="game-item ng-scope">
                                <div class="game-item game-item--overflow ng-scope">
                                    <div class="game-item__block">
                                        <img class="game-item__img" src="<?php echo e($game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : ''); ?>" casino-lazy-src="<?php echo e($game->name ? '/frontend/Default/ico/' . $game->name . '.jpg' : ''); ?>" alt="<?php echo e($game->title); ?>" loading="true" style="opacity: 1;"> 
                                       
                                        <!-- ngIf: game | gameJackpotByCurrency : $root.data.user.currency : 'EUR' -->
                                    </div>
                                    <div class="game-item__labels">
                                        <?php if($game->label): ?>
                                        <div class="game-item__label game-item__label--hot ng-binding ng-scope"><?php echo e(mb_strtoupper($game->label)); ?></div>
                                        <?php endif; ?>
                                        <div class="game-item__label game-item__label--bitcoin ng-scope"></div>
                                    </div>
                                    <div class="game-item__label-live ng-scope"> <span class="game-item__label-live-txt">Active</span> </div>
                                    <div class="game-item__overlay ng-scope">
                                        <div class="game-item__actions">
                                            <?php if( isset(auth()->user()->username) ): ?>
                                                <a href="<?php echo e(route('frontend.game.go', $game->name)); ?>?api_exit=/" class="button button-primary ng-scope ng-binding"><?php echo app('translator')->get('app.play_now'); ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('frontend.game.go', $game->name)); ?>/prego?api_exit=/" class="button button-primary ng-scope ng-binding">Demo</a>
											<br>
                                                <a href="javascript:;" class="button button-primary ng-scope ng-binding" ng-click="openModal($event, '#login-modal')"><?php echo app('translator')->get('app.login'); ?></a>
                                            <?php endif; ?>
                                            <!-- <button class="button button-primary ng-scope ng-binding"><?php echo app('translator')->get('play_now'); ?></button> -->
                                        </div>
                                    </div>
                                    <div class="game-item__panel">
                                        <p class="game-item__panel-title ng-binding"><?php echo e($game->title); ?></p>
                                        <!-- ngIf: $root.data.user.email && $root.data.device === 'mobile' -->
                                    </div>
                                </div>
                            </div><?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/partials/game_search.blade.php ENDPATH**/ ?>