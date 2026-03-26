<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        // 1. Find the exact book in the database
        $product = Product::findOrFail($id);

        // 2. Retrieve the current cart from the session (or create an empty array if it doesn't exist)
        $cart = session()->get('cart', []);

        // 3. Check if the book is already in the cart
        if (isset($cart[$id])) {
            // If it is, just increase the quantity
            $cart[$id]['quantity']++;
        } else {
            // If it's not, add it to the cart array with the essential details
            $cart[$id] = [
                "title" => $product->title,
                "author" => $product->author,
                "quantity" => 1,
                "price" => $product->price,
                "image_path" => $product->image_path
            ];
        }

        // 4. Save the updated cart back into the session
        session()->put('cart', $cart);

        // 5. Send the user right back to the page they were on with a success message
        return redirect()->back()->with('success', 'Book added to cart successfully!');
    }
    public function remove($id)
    {
        $cart = session()->get('cart');
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return back()->with('success', 'Book removed from order.');
    }
    // Increase quantity by 1
    public function increment($id)
    {
        $cart = session()->get('cart');
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
            session()->put('cart', $cart);
        }
        return back();
    }

    // Decrease quantity by 1
    public function decrement($id)
    {
        $cart = session()->get('cart');
        if(isset($cart[$id])) {
            if($cart[$id]['quantity'] > 1) {
                // If they have more than 1, reduce it
                $cart[$id]['quantity']--;
                session()->put('cart', $cart);
            } else {
                // If it's at 1 and they click minus, remove it entirely
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
        }
        return back();
    }
}