<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHelpers();
    }
    
    /**
     * @test
     */
    public function can_logout()
    {
        $user = User::factory()->create();

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer ' . $accessToken])
            ->postJson(route('api.v1.logout'))
            ->assertNoContent();

        $this->assertNull(PersonalAccessToken::findToken($accessToken));
    }
}
