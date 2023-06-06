<?php

namespace Tests\Feature\Comments;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateCommentsTests extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guests_cannot_create_comments()
    {
        $this->postJson(route('api.v1.comments.store'))
            ->assertJsonApiError(
                title: 'Unauthenticated',
                detail: 'This action requires authentication.',
                status: '401'
            );

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     */
    public function guests_can_create_comments()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        Sanctum::actingAs($user);

        $this->assertDatabaseCount('comments', 0);

        $response = $this->postJson(route('api.v1.comments.store'), [
            'body' => $commentBody = 'This is a comment.',
            '_relationships' => [
                'article' => $article,
                'author' => $user,
            ],
        ])->assertCreated();

        $comment = Comment::first();

        $response->assertJsonApiResource(
            $comment,
            [
                'body' => $commentBody
            ]
        );

        $this->assertDatabaseCount('comments', 1)
            ->assertDatabaseHas('comments', [
                'body' => $commentBody,
                'article_id' => $article->id,
                'user_id' => $user->id,
            ]);
    }

    /**
     * @test
     */
    public function body_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => ''
            ],
        )->assertJsonApiValidationErrors('body');

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     */
    public function article_relationship_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'This is a comment.',
            ],
        )->assertJsonApiValidationErrors('relationships.article');

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     */
    public function article_must_exists_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'This is a comment.',
                '_relationships' => [
                    'article' => Article::factory()->make(),
                ],
            ],
        )->assertJsonApiValidationErrors('relationships.article');

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     */
    public function author_relationship_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'This is a comment.',
                '_relationships' => [
                    'article' => Article::factory()->create(),
                ],
            ],
        )->assertJsonApiValidationErrors('relationships.author');

        $this->assertDatabaseCount('comments', 0);
    }

    /**
     * @test
     */
    public function author_must_exists_in_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(
            route('api.v1.comments.store'),
            [
                'body' => 'This is a comment.',
                '_relationships' => [
                    'article' => Article::factory()->create(),
                    'author' => User::factory()->make(['id' => 'uuid']),
                ],
            ],
        )->assertJsonApiValidationErrors('relationships.author');

        $this->assertDatabaseCount('comments', 0);
    }
}
