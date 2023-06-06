<?php

namespace Tests\Feature\Comments;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class DeleteCommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_cannot_delete_comments()
    {
        $comment = Comment::factory()->create();

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );
    }

    /**
     * @test
     */
    public function can_delete_owned_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment.delete']);

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertNoContent();

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     */
    public function cannot_delete_other_users_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['comment.delete']);

        $this->deleteJson(route('api.v1.comments.destroy', $comment))
            ->assertForbidden();

        $this->assertDatabaseCount('comments', 1);
    }
}
