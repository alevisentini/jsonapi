<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                ],
            ],
        ]);

        $response->assertCreated();

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
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'slug' => 'some-title',
                    'content' => 'Some content',
                ],
            ],
        ]);

        //$response->assertJsonValidationErrors('data.attributes.title');
        $response->assertJsonStructure([
            'errors' => [
                ['title','detail','source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => [
                'pointer' => '/data/attributes/title',
            ]
        ])->assertHeader(
            'Content-Type', 'application/vnd.api+json'
        )->assertStatus(422);   
    }

    /**
     * @test
     * 
     * slug is required
     */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Some title',
                    'content' => 'Some content',
                ],
            ],
        ]);

        $response->assertJsonValidationErrors('data.attributes.slug');
    }

    /**
     * @test
     * 
     * content is required
     */
    public function content_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                ],
            ],
        ]);

        $response->assertJsonValidationErrors('data.attributes.content');
    }

    /**
     * @test
     * 
     * title must be at least 3 characters
     */
    public function title_must_be_at_least_3_characters()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'ab',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                ],
            ],
        ]);

        $response->assertJsonValidationErrors('data.attributes.title');
    }
}
