<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_delete_articles()
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseMissing('articles', $article->toArray());
    }
}
