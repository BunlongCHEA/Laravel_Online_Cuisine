<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cuisine;
use App\Models\Category;

class UserCuisineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch all categories for the dropdown
        $categories = Category::all();
        // Query cuisines with optional category filtering
        $query = Cuisine::with('category');

        $categoryId = $request->category_id;
        
        // if ($categoryId) : checks whether the category_id exists and is not empty. 
        // If the user selects "All Categories" (i.e., no category_id is provided), this condition evaluates to false, and the query does not apply the where clause, ensuring all cuisines are shown.
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $cuisines = $query->orderBy('id', 'desc')->paginate(10)->appends(['category_id' => $categoryId]);

        // return view('cuisines.index', ["cuisines", $cuisines]);
        return view('users.cuisines.index', compact('cuisines', 'categories', 'categoryId'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Cuisine $cuisine)
    {
        // $cuisine = Cuisine::findOrFail($id)
        return view('users.cuisines.show', compact('cuisine'));
    }
}
