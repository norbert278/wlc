<?php

namespace App\Providers;

use App\Services\FeedbackForm;
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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make( 'feedback_form' )->boot();
    }
}
