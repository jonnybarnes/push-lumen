<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Interfaces\TopicRepositoryInterface',
            'App\Repositories\DBTopicRepository'
        );

        $this->app->bind(
            'App\Interfaces\SubscriberRepositoryInterface',
            'App\Repositories\DBSubscriberRepository'
        );

        $this->app->bind(
            'App\Interfaces\SubscriptionRepositoryInterface',
            'App\Repositories\DBSubscriptionRepository'
        );
    }
}
