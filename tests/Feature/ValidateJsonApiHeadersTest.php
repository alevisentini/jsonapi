<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Http\Middleware\ValidateJsonApiHeaders;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Route::any('test', function () {
            return 'OK';
        })->middleware(ValidateJsonApiHeaders::class);
    }

    /**
     * @test
     * 
     * validate that header Accept is present in all get requests, using a middleware
     */
    public function validate_that_header_accept_is_present_in_all_get_requests()
    {
        $this->get('test')->assertStatus(406);

        $this->get('test', [
            'Accept' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /**
     * @test
     * 
     * validate that header Content-Type is present in all post requests, using a middleware
     */
    public function validate_that_header_content_type_is_present_in_all_post_requests()
    {
        $this->post('test', [], [
            'Accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->post('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /**
     * @test
     * 
     * validate that header Content-Type is present in all patch requests, using a middleware
     */
    public function validate_that_header_content_type_is_present_in_all_patch_requests()
    {
        $this->patch('test', [], [
            'Accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->patch('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /**
     * @test
     * 
     * validate that header Content-Type is present in all responses, using a middleware
     */
    public function validate_that_header_content_type_is_present_in_all_responses()
    {
        $this->get('test', [
            'Accept' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->post('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');

        $this->patch('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeader('Content-Type', 'application/vnd.api+json');
    }

    /**
     * @test
     * 
     * validate that header Content-Type is not present on empty responses
     */
    public function validate_that_header_content_type_is_not_present_on_empty_responses()
    {
        Route::any('test', function () {
            return response()->noContent();
        })->middleware(ValidateJsonApiHeaders::class);

        $this->get('test', [
            'Accept' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');

        $this->post('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');

        $this->patch('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');

        $this->delete('test', [], [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('Content-Type');
    }

}
