<?php

namespace Tests;

use Illuminate\Testing\TestResponse;

trait MakesJsonApiRequests
{
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Accept'] = 'application/vnd.api+json';
        
        return parent::json($method, $uri, $data, $headers);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        
        return parent::postJson($uri, $data, $headers);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['Content-Type'] = 'application/vnd.api+json';
        
        return parent::patchJson($uri, $data, $headers);
    }
}