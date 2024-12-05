<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Cuisine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
// use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;
use Illuminate\Support\Facades\Storage;

class CuisineController extends Controller
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
        return view('admins.cuisines.index', compact('cuisines', 'categories', 'categoryId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch all categories to display in the dropdown
        $categories = Category::all();

        return view('admins.cuisines.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // 'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Validate image file
            'price' => 'required|numeric|min:0',
        ]);

        // Handle the image upload
        $imagePath = '';
        if ($request->hasFile('image')) {
            // Get the original filename of the uploaded image
            $originalFilename = $request->file('image')->getClientOriginalName();
            // Prepend a timestamp or unique identifier to prevent overwrites
            $uniqueFilename = time() . '_' . $originalFilename;
            // Store the new image with the original filename
            $imagePath = $request->file('image')->storeAs('images', $uniqueFilename, 'public'); // Save with original filename

            // $imagePath = $request->file('image')->store('images', 'public'); // Store image in 'storage/app/public/images'
        }

        // Create a new cuisine entry
        $newData = Cuisine::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image' => $imagePath, // Save image path
            'price' => $request->price,
        ]);

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null, // Log `null` for guest users
            'email' => $user->email ?? 'Guest',
            'ip_address' => $request->ip(),
            'action' => 'Create',
            'url' => $request->fullUrl(),
            'user_agent' => $request->header('User-Agent'),
            'model' => 'Cuisine',
            'data' => json_encode([
                'created' => $newData->toArray(), /// Log all created data as JSON
            ]),
        ]);

        return redirect()->route('admins.cuisines.index')->with('success', 'Cuisine created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cuisine $cuisine)
    {
        // $cuisine = Cuisine::findOrFail($id)
        return view('admins.cuisines.show', compact('cuisine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cuisine $cuisine)
    {
        // Fetch all categories to display in the dropdown
        $categories = Category::all();
        return view('admins.cuisines.update', compact('cuisine', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cuisine $cuisine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0',
        ]);

        // Save the original data before the update for logging
        $originalData = $cuisine->toArray();

        // Hanlde image re-upload if Update
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($cuisine->image_path) {
                Storage::disk('public')->delete($cuisine->image);
            }

            // Get the original filename of the uploaded image
            $originalFilename = $request->file('image')->getClientOriginalName();
            // Prepend a timestamp or unique identifier to prevent overwrites
            $uniqueFilename = time() . '_' . $originalFilename;
            // Store the new image with the original filename
            $cuisine->image = $request->file('image')->storeAs('images', $uniqueFilename, 'public');
        }
        $cuisine->name = $request->name;
        $cuisine->description = $request->description;
        $cuisine->category_id = $request->category_id;
        $cuisine->price = $request->price;
        $cuisine->save();

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null, // Log `null` for guest users
            'email' => $user->email ?? 'Guest',
            'ip_address' => $request->ip(),
            'action' => 'Update',
            'url' => $request->fullUrl(),
            'user_agent' => $request->header('User-Agent'),
            'model' => 'Cuisine',
            'data' => json_encode([
                'original' => $originalData, // Before the update
                'updated' => $cuisine->toArray(), // After the update
            ]), // Log all created data as JSON            
        ]);

        return redirect()->route('admins.cuisines.index')->with('success', 'Cuisine updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cuisine $cuisine)
    {
        // Save the record data before deletion for logging purposes
        $deletedData = $cuisine->toArray();
        
        // Delete the cuisine
        $cuisine->delete();

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null, // Log `null` for guest users
            'email' => $user->email ?? 'Guest',
            'ip_address' => request()->ip(),
            'action' => 'Delete',
            'url' => request()->fullUrl(),
            'user_agent' => request()->header('User-Agent'),
            'model' => 'Cuisine',
            'data' => json_encode([
                'deleted' => $deletedData, // Log the data of the deleted record
            ]),          
        ]);

        return redirect()->route('admins.cuisines.index')->with('success', 'Cuisine deleted successfully.');
    }
}
