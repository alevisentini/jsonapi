<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveCommentRequest;
use App\Models\Article;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::paginate();

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveCommentRequest $request)
    {
        /*$comment = Comment::create([
            'body' => $request->body,
            'article_id' => $request->article_id,
            'user_id' => $request->user_id,
        ]);

        return CommentResource::make($comment);*/

        $comment = new Comment();

        $comment->body = $request->input('data.attributes.body');
        $comment->user_id = $request->getRelationshipId('author');

        $articleSlug = $request->getRelationshipId('article');
        $comment->article_id = Article::whereSlug($articleSlug)->firstOrFail()->id;

        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return CommentResource::make($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->body = $request->input('data.attributes.body');

        if ($request->hasRelationship('article')) {
            $articleSlug = $request->getRelationshipId('article');
            $comment->article_id = Article::whereSlug($articleSlug)->firstOrFail()->id;
        }

        if ($request->hasRelationship('author')) {
            $comment->user_id = $request->getRelationshipId('author');
        }

        $comment->save();

        return CommentResource::make($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
