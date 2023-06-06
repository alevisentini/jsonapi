<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Article;
use Tests\TestCase;
use Illuminate\Testing\Assert as PHPUnit;

class PaginateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 
     * can paginate articles
     */
    public function can_paginate_articles()
    {
        $articles = Article::factory()->count(6)->create();

        $response = $this->getJson(route('api.v1.articles.index', ['page' => ['size' => 2, 'number' => 2]]));
        
        $response->assertSee([
                $articles[2]->title,
                $articles[3]->title,
        ]);

        $response->assertDontSee([
                $articles[0]->title,
                $articles[1]->title,
                $articles[4]->title,
                $articles[5]->title,
        ]);
        
        $response->assertJsonStructure([
                'links' => ['first','last','prev','next']
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        PHPUnit::assertStringContainsString(
            'page[number]=1',
            $firstLink
        );

        PHPUnit::assertStringContainsString(
            'page[size]=2',
            $firstLink
        );

        PHPUnit::assertStringContainsString(
            'page[number]=3',
            $lastLink
        );

        PHPUnit::assertStringContainsString(
            'page[size]=2',
            $lastLink
        );

        PHPUnit::assertStringContainsString(
            'page[number]=1',
            $prevLink
        );

        PHPUnit::assertStringContainsString(
            'page[size]=2',
            $prevLink
        );

        PHPUnit::assertStringContainsString(
            'page[number]=3',
            $nextLink
        );

        PHPUnit::assertStringContainsString(
            'page[size]=2',
            $nextLink
        );
    }

    /**
     * @test
     * 
     * can paginate and sort articles
     */
    public function can_paginate_and_sort_articles()
    {
        Article::factory()->create(['title' => 'C Title']);
        Article::factory()->create(['title' => 'A Title']);
        Article::factory()->create(['title' => 'B Title']);

        $response = $this->getJson(route('api.v1.articles.index', ['page' => ['size' => 1, 'number' => 1], 'sort' => 'title']));
        
        $response->assertSee([
                'A Title'
        ]);

        $response->assertDontSee([
                'C Title',
                'B Title',
        ]);
        
        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        //$prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        PHPUnit::assertStringContainsString(
            'sort=title',
            $firstLink
        );

        // PHPUnit::assertStringContainsString(
        //     'sort=title',
        //     $prevLink
        // );

        PHPUnit::assertStringContainsString(
            'sort=title',
            $lastLink
        );

        PHPUnit::assertStringContainsString(
            'sort=title',
            $nextLink
        );
    }

    /**
     * @test
     * 
     * can paginate filtered articles
     */
    // public function can_paginate_filtered_articles()
    // {   
    //     Article::factory()->count(3)->create();
    //     Article::factory()->create(['title' => 'C Title']);
    //     Article::factory()->create(['title' => 'A Title']);
    //     Article::factory()->create(['title' => 'B Title']);

    //     $response = $this->getJson(route('api.v1.articles.index', ['page' => ['size' => 1, 'number' => 1], 'filter' => ['title' => 'A']]));
        
    //     $firstLink = urldecode($response->json('links.first'));
    //     $lastLink = urldecode($response->json('links.last'));
    //     //$prevLink = urldecode($response->json('links.prev'));
    //     $nextLink = urldecode($response->json('links.next'));

    //     PHPUnit::assertStringContainsString(
    //         'filter%5Btitle%5D=A',
    //         $firstLink
    //     );

    //     // PHPUnit::assertStringContainsString(
    //     //     'filter%5Btitle%5D=A',
    //     //     $prevLink
    //     // );

    //     PHPUnit::assertStringContainsString(
    //         'filter%5Btitle%5D=A',
    //         $lastLink
    //     );

    //     PHPUnit::assertStringContainsString(
    //         'filter%5Btitle%5D=A',
    //         $nextLink
    //     );
    // }
}
