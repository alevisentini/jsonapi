<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\Category;

class CategoryRelatioshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_fetch_the_associated_category_identifier()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.relationships.category', $article));

        $response->assertExactJson([
            'data' => [
                'id' => $article->category->getRouteKey(),
                'type' => 'categories',
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_fetch_the_associated_category_resource()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.category', $article));

        $response->assertJson([
            'data' => [
                'id' => $article->category->getRouteKey(),
                'type' => 'categories',
                'attributes' => [
                    'name' => $article->category->name,
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_update_the_associated_category()
    {
        $article = Article::factory()->create();
        $category = Category::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.relationships.category', $article), [
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey(),
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                'type' => 'categories',
                'id' => $category->getRouteKey(),
            ]
        ]);

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $category->id,
        ]);
    }

    /**
     * @test
     */
    public function category_must_exist_in_database()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.relationships.category', $article), [
            'data' => [
                'type' => 'categories',
                'id' => '123',
            ]
        ])->assertJsonApiValidationErrors('data.id');

        $this->assertDatabaseHas('articles', [
            'title' => $article->title,
            'category_id' => $article->category_id,
        ]);
    }
}
