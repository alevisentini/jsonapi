<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Http\Requests\SaveArticleRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
        ->allowedIncludes(['category', 'author'])
        ->sparseFieldset()
        ->firstOrFail();
        
        return ArticleResource::make($article);
    }

    public function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->with('category')
            ->allowedIncludes(['category', 'author'])
            ->allowedFilters(['title', 'content', 'year', 'month', 'categories'])
            ->allowedSorts(['title', 'content'])
            ->sparseFieldset()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function store(SaveArticleRequest $request)
    {
        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function update(SaveArticleRequest $request, Article $article)
    {
        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
