<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\RedexArea;
use App\Models\Order;
use App\Models\OrderDetail;
use Exception;

class CheckoutController extends Controller
{
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
            if ($coupon->discount_type === 'fixed') {
                $discount = $coupon->discount_value;
            } elseif ($coupon->discount_type === 'percentage') {
                $discount = ($subtotal * $coupon->discount_value) / 100;
            }
            $discount = min($discount, $subtotal);
        }
        
        return [
            'cart'       => $cart,
            'subtotal'   => $subtotal,
            'discount'   => $discount,
            'coupon'     => $coupon,
        ];
    }

    public function checkout()
    {
        $cartData = $this->getCartData();

        if (count($cartData['cart']) == 0) {
            return redirect()->route('cart.show')->with('error', 'Your cart is empty.');
        }

        $customer = Auth::user()->customer->load('addresses');

        return view('front.checkout.checkout', [
            'cartItems' => $cartData['cart'],
            'subtotal'  => $cartData['subtotal'],
            'discount'  => $cartData['discount'],
            'coupon'    => $cartData['coupon'],
            'addresses' => $customer->addresses,
        ]);
    }

    public function getShippingCharge(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:customer_addresses,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please select a valid address.'], 422);
        }
        
        $customer = Auth::user()->customer;
        $address = $customer->addresses()->findOrFail($request->address_id);
        
        $addressParts = explode(',', $address->address);
        
        if (count($addressParts) < 3) {
            return response()->json(['success' => false, 'message' => 'Address format is incorrect. Please edit the address.'], 400);
        }

        $upazila = trim($addressParts[count($addressParts) - 2]);
        $district = trim($addressParts[count($addressParts) - 1]);

        $area = RedexArea::where('District', $district)->where('Upazila_Thana', $upazila)->first();

        $shippingCharge = $area ? $area->Delivery_Charge : 130; // Default if not found

        return response()->json([
            'success' => true,
            'shipping_charge' => $shippingCharge
        ]);
    }
    
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address_id' => 'required|exists:customer_addresses,id',
            'payment_method'      => 'required|string|in:cod,online_payment',
            'delivery_type'       => 'required|string|in:regular,express',
            'notes'               => 'nullable|string',
            'shipping_cost'       => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $cartData = $this->getCartData();
        if (count($cartData['cart']) == 0) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 400);
        }

        $customer = Auth::user()->customer;
        $shippingAddress = $customer->addresses()->findOrFail($request->shipping_address_id);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'customer_id'      => $customer->id,
                'invoice_no'       => 'INV-' . time() . $customer->id,
                'subtotal'         => $cartData['subtotal'],
                'shipping_cost'    => $request->shipping_cost,
                'discount'         => $cartData['discount'],
                'total_amount'     => ($cartData['subtotal'] - $cartData['discount']) + $request->shipping_cost,
                'status'           => 'Pending',
                'shipping_address' => $shippingAddress->address,
                'billing_address'  => $shippingAddress->address,
                'payment_method'   => $request->payment_method,
                'delivery_type'    => $request->delivery_type,
                'payment_status'   => 'unpaid',
                'notes'            => $request->notes,
            ]);

            // --- START OF FIX ---
            foreach ($cartData['cart'] as $item) {
                if (isset($item['is_bundle']) && $item['is_bundle']) {
                    // This is a bundle product, loop through its selected items
                    foreach ($item['selected_products'] as $bundleProduct) {
                        OrderDetail::create([
                            'order_id'           => $order->id,
                            'product_id'         => $bundleProduct['product_id'],
                            'product_variant_id' => $bundleProduct['variant_id'],
                            'color'              => $bundleProduct['color'],
                            'size'               => $bundleProduct['size'],
                            'quantity'           => $item['quantity'], // Use the main bundle quantity
                            'unit_price'         => $bundleProduct['price'],
                            'subtotal'           => $bundleProduct['price'] * $item['quantity'],
                        ]);
                    }
                } else {
                    // This is a single product
                    OrderDetail::create([
                        'order_id'           => $order->id,
                        'product_id'         => $item['product_id'],
                        'product_variant_id' => $item['variant_id'],
                        'color'              => $item['color'],
                        'size'               => $item['size'],
                        'quantity'           => $item['quantity'],
                        'unit_price'         => $item['price'],
                        'subtotal'           => $item['price'] * $item['quantity'],
                    ]);
                }
            }
            // --- END OF FIX ---
            
            DB::commit();

            Session::forget('cart');
            Session::forget('coupon');

            return response()->json([
                'success' => true, 
                'redirect_url' => route('order.success', ['orderId' => $order->id])
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Order placement failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Could not place order. Please try again.'], 500);
        }
    }
    
    public function orderSuccess($orderId)
    {
        $order = Order::with('customer', 'orderDetails.product')
                      ->where('id', $orderId)
                      ->where('customer_id', Auth::user()->customer->id)
                      ->firstOrFail();

        return view('front.checkout.order_success', compact('order'));
    }
}

