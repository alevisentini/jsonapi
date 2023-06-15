<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;

class IncludeAuthorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_include_related_author_of_an_article()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'author'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'authors',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name
                    ]
                ]
            ]
        ]);
    }

    /**
     * @test
     */
    public function can_include_related_author_of_multiple_articles()
    {
        $article = Article::factory()->create();
        $article2 = Article::factory()->create();

        $url = route('api.v1.articles.index', [
            'include' => 'author'
        ]);

        $this->getJson($url)->assertJson([
            'included' => [
                [
                    'type' => 'authors',
                    'id' => $article->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article->author->name
                    ]
                ],
                [
                    'type' => 'authors',
                    'id' => $article2->author->getRouteKey(),
                    'attributes' => [
                        'name' => $article2->author->name
                    ]
                ]
            ]
        ]);
    }
}
