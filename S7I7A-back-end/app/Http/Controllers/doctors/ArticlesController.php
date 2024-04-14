<?php

namespace App\Http\Controllers\doctors;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class ArticlesController extends Controller
{
    /**
     * Display a listing of the resource.
     */


     /**
      * /**
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 * * @OA\Schema(
 *     schema="Tag",
 *     title="Tag",
 *     description="Category object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )

 * @OA\Get(
 *     path="/api/doctor/articles",
 *     tags={"Articles"},
 *     summary="Retrieve all articles for the authenticated doctor",
 *     description="Retrieve a list of all articles associated with the authenticated doctor.",
 *     security={{ "bearerAuth":{} }},
 *     @OA\Response(
 *         response=200,
 *         description="List of articles",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="articles", type="array", @OA\Items(ref="#/components/schemas/Articles")),
 *             @OA\Property(property="categories", type="array", @OA\Items(ref="#/components/schemas/Category")),
 *             @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/Tag")),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="User is not a doctor",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User is not a doctor."),
 *         ),
 *     ),
 * )
 */
    public function index()
{
    $user = Auth::user();

    if ($user->doctor) {
        $articles = $user->doctor->articles()->get();
        $categories = Category::all();
        $tags = Tag::all();

        return response()->json([
            'articles' => ArticleResource::collection($articles),
            'categories' => $categories,
            'tags' => $tags
        ]);
    } else {
        return response()->json([
            'message' => 'User is not a doctor.'
        ], 403);
    }
}


    /**
     * Store a newly created resource in storage.
     */

     /**
 * @OA\Info(
 *     title="Your API Title",
 *     version="1.0",
 *     description="Description of your API",
 *     @OA\Contact(
 *         email="contact@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Schema(
 *     schema="Articles",
 *     title="Articles",
 *     description="Articles object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="expense", type="number", format="float"),
 *     @OA\Property(property="image", type="string", nullable=true),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 * )
 * @OA\Post(
 *     path="/api/doctor/articles",
 *     tags={"Articles"},
 *     summary="Create a new Article",
 *     description="Create a new article record.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Article data",
 *         @OA\JsonContent(
 *             required={"title", "content", "expense"},
 *             @OA\Property(property="title", type="string", example="Test article"),
 *             @OA\Property(property="content", type="string", example="This is a test article"),
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(property="image", type="string", example="test.jpg"),
 *             @OA\Property(property="tags", type="array", @OA\Items(type="string", example="test")),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Article created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="article", ref="#/components/schemas/Articles"),
 *         ),
 *     ),
 * )
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
     * Update the specified resource in storage.
     */


    public function update(UpdateArticleRequest $request, string $id)
    {

        $article = Article::findOrFail($id);
        $this->authorize('update', $article);
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
        $this->authorize('update', $article);
        $article->delete();
        return response()->json([
            'message' => 'Article Deleted Successfully'
        ]);
    }
}
