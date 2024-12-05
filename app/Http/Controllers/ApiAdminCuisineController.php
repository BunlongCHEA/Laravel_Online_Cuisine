<?php

namespace App\Http\Controllers;

use App\Models\Cuisine;
use Illuminate\Http\Request;

class ApiAdminCuisineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cuisine::all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cuisine = Cuisine::findOrFail($id);

        if (!$cuisine) {
            return response()->json(['message' => 'Cuisine not found'], 404);
        }

        return response()->json($cuisine);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
        ]);

        $cuisine = Cuisine::create($request->all());

        return response()->json($cuisine, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cuisine = Cuisine::find($id);
        
        if (!$cuisine) {
            return response()->json(['message', 'Cuisine not found'], 404);
        }

        $cuisine->update($request->all());

        return response()->json($cuisine);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cuisine = Cuisine::findOrFail($id);

        if (!$cuisine) {
            return response()->json(['message', 'Cuisine not found'], 404);
        }

        $cuisine->delete();

        return response()->json(['message' => 'Cuisine deleted successfully']);
    }
}
