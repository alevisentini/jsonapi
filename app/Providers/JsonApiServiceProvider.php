<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Testing\TestResponse;
use Illuminate\Http\Request;
use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiTestResponse;
use App\JsonApi\JsonApiRequest;

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

        Request::mixin(new JsonApiRequest);

        // Request::macro('isJsonApi', function () {
        //     /** @var Request $this */
            
        //     if ($this->header('accept') === 'application/vnd.api+json') {
        //         return true;
        //     }

        //     return $this->header('content-type') === 'application/vnd.api+json';
        // });
    }
}
