<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Testing\TestResponse;
use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiTestResponse;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::mixin(new JsonApiQueryBuilder);

        TestResponse::mixin(new JsonApiTestResponse);
    }
}
