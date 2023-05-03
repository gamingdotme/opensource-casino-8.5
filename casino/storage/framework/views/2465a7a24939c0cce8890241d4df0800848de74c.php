<?php


    if(Auth::check()){

        $refund = false;
        if( auth()->user()->shop && auth()->user()->shop->progress_active ){
            $refund = \VanguardLTE\Progress::where(['shop_id' => auth()->user()->shop_id, 'rating' => auth()->user()->rating])->first();
        }

            $currency = auth()->user()->present()->shop ? auth()->user()->present()->shop->currency : '';
    } else{
            $currency = '';
            $refund = false;
    }
    $rules = \VanguardLTE\Rule::get();
?>


<div class="container">
    <!-- MENU BEGIN -->
    <div class="header <?php echo $__env->yieldContent('add-header-class'); ?>">
        <!--  mobile wallet change  and icon and balance 
        <div class="mobile-search">
            <button class="search-btn"></button>
            <input type="text" placeholder="Search" class="search">
            <span class="clear-btn"></span>
        </div>
        <div class="mobile-balance">
					 
				<span class="info-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 19.67"><path d="M13.45.08a2,2,0,0,1,2.47,1.37A2,2,0,0,1,16,2V3.67h2a2,2,0,0,1,2,2v12a2,2,0,0,1-2,2H2a2,2,0,0,1-2-2H0a1.66,1.66,0,0,1,0-.32V5.43A2,2,0,0,1,1.45,3.51ZM8.14,17.67H18v-8H16v4.25a2,2,0,0,1-1.45,1.92ZM18,5.67v2H16v-2ZM2,5.43V17.35l12-3.43V2ZM12,7.67a1,1,0,1,1-1-1A1,1,0,0,1,12,7.67Z"/></svg>
					</span>  
            <span class="info-value"><?php echo e(number_format(auth()->user()->balance, 2,".","")); ?> <?php echo e($currency); ?></span></li>
        </div>
        <div class="mobMenu">
            <img src="/frontend/Default/img/badges64x64/badge-<?php echo e(auth()->user()->badge()); ?>.png" alt="" width="32" height="32">
        </div>!-->
        <div class="mobile-menu">
            <div class="mobile-menu__wrap">
                <div class="mobile-menu__item">
                    <div class="mobile-menu__acc">
                        <div class="mobile-menu__item-acc-img">
                            <?php if( auth()->user()->shop && auth()->user()->shop->progress_active ): ?>
                                <a href="<?php echo e(route('frontend.progress')); ?>">
                                    <img src="/frontend/Default/img/badges64x64/badge-<?php echo e(auth()->user()->badge()); ?>.png" alt="" width="72" height="72">
                                </a>
                            <?php else: ?>
                                <img src="/frontend/Default/img/badges64x64/badge-<?php echo e(auth()->user()->badge()); ?>.png" alt="" width="72" height="72">
                            <?php endif; ?>
                            <div class="mobile-menu__item-acc-rating"><?php echo e(auth()->user()->username); ?></div>
                        </div>
                        <ul class="mobile-menu__item-acc-info">

                            <li class="tooltip-btn bonusMenu">
                                <?php if(
                                    auth() ->user()->tournaments > 0 || auth() ->user()->happyhours > 0 || auth() ->user()->refunds > 0 ||
                                    auth() ->user()->progress > 0 || auth() ->user()->daily_entries > 0 || auth() ->user()->invite > 0 ||
                                    auth() ->user()->welcomebonus > 0 || auth() ->user()->smsbonus > 0 || auth() ->user()->wheelfortune > 0
                                ): ?>
                                    <span class="tooltip-item">
                                        <?php if(auth() ->user()->tournaments > 0): ?><p>Tournaments = <?php echo e(number_format(auth() ->user()->tournaments, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->happyhours > 0): ?><p>Happy Hours = <?php echo e(number_format(auth() ->user()->happyhours, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->refunds > 0): ?><p>Refund = <?php echo e(number_format(auth() ->user()->refunds, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->progress > 0): ?><p>Progress Bonus = <?php echo e(number_format(auth() ->user()->progress, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->daily_entries > 0): ?><p>Daily Entries = <?php echo e(number_format(auth() ->user()->daily_entries, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->invite > 0): ?><p>Invite Bonus = <?php echo e(number_format(auth() ->user()->invite, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->welcomebonus > 0): ?><p>Welcome Bonus = <?php echo e(number_format(auth() ->user()->welcomebonus, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->smsbonus > 0): ?><p>SMS Bonus = <?php echo e(number_format(auth() ->user()->smsbonus, 2,".","")); ?></p><?php endif; ?>
                                         <?php if(auth() ->user()->wheelfortune > 0): ?><p>Wheel Fortune = <?php echo e(number_format(auth() ->user()->wheelfortune, 2,".","")); ?></p><?php endif; ?>
                                    </span>
                                    <span class="info-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M3.5,5A3.75,3.75,0,0,1,3,3,3,3,0,0,1,6,0a4.36,4.36,0,0,1,4,3.11A4.36,4.36,0,0,1,14,0a3,3,0,0,1,3,3,3.75,3.75,0,0,1-.5,2H18a2,2,0,0,1,2,2V9a2,2,0,0,1-2,2v7a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V11A2,2,0,0,1,0,9V7A2,2,0,0,1,2,5ZM9,7H2V9H9Zm9,2H11V7h7ZM9,18V11H4v7Zm7,0H11V11h5ZM6,2A1,1,0,0,0,5,3C5,4.25,6,4.85,8.43,5,8.16,3.11,7.16,2,6,2Zm5.5,3c.27-1.86,1.27-3,2.43-3a1,1,0,0,1,1,1C14.93,4.25,13.91,4.85,11.5,5Z"/></svg></span>
                                <?php else: ?>
                                    <span class="info-icon _disabled"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M3.5,5A3.75,3.75,0,0,1,3,3,3,3,0,0,1,6,0a4.36,4.36,0,0,1,4,3.11A4.36,4.36,0,0,1,14,0a3,3,0,0,1,3,3,3.75,3.75,0,0,1-.5,2H18a2,2,0,0,1,2,2V9a2,2,0,0,1-2,2v7a2,2,0,0,1-2,2H4a2,2,0,0,1-2-2V11A2,2,0,0,1,0,9V7A2,2,0,0,1,2,5ZM9,7H2V9H9Zm9,2H11V7h7ZM9,18V11H4v7Zm7,0H11V11h5ZM6,2A1,1,0,0,0,5,3C5,4.25,6,4.85,8.43,5,8.16,3.11,7.16,2,6,2Zm5.5,3c.27-1.86,1.27-3,2.43-3a1,1,0,0,1,1,1C14.93,4.25,13.91,4.85,11.5,5Z"/></svg></span>
                                <?php endif; ?>
                                <span class="info-value"><?php echo e(number_format( (auth() ->user()->tournaments + auth() ->user()->happyhours + auth()->user()->refunds + auth() ->user()->progress + auth() ->user()->daily_entries + auth() ->user()->invite + auth() ->user()->welcomebonus + auth() ->user()->smsbonus + auth() ->user()->wheelfortune), 2,".","")); ?> <?php echo e($currency); ?></span>
                            </li>


                            <li class="tooltip-btn wagerMenu">
                                <?php if(
                                    auth() ->user()->count_tournaments > 0 || auth() ->user()->count_happyhours > 0 || auth() ->user()->count_refunds > 0 ||
                                    auth() ->user()->count_progress > 0 || auth() ->user()->count_daily_entries > 0 || auth() ->user()->count_invite > 0 ||
                                    auth() ->user()->count_welcomebonus > 0 || auth() ->user()->count_smsbonus > 0 || auth() ->user()->count_wheelfortune > 0
                                ): ?>
                                    <span class="tooltip-item">
                                        <?php if(auth() ->user()->count_tournaments > 0): ?><p>Tournaments = <?php echo e(number_format(auth() ->user()->count_tournaments, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_happyhours > 0): ?><p>Happy Hours = <?php echo e(number_format(auth() ->user()->count_happyhours, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_refunds > 0): ?><p>Refund = <?php echo e(number_format(auth() ->user()->count_refunds, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_progress > 0): ?><p>Progress Bonus = <?php echo e(number_format(auth() ->user()->count_progress, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_daily_entries > 0): ?><p>Daily Entries = <?php echo e(number_format(auth() ->user()->count_daily_entries, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_invite > 0): ?><p>Invite Sum или Invite Sum Ref = <?php echo e(number_format(auth() ->user()->count_invite, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_welcomebonus > 0): ?><p>Welcome Bonus = <?php echo e(number_format(auth() ->user()->count_welcomebonus, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_smsbonus > 0): ?><p>SMS Bonus = <?php echo e(number_format(auth() ->user()->count_smsbonus, 2,".","")); ?></p><?php endif; ?>
                                        <?php if(auth() ->user()->count_wheelfortune > 0): ?><p>Wheel Fortune = <?php echo e(number_format(auth() ->user()->count_wheelfortune, 2,".","")); ?></p><?php endif; ?>
                                    </span>
                                    <span class="info-icon "><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M0,11A11,11,0,1,0,11,0,11,11,0,0,0,0,11Zm20,0a9,9,0,1,1-9-9A9,9,0,0,1,20,11ZM6.58,16.94,7.43,12,3.85,8.53l4.94-.71L11,3.34l2.21,4.48,4.94.71L14.57,12l.85,4.92L11,14.62Zm5.85-5.62.33,2L11,12.36l-1.76.92.33-2L8.15,9.93l2-.29L11,7.86l.88,1.78,2,.29Z"/></svg></span>
                                <?php else: ?>
                                    <span class="info-icon _disabled"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><path d="M0,11A11,11,0,1,0,11,0,11,11,0,0,0,0,11Zm20,0a9,9,0,1,1-9-9A9,9,0,0,1,20,11ZM6.58,16.94,7.43,12,3.85,8.53l4.94-.71L11,3.34l2.21,4.48,4.94.71L14.57,12l.85,4.92L11,14.62Zm5.85-5.62.33,2L11,12.36l-1.76.92.33-2L8.15,9.93l2-.29L11,7.86l.88,1.78,2,.29Z"/></svg></span>
                                <?php endif; ?>

                                <span class="info-value"><?php echo e(number_format( (auth() ->user()->count_tournaments + auth() ->user()->count_happyhours + auth()->user()->count_refunds + auth() ->user()->count_progress + auth() ->user()->count_daily_entries + auth() ->user()->count_invite + auth() ->user()->count_welcomebonus + auth() ->user()->count_smsbonus + auth() ->user()->count_wheelfortune), 2,".","")); ?> <?php echo e($currency); ?></span>
                            </li>

                            <li class="tooltip-btn refunds-icon">
                                <?php if( $refund && auth()->user()->present()->refunds > 0 && auth()->user()->present()->balance <= $refund->min_balance ): ?>
                                    <span class="tooltip-item"><p>Refund</p></span>
                                    <span class="info-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10,2a7,7,0,0,1,5.81,3H12V7h7V0H17V3.27A8.92,8.92,0,0,0,10,0,10,10,0,0,0,0,10H2A8,8,0,0,1,10,2Zm0,16a7,7,0,0,1-5.81-3H8V13H1v7H3V16.73A8.92,8.92,0,0,0,10,20,10,10,0,0,0,20,10H18A8,8,0,0,1,10,18Z"/></svg>
                                </span>
                                    <span class="info-value refunds" id="refunds"><?php echo e(number_format(auth()->user()->refunds, 2,".","")); ?> <?php echo e($currency); ?></span>
                                <?php else: ?>
                                    <span class="info-icon _disabled">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10,2a7,7,0,0,1,5.81,3H12V7h7V0H17V3.27A8.92,8.92,0,0,0,10,0,10,10,0,0,0,0,10H2A8,8,0,0,1,10,2Zm0,16a7,7,0,0,1-5.81-3H8V13H1v7H3V16.73A8.92,8.92,0,0,0,10,20,10,10,0,0,0,20,10H18A8,8,0,0,1,10,18Z"/></svg>
                                </span>
                                    <span class="info-value refunds"><?php echo e(number_format(auth()->user()->refunds, 2,".","")); ?> <?php echo e($currency); ?></span>
                                <?php endif; ?>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="mobile-menu__item">
                    <a href="<?php echo e(route('frontend.auth.logout')); ?>" class="btn btn--logout">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 22">
                            <path d="M8,21H3a2,2,0,0,1-2-2V3A2,2,0,0,1,3,1H8"/>
                            <polyline points="15 15 19 11 15 7"/><line x1="19" y1="11" x2="7" y2="11"/>
                        </svg>
                        Exit
                    </a>
                </div>


                <?php if(
                    settings('contact_form_active') ||
                    \VanguardLTE\Faq::count() > 0 ||
                    auth()->user()->shop->getBonusesList() ||
                    count($rules) && auth()->user()->shop->hasActiveRules()
                ): ?>
                <ul class="mobile-menu__list">
                    <?php if( settings('contact_form_active') ): ?>
                    <li class="mobile-menu__list-item">
                        <a href="#" class="footer-menu__list-link modal-btn" data-name="modal-contact">Contact Form</a>
                    </li>
                    <?php endif; ?>
                    <?php if( \VanguardLTE\Faq::count() > 0 ): ?>
                    <li class="mobile-menu__list-item">
                        <a href="<?php echo e(route('frontend.faq')); ?>" class="mobile-menu__list-link">FAQ</a>
                    </li>
                    <?php endif; ?>
                    <?php if( auth()->user()->shop->getBonusesList() ): ?>
                    <li class="mobile-menu__list-item">
                        <a href="<?php echo e(route('frontend.bonuses')); ?>" class="mobile-menu__list-link">Bonuses</a>
                    </li>
                    <?php endif; ?>
                    <?php if( count($rules) && auth()->user()->shop->hasActiveRules()): ?>

                        <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(auth()->user()->shop->{'rules_'.$rule->href}): ?>
                                <li class="mobile-menu__list-item">
                                    <a href="#" class="mobile-menu__list-link modal-btn" data-name="modal-<?php echo e($rule->href); ?>"><?php echo e($rule->title); ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php endif; ?>
                </ul>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="menu custom-scroll" data-simplebar>
        <div class="menu__wrap">
            <?php if( settings('use_all_categories') ): ?>
                <a href="<?php echo e(route('frontend.game.list.category', 'all')); ?>" class="menu__link <?php if($currentSliderNum != -1 && $currentSliderNum == 'all'): ?> active <?php endif; ?>"><span><?php echo app('translator')->get('app.all'); ?></span></a>
            <?php endif; ?>
            <?php if( settings('use_my_games') && \VanguardLTE\Lib\GetHotNewMyGames::get_my_games(true)): ?>
                <a href="<?php echo e(route('frontend.game.list.category', 'my_games')); ?>" class="menu__link <?php if($currentSliderNum != -1 && $currentSliderNum == 'my_games'): ?> active <?php endif; ?>"><span><?php echo app('translator')->get('app.my_games'); ?></span></a>
            <?php endif; ?>
            <?php if( settings('use_new_categories') && \VanguardLTE\Lib\GetHotNewMyGames::get_new_games(true)): ?>
                <a href="<?php echo e(route('frontend.game.list.category', 'new')); ?>" class="menu__link <?php if($currentSliderNum != -1 && $currentSliderNum == 'new'): ?> active <?php endif; ?>"><span><?php echo app('translator')->get('app.new'); ?></span></a>
            <?php endif; ?>
            <?php if( settings('use_hot_categories') && \VanguardLTE\Lib\GetHotNewMyGames::get_hot_games(true)): ?>
                <a href="<?php echo e(route('frontend.game.list.category', 'hot')); ?>" class="menu__link <?php if($currentSliderNum != -1 && $currentSliderNum == 'hot'): ?> active <?php endif; ?>"><span><?php echo app('translator')->get('app.hot'); ?></span></a>
            <?php endif; ?>
            <?php if($categories && count($categories)): ?>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('frontend.game.list.category', $category->href)); ?>" class="menu__link <?php if($currentSliderNum != -1 && $category->href == $currentSliderNum): ?> active <?php endif; ?>"><span><?php echo e($category->title); ?></span></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
        <!-- MENU END -->
    </div>

</div>








<?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/partials/header.blade.php ENDPATH**/ ?>