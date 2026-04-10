<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        $slug = Str::slug($validated['name']);

        if (Category::where('slug', $slug)->exists()) {
            $error = 'A category with a similar name already exists (duplicate slug).';
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['name' => [$error]]], 422);
            }
            return back()->withErrors(['name' => $error])->withInput();
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['id' => $category->id, 'name' => $category->name], 201);
        }

        return back()->with('success', "Category '{$category->name}' created.");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);

        $slug = Str::slug($validated['name']);

        if (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            return back()->withErrors(['name' => 'A category with a similar name already exists (duplicate slug).'])->withInput();
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return back()->with('success', "Category '{$category->name}' updated.");
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', "Cannot delete '{$category->name}' — it still has books assigned to it.");
        }

        $category->delete();
        return back()->with('success', "Category '{$category->name}' deleted.");
    }
}
