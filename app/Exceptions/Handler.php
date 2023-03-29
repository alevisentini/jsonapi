<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        $title = $exception->getMessage();
        $errors = [];

        // ALTERNATIVE 1
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

        return response()->json([
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
        ], 422);
    }
}
