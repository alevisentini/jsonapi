<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;

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
        Builder::macro('allowedSorts', function (array $allowedSorts) {
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->sort);

                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

                    $sortField = ltrim($sortField, '-');

                    abort_unless(in_array($sortField, $allowedSorts), 400);

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        });

        Builder::macro('jsonPaginate', function () {
            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1)
            )->appends(request()->only('sort','page.size'));
        });

        TestResponse::macro('assertJsonApiValidationErrors', function ($attribute) {
            /** @var TestResponse $this */

            $pointer = Str::of($attribute)->startsWith('data') 
                        ? '/'.str_replace('.', '/', $attribute)
                        : "/data/attributes/$attribute";

            try {
                $this->assertJsonFragment([
                    'source' => [
                        'pointer' => $pointer
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    "The response does not contain the JSON pointer for the attribute: $attribute" .
                        PHP_EOL . PHP_EOL .
                        $e->getMessage()
                );
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                ]);
            } catch (ExpectationFailedException $e) {
                PHPUnit::fail(
                    "Failed to find a valid JSON:API error response" .
                        PHP_EOL . PHP_EOL .
                        $e->getMessage()
                );
            }

            $this->assertHeader(
                'Content-Type',
                'application/vnd.api+json'
            );

            $this->assertStatus(422);
        });
    }
}
