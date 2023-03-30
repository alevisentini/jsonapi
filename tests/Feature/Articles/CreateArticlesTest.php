<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use Illuminate\Testing\TestResponse;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertCreated();

        $article = Article::first();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                ],
                'links' => [
                    'self' => url(route('api.v1.articles.show', $article)),
                ],
            ],
        ]);
    }

    /**
     * @test
     * 
     * title is required
     */
    public function title_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('title');
    }

    /**
     * @test
     * 
     * slug is required
     */
    public function slug_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     * 
     * content is required
     */
    public function content_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
        ])->assertJsonApiValidationErrors('content');
    }

    /**
     * @test
     * 
     * title must be at least 3 characters
     */
    public function title_must_be_at_least_3_characters()
    {
        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'ab',
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('title');
    }
}
