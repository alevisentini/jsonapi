<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\JsonApi\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('api/test', function () {
            return 'OK';
        })->middleware(ValidateJsonApiDocument::class);
    }

    /**
     * @test
     */
    public function data_is_required()
    {
        $this->postJson('api/test', [])->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test', [])->assertJsonApiValidationErrors('data');
    }

    /**
     * @test
     */
    public function data_must_be_an_array()
    {
        $this->postJson('api/test', [
            'data' => 'not-an-array',
        ])->assertJsonApiValidationErrors('data');

        $this->patchJson('api/test', [
            'data' => 'not-an-array',
        ])->assertJsonApiValidationErrors('data');
    }

    /**
     * @test
     */
    public function data_type_is_required()
    {
        $this->postJson('api/test', [
            'data' => [
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test', [
            'data' => [
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test', [
            'data' => [
                [
                    'id' => '123',
                    'type' => 'string',
                ],
            ],
        ])->assertSuccessful();
    }

    /**
     * @test
     */
    public function data_type_must_be_a_string()
    {
        $this->postJson('api/test', [
            'data' => [
                'type' => 123,
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.type');

        $this->patchJson('api/test', [
            'data' => [
                'type' => 123,
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.type');
    }

    /**
     * @test
     */
    public function data_attribute_is_required()
    {
        $this->postJson('api/test', [
            'data' => [
                'type' => 'users',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test', [
            'data' => [
                'type' => 'users',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /**
     * @test
    */
    public function data_attribute_must_be_an_array()
    {
        $this->postJson('api/test', [
            'data' => [
                'type' => 'users',
                'attributes' => 'not-an-array',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('api/test', [
            'data' => [
                'type' => 'users',
                'attributes' => 'not-an-array',
            ],
        ])->assertJsonApiValidationErrors('data.attributes');
    }

    /**
     * @test
     */
    public function data_id_is_required()
    {
        $this->patchJson('api/test', [
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    /**
     * @test
     */
    public function data_id_must_be_a_string()
    {
        $this->patchJson('api/test', [
            'data' => [
                'type' => 'users',
                'id' => 123,
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertJsonApiValidationErrors('data.id');
    }

    /**
     * @test
     */
    public function only_accept_valid_json_api_documents()
    {
        $this->postJson('api/test', [
            'data' => [
                'type' => 'users',
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertSuccessful();

        $this->patchJson('api/test', [
            'data' => [
                'type' => 'users',
                'id' => '123',
                'attributes' => [
                    'name' => 'John Doe',
                ],
            ],
        ])->assertSuccessful();
    }
}
