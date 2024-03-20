<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DoctorRessource;
use App\Models\Article;
use App\Models\Category;
use App\Models\Doctors;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctors::latest()->take(4)->get();
        $categories = Category::latest()->take(3)->get();
        $articles = Article::where('status' , 'accepted')->latest()->take(3)->get();

        return response()->json([
            'doctors' => DoctorRessource::collection($doctors),
            'categories' => CategoryResource::collection($categories),
            'articles' => ArticleResource::collection($articles)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function showDoctor(string $id)
    {
        $doctor = Doctors::findOrFail($id);
        return response()->json([
            'doctor' => new DoctorRessource($doctor),
        ]);
    }


}
