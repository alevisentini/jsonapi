<?php

namespace App\JsonApi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ValidateJsonApiDocument
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() === 'POST' || $request->method() === 'PATCH') {
            $request->validate([
                'data' => ['required', 'array'],
                'data.type' => [
                    'required_without:data.0.type', //obligatorio siempre y cuando no exista un type dentro del primer elemento de data
                    'string'
                ],
                'data.attributes' => [
                    Rule::requiredIf(
                        ! Str::of($request->url())->contains('relationships')
                        && request()->isNotFilled('data.0.type')
                    ), 
                    'array'
                ],
            ]);
        }

        if ($request->method() === 'PATCH') {
            $request->validate([
                'data.id' => [
                    'required_without:data.0.id', 
                    'string',
                ]
            ]);
        }

        return $next($request);
    }
}
