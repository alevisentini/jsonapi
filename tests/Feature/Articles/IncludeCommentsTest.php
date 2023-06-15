<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;

class IncludeCommentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_include_related_comments_of_an_article()
    {
        $this->withoutExceptionHandling(); // this allows us to see the error message

        $article = Article::factory()->hasComments(2)->create();

        // article/the-slug?include=comments
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'include' => 'comments',
        ]);

        $response = $this->getJson($url)
            ->assertJsonCount(2, 'included');

        $article->comments->map(fn ($comment) => $response->assertJsonFragment([
            'type' => 'comments',
            'id' => (string) $comment->getRouteKey(),
            'attributes' => [
                'body' => $comment->body,
            ],
        ]));
    }

    /**
     * @test
     */
    public function can_include_related_comments_of_multiple_articles()
    {
        $this->withoutExceptionHandling(); // this allows us to see the error message

        $article = Article::factory()->hasComments(2)->create();
        $article2 = Article::factory()->hasComments(2)->create();

        // articles?include=comments
        $url = route('api.v1.articles.index', [
            'include' => 'comments',
        ]);

        $response = $this->getJson($url)
            ->assertJsonCount(4, 'included');

        // $article->comments->map(fn ($comment) => $response->assertJsonFragment([
        //     'type' => 'comments',
        //     'id' => (string) $comment->getRouteKey(),
        //     'attributes' => [
        //         'body' => $comment->body,
        //     ],
        // ]));
    }
}
