<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Probots\Pinecone\Client as PineconeClient;

class PineconeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PineconeClient::class, function ($app) {
            return new PineconeClient(
                env('PINECONE_API_KEY'),
                env('PINECONE_INDEX_HOST')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
