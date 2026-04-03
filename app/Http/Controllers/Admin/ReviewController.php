<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    public function index()
    {
        $pending  = Review::with(['user', 'product'])->where('status', 'pending')->latest()->get();
        $approved = Review::with(['user', 'product'])->where('status', 'approved')->latest()->get();
        $rejected = Review::with(['user', 'product'])->where('status', 'rejected')->latest()->get();

        return view('admin.reviews.index', compact('pending', 'approved', 'rejected'));
    }

    public function approve(Review $review)
    {
        $this->reviewService->approve($review);

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $this->reviewService->reject($review);

        return back()->with('success', 'Review rejected.');
    }
}
