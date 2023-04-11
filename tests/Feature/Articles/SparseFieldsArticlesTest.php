<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use Tests\TestCase;

class SparseFieldsArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 
     * can request sparse fieldsets
     * 
     * articles?fields[articles]=title
     */
    public function can_request_sparse_fieldsets_in_article_index()
    {
        $article = Article::factory()->create();
        
        $url = route('api.v1.articles.index', [
            'fields' => [
                'articles' => 'title,slug'
            ]
        ]);

        //dd(urldecode($url));

        $this->getJson($url)
            ->assertJsonFragment([
                'title' => $article->title,
                'slug' => $article->slug
            ])
            ->assertJsonMissing([
                'content' => $article->content
            ])
            ->assertJsonMissing([
                'content' => null
            ]);
    }

    /**
     * @test
     * 
     * route key must be added automatically
     */
    public function route_key_must_be_added_automatically_in_article_index()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.index', [
            'fields' => [
                'articles' => 'title'
            ]
        ]);

        $this->getJson($url)
        ->assertJsonFragment([
            'title' => $article->title
            ])
            ->assertJsonMissing([
                'slug' => $article->slug,
                'content' => $article->content
            ]);
    }

    /**
     * @test
     * 
     * can request sparse fieldsets
     * 
     * articles?fields[articles]=title
     */
    public function can_request_sparse_fieldsets_in_article_show()
    {
        $article = Article::factory()->create();
        
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'fields' => [
                'articles' => 'title,slug'
            ]
        ]);

        //dd(urldecode($url));

        $this->getJson($url)
            ->assertJsonFragment([
                'title' => $article->title,
                'slug' => $article->slug
            ])
            ->assertJsonMissing([
                'content' => $article->content
            ])
            ->assertJsonMissing([
                'content' => null
            ]);
    }

    /**
     * @test
     * 
     * route key must be added automatically
     */
    public function route_key_must_be_added_automatically_in_article_show()
    {
        $article = Article::factory()->create();

        $url = route('api.v1.articles.show', [
            'article' => $article,
            'fields' => [
                'articles' => 'title'
            ]
        ]);

        $this->getJson($url)
        ->assertJsonFragment([
            'title' => $article->title
            ])
            ->assertJsonMissing([
                'slug' => $article->slug,
                'content' => $article->content
            ]);
    }
}
