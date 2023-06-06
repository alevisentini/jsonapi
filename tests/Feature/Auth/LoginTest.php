<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHelpers();
    }

    /**
     * @test
     */
    public function can_issue_access_tokens()
    {
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
        $response = $this->postJson(route('api.v1.login'), $this->validCredentials());

        $response->assertJsonValidationErrorFor('email');
    }

    /**
     * @test
     */
    public function email_is_required()
    {
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
        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'device_name' => ''
        ]));

        $response->assertJsonValidationErrors(['device_name' => 'required']);
    }

    /**
     * @test
     */
    public function user_permissions_are_assigned_as_abilities_to_the_token()
    {
        $user = User::factory()->create();

        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $permission3 = Permission::factory()->create();

        $user->givePermissionTo($permission1);
        $user->givePermissionTo($permission2);

        $response = $this->postJson(route('api.v1.login'), $this->validCredentials([
            'email' => $user->email
        ]));

        $token = $response->json('plain-text-token');

        $this->assertTrue(PersonalAccessToken::findToken($token)->can($permission1->name));
        $this->assertTrue(PersonalAccessToken::findToken($token)->can($permission2->name));
        $this->assertFalse(PersonalAccessToken::findToken($token)->can($permission3->name));
    }

    /**
     * @test
     */
    public function only_one_access_token_can_be_issued_at_a_time()
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer ' . $accessToken])
            ->postJson(route('api.v1.login'))
            ->assertNoContent();

        $this->assertCount(1, $user->tokens);
    }
}
