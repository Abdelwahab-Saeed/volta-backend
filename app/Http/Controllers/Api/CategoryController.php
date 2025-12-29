<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET ALL
    public function index()
    {
        return response()->json(
            Category::latest()->get()
        );
    }

    // CREATE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    // SHOW
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // UPDATE
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $category->update($data);

        return response()->json($category);
    }

    // DELETE
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}

