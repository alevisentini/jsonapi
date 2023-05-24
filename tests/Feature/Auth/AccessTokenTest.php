<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Faker\Provider\ar_EG\Person;
use Laravel\Sanctum\PersonalAccessToken;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'email' => 'avisenti@gmail.com',
            'password' => 'password',
            'device_name' => 'My device'
        ], $overrides);
    }

    /**
     * @test
     */
    public function can_issue_access_tokens()
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'email' => $user->email]));

        $token = $response->json('plain-text-token');

        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->tokenable->is($user));
    }

    /**
     * @test
     */
    public function password_must_be_valid()
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'email' => $user->email,
            'password' => 'invalid-password'
        ]));

        $response->assertJsonValidationErrorFor('email');
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'email' => $user->email,
            'password' => ''
        ]));

        $response->assertJsonValidationErrors(['password' => 'required']);
    }

    /**
     * @test
     */
    public function user_must_be_registered()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials());

        $response->assertJsonValidationErrorFor('email');
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'email' => ''
        ]));

        $response->assertJsonValidationErrors(['email' => 'required']);
    }

    /**
     * @test
     */
    public function email_must_be_valid()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'email' => 'invalid-email'
        ]));

        $response->assertJsonValidationErrors(['email' => 'email']);
    }

    /**
     * @test
     */
    public function device_name_is_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'device_name' => ''
        ]));

        $response->assertJsonValidationErrors(['device_name' => 'required']);
    }
}
