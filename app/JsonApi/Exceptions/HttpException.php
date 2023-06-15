<?php

namespace App\JsonApi\Exceptions;

use Illuminate\Support\Js;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as BaseHttpException;
use Illuminate\Http\JsonResponse;

class HttpException extends BaseHttpException
{
    public function __construct(BaseHttpException $e)
    {
        parent::__construct($e->getStatusCode(), $e->getMessage(), $e, $e->getHeaders(), $e->getCode());
    }

    public function render($request): JsonResponse
    {
        $detail = method_exists($this, $method = "get{$this->getStatusCode()}Detail")
                    ? $this->{$method}($request)
                    : $this->getMessage();

        return response()->json([
            'errors' => [
                [
                    'title' => Response::$statusTexts[$this->getStatusCode()],
                    'detail' => $detail,
                    'status' => (string) $this->getStatusCode()
                ]
            ]
        ], $this->getStatusCode());
    }

    protected function get404Detail($request): string
    {
        if (str($this->getMessage())->startsWith('No query results for model')) {
            return 'No record found with the ID "' . $request->getResourceId() . '" in the "' . $request->getResourceType() . '" resource';
        }

        return $this->getMessage();
    }

    protected function get403Detail($request): string
    {
        if (str($this->getMessage())->startsWith('This action is unauthorized.')) {
            return 'You are not authorized to access this resource.';
        }

        return $this->getMessage();
    }
}
