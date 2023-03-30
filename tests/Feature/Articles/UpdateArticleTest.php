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
                    'slug' => $article->slug,
                    'content' => 'Update content',
        ])->assertOk();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Update title',
                    'slug' => $article->slug,
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
                    'slug' => 'slug-content',
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
     * slug is unique
     */
    public function slug_must_be_unique()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article1), [
                    'title' => 'Some title',
                    'slug' => $article2->slug,
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     * 
     * slug must only contain letters, numbers and underscores
     */
    public function slug_must_only_contain_letters_numbers_and_underscores()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Some title',
                    'slug' => '?*%',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     * 
     * slug must not contain underscores
     */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Some title',
                    'slug' => 'some_title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     * 
     * slug must not start with dashes
     */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Some title',
                    'slug' => '-some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     * 
     * slug must not end with dashes
     */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Some title',
                    'slug' => 'some-title-',
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
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
                    'title' => 'Update title',
                    'slug' => 'slug-content',
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
                    'slug' => 'slug-content',
                    'content' => 'Update content',
        ])->assertJsonApiValidationErrors('title');
    }
}

