<?php

namespace Tests\Unit\JsonApi;

use PHPUnit\Framework\TestCase;
use App\JsonApi\Document;
use Mockery;

class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_jsonapi_documents()
    {
        $category = Mockery::mock('Category', function ($mock) {
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('category-id');
        });

        $document = Document::type('articles')
            ->id('article-id')
            ->attributes([
                'title' => 'Article Title',
            ])
            ->relationshipsData([
                'category' => $category,
            ])
            ->toArray();

        $expected = [
            'data' => [
                'type' => 'articles',
                'id' => 'article-id',
                'attributes' => [
                    'title' => 'Article Title',
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => 'category-id',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $document);
    }
}
