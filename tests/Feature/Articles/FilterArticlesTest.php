<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
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
        Article::factory()->create(
            [
                'title' => 'Aprende laravel desde cero'
            ]);

        Article::factory()->create(
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
        Article::factory()->create(
            [
                'title' => 'Aprende laravel 2022',
                'created_at' => now()->subYear()
            ]);

        Article::factory()->create(
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
            ->assertSee('Aprende laravel 2022')
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
            ->assertStatus(400);
    }
}
