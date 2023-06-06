<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class CreateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_articles()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($user, ['articles.create']);

        $response = $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                    '_relationships' => [
                        'category' => $category,
                        'author' => $user,
                    ],
        ])->assertCreated();

        $article = Article::first();

        $response->assertJsonApiResource($article, [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ]);

        $this->assertDatabaseHas('articles', [
                    'title' => 'Some title',
                    'user_id' => $user->id,
                    'category_id' => $category->id,
        ]);
    }

    /**
     * @test
     */
    public function title_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('title');
    }

    /**
     * @test
     */
    public function slug_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     */
    public function slug_must_be_unique()
    {
        Sanctum::actingAs(User::factory()->create());

        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => $article->slug,
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     */
    public function slug_must_only_contain_letters_numbers_and_underscores()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => '?*%',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     */
    public function slug_must_not_contain_underscores()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some_title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     */
    public function slug_must_not_start_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => '-some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    /**
     * @test
     */
    public function slug_must_not_end_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title-',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('slug');
    }

    
    /**
     * @test
     */
    public function content_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
        ])->assertJsonApiValidationErrors('content');
    }

    /**
     * @test
     */
    public function title_must_be_at_least_3_characters()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'ab',
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('title');
    }

    /**
     * @test
     */
    public function category_relationship_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }

    /**
     * @test
     */
    public function category_relationship_must_exist_on_database()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [
                    'title' => 'Some title',
                    'slug' => 'some-title',
                    'content' => 'Some content',
                    '_relationships' => [
                        'category' => Category::factory()->make(),
                    ],
        ])->assertJsonApiValidationErrors('data.relationships.category.data.id');
    }

    /**
     * @test
     */
    public function guests_cannot_create_articles()
    {
        $this->postJson(route('api.v1.articles.store'))->assertUnauthorized()
                ->assertJsonApiError(
                    title: 'Unauthenticated',
                    detail: 'This action requires authentication.',
                    status: '401',
        );

        $this->assertDatabaseCount('articles', 0);  
    }

}
