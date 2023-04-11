<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'type' => 'articles',
                'id' => (string) $this->resource->getRouteKey(),
                'attributes' => array_filter([
                    'title' => $this->resource->title,
                    'slug' => $this->resource->slug,
                    'content' => $this->resource->content,
                ], function ($value) {

                    if (request()->isNotFilled('fields')) {
                        return true;
                    }

                    $fields = explode(',', request('fields.articles'));
                    
                    if ($value === $this->getRouteKey())
                    {
                        return in_array('slug', $fields);
                    }
                    
                    return $value;
                }),
                'links' => [
                    'self' => url(route('api.v1.articles.show', $this->resource)),
                ],
        ];
    }

    /**
     * override the default toResponse method to add location data
     */
    public function toResponse($request)
    {
        return parent::toResponse($request)
            ->withHeaders([
                'Location' => route('api.v1.articles.show', $this->resource),
            ]);
    }
}
