<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\DoctorRessource;
use App\Http\Resources\PatientRessource;
use App\Http\Resources\UserRessource;
use App\Models\Article;
use App\Models\Category;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::latest()->take(4)->get();
        $categories = Category::all();
        $all_categories = Category::all();
        $articles = Article::where('status' , 'accepted')->latest()->take(3)->get();
        $all_doctors = Doctor::all();
        $all_articles = Article::where('status' , 'accepted')->get();

        return response()->json([
            'doctors' => DoctorRessource::collection($doctors),
            'categories' => CategoryResource::collection($categories),
            'articles' => ArticleResource::collection($articles),
            'all_doctors' => DoctorRessource::collection($all_doctors),
            'all_articles' => ArticleResource::collection($all_articles),
            'all_categories' => CategoryResource::collection($all_categories),
        ]);
    }

    public function counts(){

    }

    public function updateProfile(UpdateUserRequest $request, $id)
{

        $user = User::findOrFail($id);
        $user->update($request->all());

        if ($request->hasFile('profile')) {
            $user->clearMediaCollection('media/patients');
            $user->addMediaFromRequest('profile')->toMediaCollection('media/patients', 'patients_media');
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);

}

    /**
     * Display the specified resource.
     */
    public function showDoctor(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        return response()->json([
            'doctor' => new DoctorRessource($doctor),
        ]);
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json(['patient' => new PatientRessource($user)]);
    }


    public function showDoctorByCategory($id){
        $category = Category::findOrFail($id);

        $doctors = Doctor::where('category_id', $category->id)->get();

        return response()->json([
            'doctorByCategory' => DoctorRessource::collection($doctors),
        ]);

    }


}
