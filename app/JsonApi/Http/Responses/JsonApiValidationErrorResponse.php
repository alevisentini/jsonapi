<?php

namespace App\JsonApi\Http\Responses;

use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class JsonApiValidationErrorResponse extends JsonResponse
{

    public function __construct(ValidationException $exception, $status = 422)
    {

        // ALTERNATIVE 1
        // $errors = [];
        // foreach ($exception->errors() as $field => $message) {
        //     $pointer = '/' . str_replace('.', '/', $field);

        //     $errors[] = [
        //         'title' => $title,
        //         'detail' => $message[0],
        //         'source' => [
        //             'pointer' => $pointer
        //         ]
        //     ];
        // }

        // ALTERNATIVE 2

        $data = $this->formatJsonApiErrors($exception);

        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];

        parent::__construct($data, $status, $headers);
    }

    protected function formatJsonApiErrors(ValidationException $exception): array
    {
        $title = $exception->getMessage();

        return [
            'errors' => collect($exception->errors())
                ->map(function ($message, $field) use ($title) {
                    $pointer = '/' . str_replace('.', '/', $field);

                    return [
                        'title' => $title,
                        'detail' => $message[0],
                        'source' => [
                            'pointer' => $pointer
                        ]
                    ];
                })->values()
        ];
    }
}
