<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming checkout request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'payment_method' => 'required|in:cash,card,wallet,valu',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Validate Address
        $address = Address::where('id', $request->address_id)->where('user_id', $user->id)->first();
        if (!$address) {
            return response()->json(['message' => 'Invalid address selected'], 403);
        }

        // Calculate Subtotal
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $subtotal += $item->product->price * $item->quantity;
        }

        // Apply Coupon
        $discountAmount = 0;
        $coupon = null;

        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                return response()->json(['message' => 'Invalid or expired coupon'], 422);
            }
        }

        $shippingCost = 30.00; // Fixed shipping cost for now as per design
        $totalAmount = max(0, $subtotal - $discountAmount) + $shippingCost;

        try {
            DB::beginTransaction();

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'shipping_address_snapshot' => $address->toArray(), // Snapshot
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'coupon_code' => $coupon ? $coupon->code : null,
            ]);

            // Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total' => $item->product->price * $item->quantity,
                ]);
            }

            // Update Coupon Usage
            if ($coupon) {
                $coupon->increment('times_used');
            }

            // Clear Cart
            $cart->items()->delete();
            // Optional: $cart->delete(); if you want to delete the cart container itself, but usually we just empty items.

            DB::commit();

            return response()->json($order->load('items'), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
        }
    }
}
