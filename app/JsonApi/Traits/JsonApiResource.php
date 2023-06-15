<?php

namespace App\JsonApi\Traits;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\JsonApi\Document;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait JsonApiResource
{
    abstract public function toJsonApi(): array;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        if ($request->filled('include')) {
            foreach ($this->getIncludes() as $include) {
                
                if ($include->resource instanceof Collection) {
                    foreach ($include->resource as $resource) {
                        $this->with['included'][] = $resource;
                    }
                    continue;
                }
                
                if ($include->resource instanceof MissingValue) {
                    continue;
                }
                $this->with['included'] = $this->getIncludes();
            }
        }
                
        return Document::type($this->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipsLinks($this->getRelationshipsLinks())
            ->links([
                'self' => url(route('api.v1.' . $this->getResourceType() . '.show', $this->resource)),
            ])
            ->get('data');
        
    }

    public function getIncludes(): array
    {
        return [];
    }

    public function getRelationshipsLinks(): array
    {
        return [];
    }

    /**
     * override the default toResponse method to add location data
     */
    public function withResponse($request, $response)
    {
        $response->header(
            'Location',
            route('api.v1.' . $this->getResourceType() . '.show', $this->resource),
        );
    }

    public function filterAttributes(array $attributes): array
    {
        return array_filter($attributes, function ($value) {
            if (request()->isNotFilled('fields')) {
                return true;
            }

            $fields = explode(',', request('fields.' . $this->getResourceType()));

            if ($value === $this->getRouteKey()) {
                return in_array($this->getRouteKeyName(), $fields);
            }

            return $value;
        });
    }

    public static function collection($resources): AnonymousResourceCollection
    {
        $collection = parent::collection($resources);

        if (request()->filled('include')) {
            foreach ($collection->resource as $resource) {
                foreach ($resource->getIncludes() as $include) {
                    if ($include->resource instanceof Collection) {
                        foreach ($include->resource as $resource) {
                            $collection->with['included'][] = $resource;
                        }
                        continue;
                    }

                    if ($include->resource instanceof MissingValue) {
                        continue;
                    }
                    $collection->with['included'][] = $include;
                }
            }
        }

        $collection->with['links'] = ['self' => request()->path()];

        return $collection;
    }

    public static function identifier(Model $resource): array
    {
        return Document::type($resource->getResourceType())
            ->id($resource->getRouteKey())
            ->toArray();
    }

    public static function identifiers(Collection $resource): array
    {
        return $resource->isEmpty() 
            ? Document::empty()
            : Document::type($resource->first()->getResourceType())
            ->ids($resource)
            ->toArray();
    }

}