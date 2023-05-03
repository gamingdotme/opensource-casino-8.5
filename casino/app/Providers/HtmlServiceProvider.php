<?php

namespace VanguardLTE\Providers;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Collective\Html\HtmlServiceProvider as BaseHtmlServiceProvider;

class HtmlServiceProvider extends BaseHtmlServiceProvider
{
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function($app) {

   /*          if (env('FORCE_SSL')) {
                $app['url']->forceScheme('https');
            }

  */           return new HtmlBuilder($app['url'], $app['view']);
        });
    }

    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function($app) {

/*             if (env('FORCE_SSL')) {
                $app['url']->forceScheme('https');
            } */

            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token());

            return $form->setSessionStore($app['session.store']);
        });
    }
}