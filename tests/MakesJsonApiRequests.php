<?php

namespace Tests;

use App\JsonApi\Document;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;

    protected function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('api/v1/')->before('/');
        $id = (string) Str::of($uri)->after($type)->replace('/', '');

        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationships($data['_relationships'] ?? [])
            ->toArray();
    }

    public function withoutJsonApiDocumentFormatting()
    {
        $this->formatJsonApiDocument = false;
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';
        
        if ($this->formatJsonApiDocument) {
            $formattedData = $this->getFormattedData($uri, $data);
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';

        return parent::postJson($uri, $data, $headers);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';

        return parent::patchJson($uri, $data, $headers);
    }
}
