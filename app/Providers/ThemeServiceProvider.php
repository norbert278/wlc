<?php

namespace App\Providers;

use App\Services\FeedbackForm;
use App\Services\FeedbackResult;
use Roots\Acorn\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton( 'feedback_form', function () {
            return new FeedbackForm();
        } );
        $this->app->singleton( 'feedback_results', function () {
            return new FeedbackResult();
        } );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make( 'feedback_form' )->boot();
        $this->app->make( 'feedback_results' )->boot();
    }
}
