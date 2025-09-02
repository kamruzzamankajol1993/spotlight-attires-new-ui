<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{


     public function showCartData()
    {
        // 2. FETCH 4 RANDOM, ACTIVE PRODUCTS
        $randomProducts = Product::where('status', 1) // Optional: only show active products
                                 ->inRandomOrder()
                                 ->limit(4)
                                 ->get();

        // 3. PASS THE PRODUCTS TO THE VIEW
        return view('front.cart.cart', compact('randomProducts'));
    }
    /**
     * Add a product to the cart.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'productId' => 'required|exists:products,id',
            'variantId' => 'required|exists:product_variants,id',
            'size' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->productId);
        $variant = ProductVariant::with('color')->findOrFail($request->variantId);

        // Create a unique ID for the cart item based on variant and size
        $cartItemId = $variant->id . '-' . str_replace(' ', '', $request->size);
        $cart = Session::get('cart', []);

        // Determine price
        $basePrice = $product->discount_price ?? $product->base_price;
        $finalPrice = $basePrice + ($variant->additional_price ?? 0);

        // Determine image from variant or fallback to product thumbnail
        $image = $variant->variant_image[0] ?? $product->thumbnail_image[0] ?? null;

        // If item already exists, update quantity. Otherwise, add a new item.
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            $cart[$cartItemId] = [
                'rowId' => $cartItemId,
                'product_id' => $product->id,
                'variant_id' => $variant->id,
                'name' => $product->name,
                'size' => $request->size,
                'color' => $variant->color->name ?? 'N/A',
                'quantity' => $request->quantity,
                'price' => $finalPrice,
                'image' => $image,
                'slug' => $product->slug,
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
        ]);
    }

    /**
     * Get the contents of the cart.
     */
    public function getCartContent()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $cartHtml = view('front.include.cart_items_partial', ['cart' => $cart])->render();

        return response()->json([
            'html' => $cartHtml,
            'count' => count($cart),
            'subtotal' => number_format($subtotal, 2)
        ]);
    }

    /**
     * Update an item's quantity in the cart.
     */
    public function updateCartItem(Request $request)
    {
        $request->validate([
            'rowId' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$request->rowId])) {
            $cart[$request->rowId]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
        }

        return $this->getCartContent();
    }

    /**
     * Remove an item from the cart.
     */
    public function removeCartItem(Request $request)
    {

        //dd(123);
        $request->validate(['rowId' => 'required|string']);
        $cart = Session::get('cart', []);

        if (isset($cart[$request->rowId])) {
            unset($cart[$request->rowId]);
            Session::put('cart', $cart);
        }

        return $this->getCartContent();
    }


public function getMainCartContent()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Renders the NEW MAIN cart partial
        $cartHtml = view('front.include.main_cart_items_partial', ['cart' => $cart])->render();

        return response()->json([
            'html' => $cartHtml,
            'count' => count($cart),
            'subtotal' => number_format($subtotal, 2)
        ]);
    }
     // ===================================================
    // =========== NEW METHODS FOR MAIN CART =============
    // ===================================================

    /**
     * Update an item's quantity for the MAIN cart page.
     */
    public function updateMainCartItem(Request $request)
    {
        $request->validate([
            'rowId' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$request->rowId])) {
            $cart[$request->rowId]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
        }
        // Returns MAIN cart partial
        return $this->getMainCartContent();
    }

    /**
     * Remove an item for the MAIN cart page.
     */
    public function removeMainCartItem(Request $request)
    {
        $request->validate(['rowId' => 'required|string']);
        $cart = Session::get('cart', []);

        if (isset($cart[$request->rowId])) {
            unset($cart[$request->rowId]);
            Session::put('cart', $cart);
        }
        // Returns MAIN cart partial
        return $this->getMainCartContent();
    }
}