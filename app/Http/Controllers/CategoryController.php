<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\AuditLog;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(10);

        return view('admins.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new category entry
        $newData = Category::create([
            'name' => $request->name,
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
            'model' => 'Category',
            'data' => json_encode([
                'created' => $newData->toArray(), /// Log all created data as JSON
            ]),
        ]);

        return redirect()->route('admins.categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // return view('admins.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admins.categories.update', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Save the original data before the update for logging
        $originalData = $category->toArray();

        $category->name = $request->name;
        $category->save();

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null, // Log `null` for guest users
            'email' => $user->email ?? 'Guest',
            'ip_address' => $request->ip(),
            'action' => 'Update',
            'url' => $request->fullUrl(),
            'user_agent' => $request->header('User-Agent'),
            'model' => 'Category',
            'data' => json_encode([
                'original' => $originalData, // Before the update
                'updated' => $category->toArray(), // After the update
            ]), // Log all created data as JSON            
        ]);

        return redirect()->route('admins.categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Save the record data before deletion for logging purposes
        $deletedData = $category->toArray();

        $category->delete();

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null, // Log `null` for guest users
            'email' => $user->email ?? 'Guest',
            'ip_address' => request()->ip(),
            'action' => 'Delete',
            'url' => request()->fullUrl(),
            'user_agent' => request()->header('User-Agent'),
            'model' => 'Category',
            'data' => json_encode([
                'deleted' => $deletedData, // Log the data of the deleted record
            ]),          
        ]);

        return redirect()->route('admins.categories.index')->with('success', 'Category deleted successfully.');
    }
}
