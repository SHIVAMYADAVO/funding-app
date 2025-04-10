<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class CategoryController extends Controller {
    public function index() {
        $categories = Category::all();
        return view('category.index', compact('categories'));
    }

    public function create() {
        return view('category.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName); // Public me store karein
            $imagePath = 'uploads/categories/' . $imageName; // DB me path save karein
        }
    

        Category::create([
            'name' => $request->name,
            'image' => $imagePath,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category) {
        return view('category.edit', compact('category'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
    ]);

    $category = Category::findOrFail($id);
    $category->name = $request->name;

    // Agar nayi image upload hui hai
    if ($request->hasFile('image')) {
        // Pehli image delete karein
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }

        // Nayi image upload karein
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/categories'), $imageName);
        $category->image = 'uploads/categories/' . $imageName; // DB me naya path save karein
    }

    $category->save();

    return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
}

public function destroy(Category $category) {
    // Delete the image if it exists
    if ($category->image) {
        $imagePath = public_path('uploads/categories/' . $category->image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }
    }

    // Delete the category
    $category->delete();

    return redirect()->route('categories.index')->with('success', 'Category deleted successfully!');
}
}

