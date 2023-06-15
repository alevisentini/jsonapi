<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExceptionsHandlerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function json_api_errors_are_only_shown_to_requests_with_the_prefix_api()
    {
        $this->getJson('api/invalid-endpoint')
                ->assertJsonApiError(
                    title: 'Not Found',
                    detail: 'The route api/invalid-endpoint could not be found.',
                    status: '404'
            );

        $this->getJson('api/v1/invalid-resource/invalid-id')
                ->assertJsonApiError(
                    title: 'Not Found',
                    detail: 'The route api/v1/invalid-resource/invalid-id could not be found.',
                    status: '404'
            );
    }

    /**
     * @test
     */
    public function default_laravel_error_is_shown_to_requests_outside_the_prefix_api()
    {
        $this->getJson('non/apì/route')
                ->assertJson([
                    'message' => 'The route non/apì/route could not be found.',
                ]);

        $this->withoutJsonApiHeaders()
                ->getJson('non/apì/route')
                ->assertJson([
                    'message' => 'The route non/apì/route could not be found.',
                ]);
    }
}
