<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // NEW: Add the Keyword Search logic (Fixes D-003)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('slug', 'LIKE', "%{$searchTerm}%"); // Bonus: search by slug too
            });
        }

        // Add withQueryString() so pagination links remember the search term
        $books = $query->latest()->with('category')->paginate(10)->withQueryString();

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
            'title'           => 'required|string|max:255|unique:products,title',
            'author'          => 'required|string|max:255',
            'paperback_price' => 'nullable|numeric|min:0',
            'hardcover_price' => 'nullable|numeric|min:0',
            // At least one price format must be provided
            'sale_price'      => 'nullable|numeric|min:0',
            'sale_ends_at'    => 'nullable|date|after:now',
            'category_id'     => 'required|integer|exists:categories,id',
            'stock_quantity'  => 'required|integer|min:0',
            'description'     => 'required|string',
            'synopsis'        => 'nullable|string',
            'image'           => 'required|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
        ], [
            'paperback_price.required' => 'Please provide at least one price (Paperback or Hardcover).',
            'hardcover_price.required' => 'Please provide at least one price (Paperback or Hardcover).',
        ]);

        // Validate that at least one price is provided
        if (empty($validated['paperback_price']) && empty($validated['hardcover_price'])) {
            throw ValidationException::withMessages([
                'paperback_price' => 'Please provide a price for at least one format (Paperback or Hardcover).',
            ]);
        }

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
            'title'           => $validated['title'],
            'slug'            => Str::slug($validated['title']) . '-' . substr(uniqid(), -5),
            'author'          => $validated['author'],
            'paperback_price' => $validated['paperback_price'] ?? null,
            'hardcover_price' => $validated['hardcover_price'] ?? null,
            'sale_price'      => $validated['sale_price'] ?? null,
            'sale_ends_at'    => $validated['sale_ends_at'] ?? null,
            'category_id'     => $validated['category_id'],
            'stock_quantity'  => $validated['stock_quantity'],
            'description'     => $validated['description'],
            'synopsis'        => $validated['synopsis'] ?? null,
            'image_path'      => $imagePath,
            'rating'          => 0,
        ]);

        Cache::forget('homepage_data_v4');

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
            'title'           => 'required|string|max:255|unique:products,title,' . $id,
            'author'          => 'required|string|max:255',
            'category_id'     => 'required|integer|exists:categories,id',
            'paperback_price' => 'nullable|numeric|min:0',
            'hardcover_price' => 'nullable|numeric|min:0',
            'sale_price'      => 'nullable|numeric|min:0',
            'sale_ends_at'    => 'nullable|date',
            'stock_quantity'  => 'required|integer|min:0',
            'description'     => 'nullable|string',
            'synopsis'        => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
        ]);

        // Validate that at least one price is provided
        if (empty($request->paperback_price) && empty($request->hardcover_price)) {
            throw ValidationException::withMessages([
                'paperback_price' => 'Please provide a price for at least one format (Paperback or Hardcover).',
            ]);
        }

        $updateData = [
            'title'           => $request->title,
            'author'          => $request->author,
            'category_id'     => $request->category_id,
            'paperback_price' => $request->paperback_price ?? null,
            'hardcover_price' => $request->hardcover_price ?? null,
            'sale_price'      => $request->sale_price ?: null,
            'sale_ends_at'    => $request->sale_ends_at ?: null,
            'stock_quantity'  => $request->stock_quantity,
            'description'     => $request->description,
            'synopsis'        => $request->synopsis,
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

        Cache::forget('homepage_data_v4');

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

        Cache::forget('homepage_data_v4');

        return back()->with('success', "Book '{$book->title}' has been deleted.");
    }
}
