<?php

namespace App\JsonApi;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;

class JsonApiTestResponse
{
    public function assertJsonApiValidationErrors()
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
}