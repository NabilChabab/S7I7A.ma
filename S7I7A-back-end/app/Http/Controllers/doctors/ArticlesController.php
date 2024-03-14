<?php

namespace App\Http\Controllers\doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user()->doctor;

        $articles = $user->articles()->get();

        return response()->json([
            'articles' => ArticleResource::collection($articles),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $doctor = Auth::user()->doctor;

        $requestData = $request->all();
        $requestData['createdBy'] = $doctor->id;

        $article = Article::create($requestData);

        $article->tags()->attach($request->tags);

        $article->addMediaFromRequest('image')->toMediaCollection('media/articles', 'articles_media');

        return response()->json([
            'article' => new ArticleResource($article)
        ]);
    }






    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::findOrFail($id);
        return response()->json([
            'article' => new ArticleResource($article),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, string $id)
    {

        $article = Article::findOrFail($id);
        $article->update($request->all());
        $article->tags()->sync($request->tags);
        if($request->hasFile('image')){
            $article->clearMediaCollection('media/articles');
            $article->addMediaFromRequest('image')->toMediaCollection('media/articles', 'articles_media');
        }
        return response()->json([
            'article' => new ArticleResource($article),
            'message' => 'Article Updated Successfully'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return response()->json([
            'message' => 'Article Deleted Successfully'
        ]);
    }
}
