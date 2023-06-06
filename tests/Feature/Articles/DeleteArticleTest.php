<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_delete_owned_articles()
    {
        $this->withoutExceptionHandling();

        $article = Article::factory()->create();

        Sanctum::actingAs($article->author, ['articles.delete']);

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();

        $this->assertDatabaseMissing('articles', $article->toArray());
    }

    /**
     * @test
     */
    public function cannot_delete_articles_owned_by_other_users()
    {
        $article = Article::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertForbidden();

        $this->assertDatabaseCount('articles', 1);
    }

    /**
     * @test
     */
    public function guest_users_cannot_delete_articles()
    {
        $article = Article::factory()->create();

        $this->deleteJson(route('api.v1.articles.destroy', $article))
                ->assertJsonApiError(
                    title: 'Unauthenticated',
                    detail: 'This action requires authentication.',
                    status: '401',
        );
    }
}
