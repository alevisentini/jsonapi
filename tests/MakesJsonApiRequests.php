<?php

namespace Tests;

use App\JsonApi\Document;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;
    protected bool $addJsonApiHeaders = true;

    public function withoutJsonApiDocumentFormatting(): self
    {
        $this->formatJsonApiDocument = false;

        return $this;
    }

    public function withoutJsonApiHeaders(): self
    {
        $this->addJsonApiHeaders = false;

        return $this;
    }

    public function withoutJsonApiHelpers(): self
    {
        $this->addJsonApiHeaders = false;
        $this->formatJsonApiDocument = false;

        return $this;
    }

    protected function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('api/v1/')->before('/');
        $id = (string) Str::of($path)->after($type)->replace('/', '');

        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationshipsData($data['_relationships'] ?? [])
            ->toArray();
    }


    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        if ($this->addJsonApiHeaders) {
            $headers['accept'] = 'application/vnd.api+json';

            if (in_array($method, ['POST', 'PATCH'])) {
                $headers['content-type'] = 'application/vnd.api+json';
            }
        }

        if ($this->formatJsonApiDocument && in_array($method, ['POST', 'PATCH'])) {
            if (! isset($data['data'])) {
                $formattedData = $this->getFormattedData($uri, $data);
            }
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers);
    }
}
