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
    // 1. Show the Books List
    public function index(Request $request)
    {
        $query = Product::query();

        // Keep your existing Category Filter
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // NEW: Add the Keyword Search logic (Fixes D-003)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('slug', 'LIKE', "%{$searchTerm}%"); // Bonus: search by slug too
            });
        }

        // Add withQueryString() so pagination links remember the search term
        $books = $query->latest()->paginate(10)->withQueryString(); 
        
        $globalCategories = Category::all(); 

        return view('admin.books.index', compact('books', 'globalCategories'));
    }

    // 2. Shows the "Add New Book" form
    // 2. Shows the "Add New Book" form
    public function create()
    {
        // FIX: Fetch the categories so the Blade view doesn't crash
        $categories = Category::orderBy('name')->get();
        
        return view('admin.books.create', compact('categories'));
    }

    // 3. Handles the form submission (CREATE)
    // 3. Handles the form submission (CREATE)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            // FIX A-3: Expect category_id and verify it exists in the categories table
            'category_id' => 'required|integer|exists:categories,id', 
            // FIX A-3: Expect 'stock_quantity' instead of 'stock' to match your update() method and DB
            'stock_quantity' => 'required|integer|min:0', 
            'description' => 'required|string',
            'synopsis'    => 'nullable|string',
            'image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100', 
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw ValidationException::withMessages([
                    'image' => 'The uploaded file must be a genuine image, not a disguised script.'
                ]);
            }

            $imagePath = $file->store('books', 'public');
        }

        Product::create([
            'title'          => $validated['title'],
            'slug'           => Str::slug($validated['title']) . '-' . substr(uniqid(), -5),
            'author'         => $validated['author'],
            'price'          => $validated['price'],
            // FIX A-3: Actually use the validated category_id from the form
            'category_id'    => $validated['category_id'], 

            // FIX A-3: Use the correct column name
            'stock_quantity' => $validated['stock_quantity'], 
            'description'    => $validated['description'],
            'synopsis'       => $validated['synopsis'] ?? null,
            // Match the column name from your update method (usually image_path, not image)
            'image_path'     => $imagePath, 
            'rating'         => 0, 
        ]);

        return redirect()->route('admin.books.index')->with('success', 'New book added successfully!');
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
            'synopsis'    => 'nullable|string',
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
            'synopsis'       => $request->synopsis,
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