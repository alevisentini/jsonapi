<?php

namespace App\JsonApi;

use Illuminate\Support\Collection;

class Document extends Collection
{
    public static function type(string $type): self
    {
        return new self([
            'data' => [
                'type' => $type,
            ],
        ]);
    }

    public function id(string $id): self
    {
        if ($id) {
            $this->items['data']['id'] = (string) $id;
        }

        return $this;
    }

    public function attributes(array $attributes): self
    {
        unset($attributes['_relationships']);
        
        $this->items['data']['attributes'] = $attributes;

        return $this;
    }

    public function links(array $links): self
    {
        $this->items['data']['links'] = $links;

        return $this;
    }

    public function relationships(array $relationships): self
    {
        foreach ($relationships as $key => $relationship) {
            $this->items['data']['relationships'][$key] = [
                'data' => [
                    'type' => $relationship->getResourceType(),
                    'id' => $relationship->getRouteKey(),
                ],
            ];
        }

        return $this;
    }

}