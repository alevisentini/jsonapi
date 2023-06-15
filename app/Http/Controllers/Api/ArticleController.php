<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', [
            'only' => ['store', 'update', 'destroy'],
        ]);
    }

    public function show($article): JsonResource
    {
        $article = Article::where('slug', $article)
            ->allowedIncludes(['category', 'author', 'comments'])
            ->sparseFieldset()
            ->firstOrFail();

        return ArticleResource::make($article);
    }

    public function index(): AnonymousResourceCollection
    {
        $articles = Article::query()
            // ->with('category')
            ->allowedIncludes(['category', 'author', 'comments'])
            ->allowedFilters(['title', 'content', 'year', 'month', 'categories'])
            ->allowedSorts(['title', 'content'])
            ->sparseFieldset()
            ->jsonPaginate();

        return ArticleResource::collection($articles);
    }

    public function store(SaveArticleRequest $request)
    {
        $this->authorize('create', new Article);

        $articleData = $request->getAttributes();
        $articleData['user_id'] = $request->getRelationshipId('author');

        $categorySlug = $request->getRelationshipId('category');
        $category = Category::where('slug', $categorySlug)->first();
        $articleData['category_id'] = $category->id;

        $article = Article::create($articleData);

        return ArticleResource::make($article);
    }

    public function update(SaveArticleRequest $request, Article $article)
    {
        // This is the code without using Policy:
        // if ($request->user()->id !== $request->route('article')->author->id) {
        //     throw new AccessDeniedHttpException;
        // }

        // This is the code using Policy:
        $this->authorize('update', $article);

        $data = $request->validatedData();

        $articleData = $request->getAttributes();

        if ($request->hasRelationship('author')) {
            $articleData['user_id'] = $request->getRelationshipId('author');
        }

        if ($request->hasRelationship('category')) {
            $categorySlug = $request->getRelationshipId('category');
            $category = Category::where('slug', $categorySlug)->first();
            $articleData['category_id'] = $category->id;
        }

        $article->update($articleData);

        return ArticleResource::make($article);
    }

    public function destroy(Article $article, Request $request): Response
    {
        $this->authorize('delete', $article);

        $article->delete();

        return response()->noContent();
    }
}
