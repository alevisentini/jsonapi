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
        $this->items['data']['id'] = (string) $id;

        return $this;
    }

    public function attributes(array $attributes): self
    {
        $this->items['data']['attributes'] = $attributes;

        return $this;
    }

    public function links(array $links): self
    {
        $this->items['data']['links'] = $links;

        return $this;
    }

}