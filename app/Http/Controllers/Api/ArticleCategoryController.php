<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class ArticleCategoryController extends Controller
{
    public function index(Article $article)
    {
        return CategoryResource::identifier($article->category);
    }

    public function show(Article $article)
    {
        return CategoryResource::make($article->category);
    }

    public function update(Request $request, Article $article)
    {
        $request->validate([
            'data.id' => ['required', 'exists:categories,slug']
        ]);
        
        $categorySlug = $request->input('data.id');
        
        $category = Category::where('slug', $categorySlug)->first();
        
        $article->update(['category_id' => $category->id]);

        return CategoryResource::identifier($article->category);
    }
}
