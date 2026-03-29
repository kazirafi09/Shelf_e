<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // 1. Show the user's wishlist page
    public function index()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('wishlists.index', compact('wishlists'));
    }

    // 2. Toggle item in/out of wishlist
    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
                            ->where('product_id', $product->id)
                            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return back()->with('success', 'Removed from your wishlist.');
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id
            ]);
            return back()->with('success', 'Added to your wishlist!');
        }
    }
}