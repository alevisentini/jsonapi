<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthorRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_the_associated_author_identifier()
    {
        $comment = Comment::factory()->create();

        $this->getJson(route('api.v1.comments.relationships.author', $comment))
            ->assertExactJson(
                [
                    'data' => [
                        'id' => $comment->author->getRouteKey(),
                        'type' => 'authors',
                    ]
                ]);
    }

    /**
     * @test
     */
    public function can_fetch_the_associated_author_resource()
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.author', $comment);

        $this->getJson($url)
            ->assertJson([
            'data' => [
                'id' => $comment->author->getRouteKey(),
                'type' => 'authors',
                'attributes' => [
                    'name' => $comment->author->name,
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_update_the_associated_author()
    {
        $comment = Comment::factory()->create();

        $author = User::factory()->create();

        $url = route('api.v1.comments.relationships.author', $comment);

        $response = $this->patchJson(
            $url,
            [
                'data' => [
                    'type' => 'authors',
                    'id' => $author->getRouteKey(),
                ]
            ]
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ]
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => $author->id,
        ]);
    }

    /**
     * @test
     */
    public function author_must_exists_in_database()
    {
        $comment = Comment::factory()->create();

        $url = route('api.v1.comments.relationships.author', $comment);

        $this->patchJson(
            $url,
            [
                'data' => [
                    'type' => 'authors',
                    'id' => 'non-existing-author',
                ]
            ]
        )->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'user_id' => $comment->user_id,
        ]);
    }
}
