<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Testing\TestResponse;
use Illuminate\Http\Request;
use App\JsonApi\Mixins\JsonApiQueryBuilder;
use App\JsonApi\Mixins\JsonApiTestResponse;
use App\JsonApi\Mixins\JsonApiRequest;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // cuando se necesite una instancia de la clase \App\Exceptions\Handler 
        // se va a crear una instancia de la clase \App\JsonApi\Exceptions\Handler
        $this->app->singleton(
            \App\Exceptions\Handler::class,
            \App\JsonApi\Exceptions\Handler::class
        );
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
