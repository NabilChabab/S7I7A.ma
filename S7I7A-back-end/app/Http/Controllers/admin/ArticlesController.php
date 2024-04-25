<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        return response()->json([
            'articles' => ArticleResource::collection($articles),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $article->update($request->all());
        return response()->json([
            'article' => new ArticleResource($article),
            'message' => "The article has been updated successfully"
        ]);
    }

    public function show(string $id)
    {
        $article = Article::findOrFail($id);
        return response()->json([
            'article' => new ArticleResource($article),
        ]);
    }

}
