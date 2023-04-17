<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use Tests\TestCase;

class IncludeCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_include_related_category_of_an_article()
    {
        $article = Article::factory()->create();

        $response = $this->getJson(route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'category'
        ]));

        $response->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $article->category->getRouteKey(),
                    'attributes' => [
                        'name' => $article->category->name,
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_include_related_categories_of_multiple_articles()
    {
        $articles = Article::factory()->count(2)->create();

        $response = $this->getJson(route('api.v1.articles.index', [
            'include' => 'category'
        ]));

        $response->assertJson([
            'included' => [
                [
                    'type' => 'categories',
                    'id' => $articles[0]->category->getRouteKey(),
                    'attributes' => [
                        'name' => $articles[0]->category->name,
                    ]
                ],
                [
                    'type' => 'categories',
                    'id' => $articles[1]->category->getRouteKey(),
                    'attributes' => [
                        'name' => $articles[1]->category->name,
                    ]
                ]
            ]
        ]);
    }
}
