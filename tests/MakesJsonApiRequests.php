<?php

namespace Tests;

use Closure;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;
use Illuminate\Support\Str;
trait MakesJsonApiRequests
{
    protected function assertJsonApiValidationErrors(): Closure
    {
        return function ($attribute) {
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
        };
    }

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('assertJsonApiValidationErrors', $this->assertJsonApiValidationErrors());
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';

        return parent::json($method, $uri, $data, $headers);
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
