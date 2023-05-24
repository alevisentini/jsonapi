<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Resources\AuthorResource;

class ArticleAuthorController extends Controller
{
    public function index(Article $article)
    {
        return AuthorResource::identifier($article->author);
    }

    public function show(Article $article)
    {
        return AuthorResource::make($article->author);
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'data.id' => ['required', 'exists:users,id']
        ]);

        $userId = $request->input('data.id');
        
        $article->update(['user_id' => $userId]);

        return AuthorResource::identifier($article->author);
    }
}
