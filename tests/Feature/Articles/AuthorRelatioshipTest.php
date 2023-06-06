<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;

class AuthorRelatioshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_the_associated_author_identifier()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.relationships.author', $article));

        $response->assertExactJson([
            'data' => [
                'id' => $article->author->getRouteKey(),
                'type' => 'authors',
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_fetch_the_associated_author_resource()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.author', $article));

        $response->assertJson([
            'data' => [
                'id' => $article->author->getRouteKey(),
                'type' => 'authors',
                'attributes' => [
                    'name' => $article->author->name,
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_update_the_associated_author()
    {
        $article = Article::factory()->create();
        $author = User::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.relationships.author', $article), [
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ]
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'user_id' => $author->id,
        ]);
    }

    /**
     * @test
     */
    public function author_must_exist_in_database()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.relationships.author', $article), [
            'data' => [
                'type' => 'authors',
                'id' => '123',
            ]
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'user_id' => $article->user_id,
        ]);
    }
}
