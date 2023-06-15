<?php

namespace App\JsonApi\Exceptions;

use App\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\JsonApi\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        $this->renderable(function (HttpException $e, Request $request) {
            $request->isJsonApi() && throw new \App\JsonApi\Exceptions\HttpException($e);
        })->renderable(function (AuthenticationException $e, Request $request) {
            $request->isJsonApi() && throw new \App\JsonApi\Exceptions\AuthenticationException();
        });

        parent::register();
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return $request->isJsonApi()
                ? new JsonApiValidationErrorResponse($exception)
                : parent::invalidJson($request, $exception);
    }
}
