<?php

namespace Tests\Feature\Authors;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;

class ListAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 
     * can fetch a single author
     */
    public function can_fetch_a_single_author()
    {
        $author = User::factory()->create();

        $response = $this->getJson(route('api.v1.authors.show', $author));

        $response->assertJsonApiResource($author, [
            'name' => $author->name
        ]);

        $this->assertTrue(Str::isUuid($response->json('data.id')),
            'The `id` must be a valid UUID.'
        );
    }

    /**
     * @test
     * 
     * can fetch a list of authors
     */
    public function can_fetch_all_authors()
    {
        $authors = User::factory()->count(3)->create();

        $response = $this->getJson(route('api.v1.authors.index'));

        $response->assertJsonApiResourceCollection($authors, [
            'name'
        ]);
    }
}
