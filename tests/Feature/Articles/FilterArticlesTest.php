<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use App\Models\Category;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 
     * can filter articles by title
     */
    public function can_filter_articles_by_title()
    {
        $art1 = Article::factory()->create(
            [
                'title' => 'Aprende laravel desde cero'
            ]);

        $art2 = Article::factory()->create(
            [
                'title' => 'Aprende vue desde cero'
            ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'title' => 'laravel'
                ]
            ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende laravel desde cero')
            ->assertDontSee('Aprende vue desde cero');

    }

    /**
     * @test
     * 
     * can filter articles by content
     */
    public function can_filter_articles_by_content()
    {
        Article::factory()->create(
            [
                'content' => 'Aprende laravel desde cero'
            ]);

        Article::factory()->create(
            [
                'content' => 'Aprende vue desde cero'
            ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'content' => 'laravel'
                ]
            ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende laravel desde cero')
            ->assertDontSee('Aprende vue desde cero');
    }

    /**
     * @test
     * 
     * can filter articles by year
     */
    public function can_filter_articles_by_year()
    {
        $art1 = Article::factory()->create(
            [
                'title' => 'Aprende laravel 2022',
                'created_at' => now()->subYear()
            ]);

        $art2 =  Article::factory()->create(
            [
                'title' => 'Aprende laravel 2023',
                'created_at' => now()
            ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'year' => now()->format('Y')
                ]
            ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende laravel 2023')
            ->assertDontSee('Aprende laravel 2022');
    }

    /**
     * @test
     * 
     * can filter articles by month
     */
    public function can_filter_articles_by_month()
    {
        Article::factory()->create(
            [
                'title' => 'Aprende laravel 2022',
                'created_at' => now()->subMonth()
            ]);

        Article::factory()->create(
            [
                'title' => 'Aprende laravel 2023',
                'created_at' => now()
            ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'month' => now()->format('m')
                ]
            ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende laravel 2023')
            ->assertDontSee('Aprende laravel 2022');
    }

    /**
     * @test
     * 
     * can filter articles by category
     */
    public function can_filter_articles_by_category()
    {
        Article::factory()->count(2)->create();
        $cat1 = Category::factory()->hasArticles(3)->create(['slug' => 'cat-1']);
        $cat2 = Category::factory()->hasArticles()->create(['slug' => 'cat-2']);
        
        $url = route('api.v1.articles.index', [
            'filter' => [
                'categories' => 'cat-1,cat-2'
                ]
            ]);

        $this->getJson($url)
            ->assertJsonCount(4, 'data')
            ->assertSee($cat1->articles[0]->title)
            ->assertSee($cat1->articles[1]->title)
            ->assertSee($cat1->articles[2]->title)
            ->assertSee($cat2->articles[0]->title);
    }

    /**
     * @test
     * 
     * cannot filter articles by unknown filter
     */
    public function cannot_filter_articles_by_unknown_filter()
    {
        Article::factory()->create(
            [
                'title' => 'Aprende laravel 2022',
                'created_at' => now()->subMonth()
            ]);

        Article::factory()->create(
            [
                'title' => 'Aprende laravel 2023',
                'created_at' => now()
            ]);

        $url = route('api.v1.articles.index', [
            'filter' => [
                'unknown' => now()->format('m')
                ]
            ]);

        $this->getJson($url)
                ->assertJsonApiError(
                    title : 'Bad Request',
                    detail:  "The filter 'unknown' is not allowed in the 'articles' resource.",
                    status:  '400'
        );
    }
}
