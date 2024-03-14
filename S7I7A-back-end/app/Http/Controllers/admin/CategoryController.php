<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $deletedCategories = Category::onlyTrashed()->get();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
            'deletedCat' => CategoryResource::collection($deletedCategories)
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'icon' => 'required|image'
        ]);
        $category = Category::create($request->all());
        $category->addMediaFromRequest('icon')->toMediaCollection('media/categories' , 'categories_media');
        return response()->json([
            'category' => $category,
            'message' => 'Category Created Successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json([
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'nullable',
            'icon' => 'nullable',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());
        if($request->hasFile('icon')){
            $category->clearMediaCollection('media/categories');
            $category->addMediaFromRequest('icon')->toMediaCollection('media/categories', 'categories_media');
        }
        return response()->json([
            'category' => $category,
            'message' => 'Category updated successfully!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json([
         'message' => 'Category deleted successfully!'
        ], 200);
    }

    public function restore($id)
    {
        $patient = Category::onlyTrashed()->where('id', $id)->firstOrFail();

        $patient->restore();

        return response()->json([
            'message' => 'Patient restored successfully',
        ]);
    }
}
