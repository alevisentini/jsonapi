<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'name' => 'Avisenti',
            'email' => 'avisenti@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
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
    public function can_register()
    {
        $response = $this->postJson(
                route('api.v1.register'), 
                $data = $this->validCredentials()
        );

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    /**
     * @test
     */
    public function authenticated_users_cannot_register_again()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))->assertNoContent();
    }

    /**
     * @test
     */
    public function name_is_required()
    {
        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['name' => ''])
        )->assertJsonValidationErrors('name');
    }

    /**
     * @test
     */
    public function name_must_be_unique()
    {
        User::factory()->create(['name' => 'Avisenti']);

        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['name' => 'Avisenti'])
        )->assertJsonValidationErrors('name');
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['email' => ''])
        )->assertJsonValidationErrors('email');
    }

    /**
     * @test
     */
    public function email_must_be_valid()
    {
        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['email' => 'invalid'])
        )->assertJsonValidationErrors('email');
    }

    /**
     * @test
     */
    public function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['email' => $user->email])
        )->assertJsonValidationErrors('email');
    
    }

    /**
     * @test
     */
    public function password_is_required()
    {
        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['password' => ''])
        )->assertJsonValidationErrors('password');
    }

    /**
     * @test
     */
    public function password_must_be_confirmed()
    {
        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['password_confirmation' => ''])
        )->assertJsonValidationErrors('password');
    }

    /**
     * @test
     */
    public function device_name_is_required()
    {
        $this->postJson(
            route('api.v1.register'), 
            $this->validCredentials(['device_name' => ''])
        )->assertJsonValidationErrors('device_name');
    }
}
