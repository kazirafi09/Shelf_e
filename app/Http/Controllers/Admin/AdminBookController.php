<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; 
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Validation\ValidationException; // Add this import

class AdminBookController extends Controller
{
    // 1. Show the Books List
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $books = $query->latest()->paginate(10); 
        $globalCategories = Category::all(); 

        return view('admin.books.index', compact('books', 'globalCategories'));
    }

    // 2. Shows the "Add New Book" form
    public function create()
    {
        return view('admin.books.create');
    }

    // 3. Handles the form submission (CREATE)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category'    => 'required|string',
            'stock'       => 'required|integer|min:0',
            'description' => 'required|string',
            // Added dimensions validation to force PHP to parse it as a real image
            'image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // FIX 1.8: Strict Server-Side MIME Type Verification
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw ValidationException::withMessages([
                    'image' => 'The uploaded file must be a genuine image, not a disguised script.'
                ]);
            }

            $imagePath = $file->store('books', 'public');
        }

        Product::create([
            'title'       => $validated['title'],
            'slug'        => Str::slug($validated['title']) . '-' . substr(uniqid(), -5),
            'author'      => $validated['author'],
            'price'       => $validated['price'],
            'category_id' => 1, // Note: You need to update this to use the dynamic category later!
            'stock'       => $validated['stock'],
            'description' => $validated['description'],
            'image'       => $imagePath,
            'rating'      => 0, 
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'New book added successfully!');
    }

    // 4. Show the Edit Form
    public function edit($id)
    {
        $book = Product::findOrFail($id);
        $categories = Category::all(); 
        
        return view('admin.books.edit', compact('book', 'categories'));
    }

    // 5. Save the Updates
    public function update(Request $request, $id)
    {
        $book = Product::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            // Added dimensions validation here too
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100', 
        ]);

        $updateData = [
            'title' => $request->title,
            'author' => $request->author,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'description' => $request->description,
        ];

        if ($book->title !== $request->title) {
            $updateData['slug'] = Str::slug($request->title);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // FIX 1.8: Strict Server-Side MIME Type Verification
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw ValidationException::withMessages([
                    'image' => 'The uploaded file must be a genuine image, not a disguised script.'
                ]);
            }

            if ($book->image_path && Storage::disk('public')->exists($book->image_path)) {
                Storage::disk('public')->delete($book->image_path);
            }
            
            $updateData['image_path'] = $file->store('books', 'public');
        }

        $book->update($updateData);

        return redirect()->route('admin.books.index')->with('success', "Book '{$book->title}' updated successfully!");
    }

    // 6. Delete a book
    public function destroy($id)
    {
        $book = Product::findOrFail($id);
        
        if ($book->image_path && Storage::disk('public')->exists($book->image_path)) {
            Storage::disk('public')->delete($book->image_path);
        }

        $book->delete();

        return back()->with('success', "Book '{$book->title}' has been deleted.");
    }
}