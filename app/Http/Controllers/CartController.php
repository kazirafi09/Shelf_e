<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = $request->session()->get('cart', []);

        // Anti-Session Fixation: Regenerate session ID when starting a new cart
        if (empty($cart)) {
            $request->session()->regenerate();
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "title" => $product->title,
                "author" => $product->author,
                "quantity" => 1,
                "price" => $product->price,
                "image_path" => $product->image_path
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Book added to cart successfully!');
    }

    public function remove(Request $request, $id)
    {
        $cart = $request->session()->get('cart');
        
        if(isset($cart[$id])) {
            unset($cart[$id]);
            $request->session()->put('cart', $cart);
        }
        
        return back()->with('success', 'Book removed from order.');
    }

    public function increment(Request $request, $id)
    {
        $cart = $request->session()->get('cart');
        
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
            $request->session()->put('cart', $cart);
        }
        
        return back();
    }

    public function decrement(Request $request, $id)
    {
        $cart = $request->session()->get('cart');
        
        if(isset($cart[$id])) {
            if($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            } else {
                unset($cart[$id]);
            }
            $request->session()->put('cart', $cart);
        }
        
        return back();
    }
}