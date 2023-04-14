<?php

namespace App\JsonApi\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\JsonApi\Document;

trait JsonApiResource
{
    abstract public function toJsonApi(): array;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return Document::type($this->getResourceType())
            ->id($this->resource->getRouteKey())
            ->attributes($this->filterAttributes($this->toJsonApi()))
            ->relationshipsLinks($this->getRelationshipsLinks())
            ->links([
                'self' => url(route('api.v1.' . $this->getResourceType() . '.show', $this->resource)),
            ])
            ->get('data');
        
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

    public static function collection($resource): AnonymousResourceCollection
    {
        $collection = parent::collection($resource);

        $collection->with['links'] = ['self' => $resource->path()];

        return $collection;
    }

}