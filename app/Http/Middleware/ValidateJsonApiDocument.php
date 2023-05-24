<?php

namespace App\Http\Middleware;

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
                'data.type' => ['required', 'string'],
                'data.attributes' => [
                    Rule::requiredIf(
                        ! Str::of($request->url())->contains('relationships')
                    ), 
                    'array'
                ],
            ]);
        }

        if ($request->method() === 'PATCH') {
            $request->validate([
                'data.id' => ['required', 'string']
            ]);
        }

        return $next($request);
    }
}
