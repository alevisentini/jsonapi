<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentsRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_the_associated_comments_identifiers()
    {
        $this->withoutExceptionHandling(); // this allows us to see the error message

        $article = Article::factory()->hasComments(2)->create();

        $response = $this->getJson(route('api.v1.articles.relationships.comments', $article))
            ->assertJsonCount(2, 'data');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => 'comments',
        ]));
    }

    /**
     * @test
     */
    public function it_returns_an_empty_array_when_there_are_not_associated_comments()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.relationships.comments', $article))
            ->assertJsonCount(0, 'data');

        $response->assertExactJson(
            [
                'data' => [],
            ]
        );
    }

    /**
     * @test
     */
    public function can_fetch_the__associated_comments_resource()
    {
        $this->withoutExceptionHandling(); // this allows us to see the error message
        
        $article = Article::factory()->hasComments(2)->create();

        $response = $this->getJson(route('api.v1.articles.comments', $article))
            ->assertJson([
                'data' => [
                    [
                        'id' => (string) $article->comments[0]->getRouteKey(),
                        'type' => 'comments',
                        'attributes' => [
                            'body' => $article->comments[0]->body,
                        ]
                    ],
                    [
                        'id' => (string) $article->comments[1]->getRouteKey(),
                        'type' => 'comments',
                        'attributes' => [
                            'body' => $article->comments[1]->body,
                        ]
                    ],
                ]
            ]);
    }

    /**
     * @test
     */
    public function can_update_the_associated_comments()
    {
        $this->withoutExceptionHandling(); // this allows us to see the error message

        $comments = Comment::factory(2)->create();

        $article = Article::factory()->create();

        $response = $this->patchJson(
            route('api.v1.articles.relationships.comments', $article),
            [
                'data' => [
                    [
                        'id' => (string) $comments[0]->getRouteKey(),
                        'type' => 'comments',
                    ],
                    [
                        'id' => (string) $comments[1]->getRouteKey(),
                        'type' => 'comments',
                    ],
                ]
            ]
        )->assertJsonCount(2, 'data');

        $comments->map(fn ($comment) => $response->assertJsonFragment([
            'id' => (string) $comment->getRouteKey(),
            'type' => 'comments',
        ]));

        $comments->map(fn ($comment) => $this->assertDatabaseHas('comments', [
            'body' => $comment->body,
            'article_id' => $article->id,
        ]));
    }

    /**
     * @test
     */
    public function comments_must_exists_in_database()
    {
        $article = Article::factory()->create();

        $this->patchJson(
            route('api.v1.articles.relationships.comments', $article),
            [
                'data' => [
                    [
                        'id' => '123',
                        'type' => 'comments',
                    ],
                ]
            ]
        )->assertJsonApiValidationErrors('data.0.id');

        $this->assertDatabaseEmpty('comments');
    }
}
