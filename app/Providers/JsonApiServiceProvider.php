<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

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
    }
}
