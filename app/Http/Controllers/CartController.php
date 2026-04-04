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

        if ($product->stock_quantity === 0) {
            return redirect()->back()->with('error', 'Sorry, this book is out of stock.');
        }

        $format = $request->input('format');
        $itemPrice = $format === 'paperback' ? $product->paperback_price : $product->hardcover_price;

        if (is_null($itemPrice)) {
            return redirect()->back()->with('error', 'The selected format is currently unavailable for this book.');
        }

        $requested = max(1, (int) $request->input('quantity', 1));

        if (isset($cart[$id])) {
            $newQuantity = $cart[$id]['quantity'] + $requested;
            if ($newQuantity > $product->stock_quantity) {
                return redirect()->back()->with('error', 'Sorry, not enough stock available.');
            }
            $cart[$id]['quantity'] = $newQuantity;
        } else {
            if ($requested > $product->stock_quantity) {
                return redirect()->back()->with('error', 'Sorry, not enough stock available.');
            }

            $cart[$id] = [
                "title"      => $product->title,
                "author"     => $product->author,
                "quantity"   => $requested,
                "price"      => $itemPrice,
                "image_path" => $product->image_path,
            ];
        }

        $request->session()->put('cart', $cart);

        if ($request->input('action_type') === 'buy_now') {
            return redirect()->route('checkout.index');
        }

        return redirect()->back()->with('success', 'Book added to cart successfully!');
    }

    public function remove(Request $request, $id)
    {
        $cart = $request->session()->get('cart');

        if (isset($cart[$id])) {
            unset($cart[$id]);
            $request->session()->put('cart', $cart);
        }

        return back()->with('success', 'Book removed from order.');
    }

    public function increment(Request $request, $id)
    {
        $cart = $request->session()->get('cart');

        if (isset($cart[$id])) {
            $product = Product::findOrFail($id);
            $newQuantity = $cart[$id]['quantity'] + 1;

            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', 'Sorry, not enough stock available.');
            }

            $cart[$id]['quantity'] = $newQuantity;
            $request->session()->put('cart', $cart);
        }

        return back();
    }

    public function decrement(Request $request, $id)
    {
        $cart = $request->session()->get('cart');

        if (isset($cart[$id])) {
            if ($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            } else {
                unset($cart[$id]);
            }
            $request->session()->put('cart', $cart);
        }

        return back();
    }
}
