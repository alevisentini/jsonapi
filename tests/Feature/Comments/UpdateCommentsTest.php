<?php

namespace Tests\Feature\Comments;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Comment;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateCommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_cannot_update_comments()
    {
        $comment = Comment::factory()->create();

        $this->patchJson(route('api.v1.comments.update', $comment))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );
    }

    /**
     * @test
     */
    public function can_update_owned_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author, ['comment.update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => $commentBody = 'This is a comment.',
        ])->assertOk();

        $response->assertJsonApiResource(
            $comment,
            [
                'body' => $commentBody,
            ]
        );
    }

    /**
     * @test
     */
    public function can_update_owned_comments_with_relationships()
    {
        $comment = Comment::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs($comment->author, ['comment.update']);

        $response = $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => $commentBody = 'This is a comment.',
            '_relationships' => [
                'article' => $article,
                'author' => $comment->author,
            ],
        ])->assertOk();

        $response->assertJsonApiResource(
            $comment,
            [
                'body' => $commentBody,
            ]
        );

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'article_id' => $comment->article->id,
            'user_id' => $comment->author->id,
        ]);
    }

    /**
     * @test
     */
    public function cannot_update_other_comments()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs(User::factory()->create(), ['comment.update']);

        $this->patchJson(route('api.v1.comments.update', $comment))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function body_is_required()
    {
        $comment = Comment::factory()->create();

        Sanctum::actingAs($comment->author);

        $this->patchJson(route('api.v1.comments.update', $comment), [
            'body' => '',
        ])->assertJsonApiValidationErrors('body');
    }
}
