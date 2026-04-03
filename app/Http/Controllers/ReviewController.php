<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:255',
            'body'   => 'required|string|max:5000',
        ]);

        $product->reviews()->create([
            'user_id'              => $request->user()->id,
            'rating'               => $validated['rating'],
            'title'                => $validated['title'] ?? null,
            'body'                 => $validated['body'],
            'status'               => 'pending',
            'is_verified_purchase' => $this->reviewService->isVerifiedPurchase($request->user(), $product),
        ]);

        return back()->with('success', 'Your review has been submitted and is awaiting approval.');
    }
}
