<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product; // Make sure to import your Product model!
use Illuminate\Support\Str; // We need this to generate the URL slug
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Book;

class AdminBookController extends Controller
{
    // 3. Show the Edit Form
    public function edit($id)
    {
        $book = Product::findOrFail($id);
        $categories = Category::all(); // We need this for the category dropdown
        
        return view('admin.books.edit', compact('book', 'categories'));
    }

    // 4. Save the Updates
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Image is optional on update
        ]);

        // Start building the data array to update
        $updateData = [
            'title' => $request->title,
            'author' => $request->author,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'description' => $request->description,
        ];

        // If the title changed, generate a new slug
        if ($book->title !== $request->title) {
            $updateData['slug'] = Str::slug($request->title);
        }

        // Handle Image Upload if a new one is provided
        if ($request->hasFile('image')) {
            // Delete the old image from storage to save space
            if ($book->image_path && Storage::disk('public')->exists($book->image_path)) {
                Storage::disk('public')->delete($book->image_path);
            }
            // Save the new image
            $updateData['image_path'] = $request->file('image')->store('books', 'public');
        }

        $book->update($updateData);

        return redirect()->route('admin.books.index')->with('success', "Book '{$book->title}' updated successfully!");
    }
    public function index(Request $request)
    {
        // Start the database query
        $query = Book::query();

        // Check if a category was clicked in the UI
        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function($q) use ($request) {
                // Filter books by the requested category slug
                $q->where('slug', $request->category);
            });
        }

        // Get the results (you can chain ->paginate(12) here instead of get())
        $books = $query->get(); 
        
        // Pass everything to the view
        $globalCategories = Category::all(); 

        return view('your.view.name', compact('books', 'globalCategories'));
    }

    // (Your existing create and store methods should still be here!)
    // public function create() { ... }
    // public function store(Request $request) { ... }

    // 2. Delete a book
    public function destroy($id)
    {
        $book = Product::findOrFail($id);
        
        // Optional: Delete the image from storage so it doesn't take up space
        if ($book->image_path && Storage::disk('public')->exists($book->image_path)) {
            Storage::disk('public')->delete($book->image_path);
        }

        $book->delete();

        return back()->with('success', "Book '{$book->title}' has been deleted.");
    }
    // Shows the "Add New Book" form
    public function create()
    {
        return view('admin.books.create');
    }

    // Handles the form submission
    public function store(Request $request)
    {
        // 1. Validate the form data
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category'    => 'required|string',
            'stock'       => 'required|integer|min:0',
            'description' => 'required|string',
            'image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // 2MB max
        ]);

        // 2. Handle the Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            // This saves the file to storage/app/public/books
            $imagePath = $request->file('image')->store('books', 'public');
        }

        // 3. Save to the Database
        // 3. Save to the Database
        Product::create([
            'title'       => $validated['title'],
            'slug'        => Str::slug($validated['title']) . '-' . substr(uniqid(), -5),
            'author'      => $validated['author'],
            'price'       => $validated['price'],
            
            // Temporary fix: Assign a default category_id (e.g., 1) 
            // until we build the dynamic Category system.
            'category_id' => 1, 
            
            'stock'       => $validated['stock'],
            'description' => $validated['description'],
            'image'       => $imagePath,
            'rating'      => 0, 
        ]);

        // 4. Redirect back to the dashboard with a success message
        return redirect()->route('admin.dashboard')->with('success', 'New book added successfully!');
    }
}