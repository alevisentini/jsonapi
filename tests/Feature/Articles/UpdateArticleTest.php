<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_update_articles()
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Update title',
                    'slug' => 'update-title',
                    'content' => 'Update content',
        ])->assertOk();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Update title',
                    'slug' => 'update-title',
                    'content' => 'Update content',
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
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'slug' => 'update-title',
                    'content' => 'Update content',
        ])->assertJsonApiValidationErrors('title');
    }

    /**
     * @test
     * 
     * slug is required
     */
    public function slug_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Update title',
                    'content' => 'Update content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     * 
     * content is required
     */
    public function content_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Update title',
                    'slug' => 'update-title',
        ])->assertJsonApiValidationErrors('content');
    }

    /**
     * @test
     * 
     * title must be at least 3 characters
     */
    public function title_must_be_at_least_3_characters()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'ab',
                    'slug' => 'update-title',
                    'content' => 'Update content',
        ])->assertJsonApiValidationErrors('title');
    }
}

