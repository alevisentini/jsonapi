<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use Tests\TestCase;

class SortArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 
     * can sort articles by title
     */
    public function can_sort_articles_by_title()
    {
        $article1 = Article::factory()->create(['title' => 'C Title']);
        $article2 = Article::factory()->create(['title' => 'A Title']);
        $article3 = Article::factory()->create(['title' => 'B Title']);

        $this->getJson(route('api.v1.articles.index', ['sort' => 'title']))
            ->assertSeeInOrder([
                $article2->title,
                $article3->title,
                $article1->title,
            ]);
    }

    /**
     * @test
     * 
     * can sort articles by content
     */
    public function can_sort_articles_by_content()
    {
        $article1 = Article::factory()->create(['content' => 'C Content']);
        $article2 = Article::factory()->create(['content' => 'A Content']);
        $article3 = Article::factory()->create(['content' => 'B Content']);

        $this->getJson(route('api.v1.articles.index', ['sort' => 'content']))
            ->assertSeeInOrder([
                $article2->content,
                $article3->content,
                $article1->content,
            ]);
    }

    /**
     * @test
     * 
     * can sort articles by title and content
     */
    public function can_sort_articles_by_title_and_content()
    {
        $article1 = Article::factory()->create(['title' => 'C Title', 'content' => 'C Content']);
        $article2 = Article::factory()->create(['title' => 'A Title', 'content' => 'A Content']);
        $article3 = Article::factory()->create(['title' => 'B Title', 'content' => 'B Content']);

        $this->getJson(route('api.v1.articles.index', ['sort' => 'title,content']))
            ->assertSeeInOrder([
                $article2->content,
                $article3->content,
                $article1->content,
            ]);
    }
    
    /**
     * @test
     * 
     * cannot sort articles by unknown fields
     */
    public function cannot_sort_articles_by_unknown_fields()
    {
        Article::factory()->count(3)->create();

        $this->getJson(route('api.v1.articles.index', ['sort' => 'unknown']))
            ->assertStatus(400);
    }

    /**
     * @test
     * 
     * can sort articles by title in descending order
     */
    public function can_sort_articles_by_title_in_descending_order()
    {
        $article1 = Article::factory()->create(['title' => 'C Title']);
        $article2 = Article::factory()->create(['title' => 'A Title']);
        $article3 = Article::factory()->create(['title' => 'B Title']);

        $this->getJson(route('api.v1.articles.index', ['sort' => '-title']))
            ->assertSeeInOrder([
                $article1->title,
                $article3->title,
                $article2->title,
            ]);
    }

    /**
     * @test
     * 
     * can sort articles by content in descending order
     */
    public function can_sort_articles_by_content_in_descending_order()
    {
        $article1 = Article::factory()->create(['content' => 'C Content']);
        $article2 = Article::factory()->create(['content' => 'A Content']);
        $article3 = Article::factory()->create(['content' => 'B Content']);

        $this->getJson(route('api.v1.articles.index', ['sort' => '-content']))
            ->assertSeeInOrder([
                $article1->content,
                $article3->content,
                $article2->content,
            ]);
    }
    
}
