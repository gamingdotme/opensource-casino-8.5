<link rel="stylesheet" href="/woocasino/css/appef20.css">

<header class="header">
    <div class="header__mob-container">
        <div class="header__logo">
            <a class="header__logo-link" scroll-up="" href="#"> <img class="header__logo-img" src="/woocasino/resources/images/logo.png" alt="WooCasino"> </a>
        </div>
        <div class="header__mob-wrp">
            <button class="header__mob-btn button button-secondary button-small ng-scope" ng-click="openModal($event, '#login-modal')"><?php echo app('translator')->get('app.log_in'); ?></button>
            <a class="header__mobile-menu"> <span class="header__mobile-menu-icon"></span> <span class="header__mobile-menu-icon"></span> <span class="header__mobile-menu-icon"></span> </a>
        </div>
    </div>
    <div class="header__container">
        <div class="header__logo">
            <a class="header__logo-link" scroll-up="" href="#"> <img class="header__logo-img" src="/woocasino/resources/images/logo.png" alt="WooCasino"> </a>
        </div>
        <div class="header__container-bg">
            <div class="header-auth ng-isolate-scope">
                <div class="header-auth__anon ng-scope">
                    <div class="header-auth__anon-status"> <img class="header-auth__anon-img" src="/woocasino/resources/images/status/anon.svg" alt=""> </div>
                    <?php if( !isset(auth()->user()->username) ): ?>
                    <div class="header-auth__anon-btn-wrp">
                        <button class="modal-btn button button-primary header-auth__reg-btn ng-scope" data-name="modal-register" ng-click="openModal($event, '#registration-confirm')"><?php echo app('translator')->get('app.register'); ?></button>
                        <button class="modal-btn button button-secondary header-auth__login-btn ng-scope" ng-click="openModal($event, '#login-modal')" ><?php echo app('translator')->get('app.log_in'); ?></button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <nav class="header-menu ng-scope ng-isolate-scope" type="main-menu">
                <div class="header-menu__live">
                    <a class="header-menu__live-link" scroll-up="" href="<?php echo e(route('frontend.game.list.category', 'slots')); ?>"> 
                        <span class="header-menu__live-icon icon-woo-menu-default icon-woo-blackjack"></span> <span class="header-menu__live-text ng-scope"><?php echo app('translator')->get('app.slots'); ?></span> 
                    </a>
                    <a class="header-menu__live-link" scroll-up="" href="<?php echo e(route('frontend.game.list.category', 'hot')); ?>">
                        <span class="header-menu__live-icon icon-woo-menu-default icon-woo-roulette"></span> <span class="header-menu__live-text ng-scope"><?php echo app('translator')->get('app.hot_game'); ?></span>
                    </a>
                </div>
                <?php if(isset($categories)): ?>
                <ul class="header-menu__list">
                    <?php if( settings('use_all_categories') || true): ?>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games <?php if($currentSliderNum != -1 && $currentSliderNum == 'all'): ?> header-menu__link--current <?php endif; ?>" scroll-up="" href="<?php echo e(route('frontend.game.list.category', 'all')); ?>"> <i class="header-menu__icon icon-woo-menu-default icon-woo-bgaming-slot-battle"></i> <span class="header-menu__text ng-binding"><?php echo app('translator')->get('app.all'); ?></span> </a>
                        </li>
                    <?php endif; ?>
                    <?php if( settings('use_new_categories') || true): ?>
                        <li class="header-menu__item ng-scope">
                            <a class="header-menu__link header-menu__link--games <?php if($currentSliderNum != -1 && $currentSliderNum == 'new'): ?> header-menu__link--current <?php endif; ?>" scroll-up="" href="<?php echo e(route('frontend.game.list.category', 'new')); ?>"> <i class="header-menu__icon icon-woo-menu-default icon-woo-bgaming-slot-battle"></i> <span class="header-menu__text ng-binding"><?php echo app('translator')->get('app.new'); ?></span> </a>
                        </li>
                    <?php endif; ?>
                    <?php if($categories && count($categories)): ?>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index=>$category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="header-menu__item ng-scope">
                                <a class="header-menu__link header-menu__link--games <?php if($currentSliderNum != -1 && $currentSliderNum == $category->href): ?> header-menu__link--current <?php endif; ?>" scroll-up="" href="<?php echo e(route('frontend.game.list.category', $category->href)); ?>"> <i class="header-menu__icon icon-woo-menu-default icon-woo-bgaming-slot-battle"></i> <span class="header-menu__text ng-binding"><?php echo e($category->title); ?></span> </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </ul>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
<?php /**PATH /home/betshopus/casino/resources/views/frontend/Default/partials/header_not_logged.blade.php ENDPATH**/ ?>