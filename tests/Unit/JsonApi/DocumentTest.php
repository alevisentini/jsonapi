<?php

namespace Tests\Unit\JsonApi;

use PHPUnit\Framework\TestCase;
use App\JsonApi\Document;

class DocumentTest extends TestCase
{
    /**
     * @test
     */
    public function can_create_jsonapi_documents()
    {
        $document = Document::type('articles')
            ->id('article-id')
            ->attributes([
                'title' => 'Article Title',
            ])
            ->toArray();

        $expected = [
            'data' => [
                'type' => 'articles',
                'id' => 'article-id',
                'attributes' => [
                    'title' => 'Article Title',
                ],
            ],
        ];

        $this->assertEquals($expected, $document);
    }
}
