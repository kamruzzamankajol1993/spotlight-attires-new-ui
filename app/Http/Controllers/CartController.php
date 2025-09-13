<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{

     /**
     * Add a bundle product to the cart.
     */
    public function addBundleToCart(Request $request)
    {
        $request->validate([
            'bundleId' => 'required|exists:bundle_offer_product,id',
            'quantity' => 'required|integer|min:1',
            'selectedProducts' => 'required|array',
            'selectedProducts.*.id' => 'required|exists:products,id',
            'selectedProducts.*.variantId' => 'required|exists:product_variants,id',
            'selectedProducts.*.size' => 'required|string',
        ]);

        $bundle = \App\Models\BundleOfferProduct::findOrFail($request->bundleId);

        // Create a unique ID for the bundle cart item.
        $cartItemId = 'bundle-' . $bundle->id;
        $cart = Session::get('cart', []);

        // Get an image for the bundle from the first selected product
        $firstProductImage = !empty($request->selectedProducts) ? $request->selectedProducts[0]['image'] : null;

        // Structure the sub-items (the actual products chosen)
        $selectedProductsDetails = [];
        foreach ($request->selectedProducts as $productData) {
            if ($productData) { // Ensure the slot is not empty
                $selectedProductsDetails[] = [
                    'product_id' => $productData['id'],
                    'variant_id' => $productData['variantId'],
                    'name'       => $productData['name'],
                    'size'       => $productData['size'],
                    'color'      => $productData['color'],
                    'image'      => $productData['image'],
                    'price'      => $productData['finalPrice'], // Per-item price for display
                ];
            }
        }

        // If the bundle already exists in the cart, just update its quantity.
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $request->quantity;
        } else {
            // Otherwise, add it as a new item.
            $cart[$cartItemId] = [
                'rowId'             => $cartItemId,
                'id'                => $bundle->id,
                'name'              => $bundle->title,
                'quantity'          => $request->quantity,
                'price'             => $bundle->discount_price, // The total price for the bundle
                'image'             => $firstProductImage,
                'url'               => route('offerProduct.show', $bundle->id), // Link back to the offer page
                'is_bundle'         => true,
                'selected_products' => $selectedProductsDetails,
            ];
        }

        Session::put('cart', $cart);

     


        return response()->json([
            'success' => true,
            'message' => 'Bundle added to cart successfully!',
        ]);
    }


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
        $cartItemId = $variant->id . '-' . str_replace(' ', '', $request->size);
        $cart = Session::get('cart', []);
        $basePrice = $product->discount_price ?? $product->base_price;
        $finalPrice = $basePrice + ($variant->additional_price ?? 0);
        $image = $variant->variant_image[0] ?? $product->thumbnail_image[0] ?? null;

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
                'is_bundle'  => false,
                'url'        => route('product.show', $product->slug)
            ];
        }
        Session::put('cart', $cart);

        $response = $this->getCartResponse();
        $responseData = $response->getData(true);
        $responseData['success'] = true;
        $responseData['message'] = 'Product added to cart!';

        return response()->json($responseData);
    }
 private function getCartResponse()
    {
        $data = $this->getCartData();
        
        return response()->json([
            'sidebar_html'   => view('front.include.cart_items_partial', ['cart' => $data['cart']])->render(),
            'main_cart_html' => view('front.include.main_cart_items_partial', ['cart' => $data['cart']])->render(),
            'count'          => count($data['cart']),
            'subtotal'       => number_format($data['subtotal'], 2),
            'discount'       => number_format($data['discount'], 2),
            'total'          => number_format($data['total'], 2),
            'coupon'         => $data['coupon'],
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
/**
     * A private helper function to get all cart data in a consistent format.
     */
     private function getCartData()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $coupon = Session::get('coupon');
        $discount = 0;
        
        if ($coupon) {
            $eligibleSubtotal = $subtotal;

            if (!empty($coupon->product_ids) || !empty($coupon->category_ids)) {
                $eligibleSubtotal = 0;
                $productIdsInCart = collect($cart)->where('is_bundle', false)->pluck('product_id')->unique()->all();
                
                if(!empty($productIdsInCart)){
                    $products = Product::whereIn('id', $productIdsInCart)->get()->keyBy('id');
                    foreach ($cart as $item) {
                        if (isset($item['is_bundle']) && !$item['is_bundle'] && isset($products[$item['product_id']])) {
                            $product = $products[$item['product_id']];
                            $isProductEligible = !empty($coupon->product_ids) && in_array($product->id, $coupon->product_ids);
                            $isCategoryEligible = !empty($coupon->category_ids) && in_array($product->category_id, $coupon->category_ids);
                            
                            if ($isProductEligible || $isCategoryEligible) {
                                $eligibleSubtotal += $item['price'] * $item['quantity'];
                            }
                        }
                    }
                }
            }
            
            if ($coupon->type === 'fixed') {
                $discount = $coupon->value;
            } elseif ($coupon->type === 'percentage') {
                $discount = ($eligibleSubtotal * $coupon->value) / 100;
            }
            
            $discount = min($discount, $eligibleSubtotal);
        }
        
        $total = $subtotal - $discount;

        return [
            'cart'       => $cart,
            'subtotal'   => $subtotal,
            'discount'   => $discount,
            'total'      => $total,
            'coupon'     => $coupon,
        ];
    }

public function getMainCartContent()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $coupon = Session::get('coupon');
        $discount = 0;
        $total = $subtotal;

        if ($coupon) {
            if ($coupon->type === 'fixed') {
                $discount = $coupon->value;
            } elseif ($coupon->type === 'percentage') {
                $discount = ($subtotal * $coupon->value) / 100;
            }
        }
        
        $total = $subtotal - $discount;

        $cartHtml = view('front.include.main_cart_items_partial', ['cart' => $cart])->render();

        return response()->json([
            'html' => $cartHtml,
            'count' => count($cart),
            'subtotal' => number_format($subtotal, 2),
            'discount' => number_format($discount, 2),
            'total' => number_format($total, 2),
            'coupon' => $coupon, // Send coupon details to the frontend
        ]);
    }
 
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);
        
        $coupon = Coupon::where('code', $request->coupon_code)
                        ->where('status', true)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon code.'], 404);
        }

        if ($coupon->usage_limit !== null && $coupon->times_used >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => 'This coupon has reached its usage limit.'], 422);
        }

        $cartData = $this->getCartData();

        if ($coupon->min_amount !== null && $cartData['subtotal'] < $coupon->min_amount) {
            return response()->json(['success' => false, 'message' => "You must spend at least ৳{$coupon->min_amount} to use this coupon."], 422);
        }

        if (Auth::check() && $coupon->user_type !== 'all') {
            $orderCount = Auth::user()->customer->orders()->count();
            if ($coupon->user_type === 'new_user' && $orderCount > 0) {
                return response()->json(['success' => false, 'message' => 'This coupon is for new customers only.'], 422);
            }
            if ($coupon->user_type === 'existing_user' && $orderCount === 0) {
                return response()->json(['success' => false, 'message' => 'This coupon is for existing customers only.'], 422);
            }
        } elseif (!Auth::check() && $coupon->user_type !== 'all') {
             return response()->json(['success' => false, 'message' => 'You must be logged in to use this coupon.'], 401);
        }

        $eligibleItemsFound = false;
        if (empty($coupon->product_ids) && empty($coupon->category_ids)) {
            $eligibleItemsFound = true; // Coupon applies to all items if no restrictions are set
        } else {
            $productIdsInCart = collect($cartData['cart'])->where('is_bundle', false)->pluck('product_id')->unique()->all();
            if(!empty($productIdsInCart)){
                $products = Product::whereIn('id', $productIdsInCart)->get()->keyBy('id');
                foreach ($cartData['cart'] as $item) {
                     if (isset($item['is_bundle']) && !$item['is_bundle'] && isset($products[$item['product_id']])) {
                        $product = $products[$item['product_id']];
                        if ((!empty($coupon->product_ids) && in_array($product->id, $coupon->product_ids)) || 
                            (!empty($coupon->category_ids) && in_array($product->category_id, $coupon->category_ids))) {
                            $eligibleItemsFound = true;
                            break;
                        }
                    }
                }
            }
        }
        
        if (!$eligibleItemsFound) {
            return response()->json(['success' => false, 'message' => 'This coupon is not valid for the items in your cart.'], 422);
        }
        
        Session::put('coupon', $coupon);

        $response = $this->getMainCartContent();
        $responseData = $response->getData(true);
        $responseData['success'] = true;
        $responseData['message'] = 'Coupon applied successfully!';

        return response()->json($responseData);
    }

    /**
     * Remove the applied coupon from the cart.
     */
    public function removeCoupon()
    {
        Session::forget('coupon');
        
        $response = $this->getMainCartContent();
        $responseData = $response->getData(true);
        $responseData['success'] = true;
        $responseData['message'] = 'Coupon removed.';

        return response()->json($responseData);
    }
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