<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_articles()
    {
        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                    '_relationships' => [
                        'category' => $category,
                    ],
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
     * slug is unique
     */
    public function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => $article->slug,
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
        $this->postJson(route('api.v1.articles.store'), [
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
        $this->postJson(route('api.v1.articles.store'), [
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
        $this->postJson(route('api.v1.articles.store'), [
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
        $this->postJson(route('api.v1.articles.store'), [
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

    /**
     * @test
     * 
     * category relationship is required
     */
    public function category_relationship_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }

    /**
     * @test
     * 
     * category relationship must exist
     */
    public function category_relationship_must_exist_on_database()
    {
        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                    '_relationships' => [
                        'category' => Category::factory()->make(),
                    ],
        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }

}
