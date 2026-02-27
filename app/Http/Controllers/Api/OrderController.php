<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Mail\OrderPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        if (!$idempotencyKey) {
            return response()->json(['message' => 'Idempotency-Key header is required'], 400);
        }

        // 1. Check if order already exists (Duplicate Prevention)
        $existingOrder = Order::where('idempotency_key', $idempotencyKey)->first();
        if ($existingOrder) {
            return response()->json([
                'message' => 'Order already placed',
                'order_id' => $existingOrder->id,
                'status' => 'already_placed'
            ], 200);
        }

        $request->validate([
            'total_amount' => 'required',
            'shipping_address' => 'required|string',
            'phone_number' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required',
        ]);

        $order = null;

        try {
            if (!$request->user()) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            DB::transaction(function () use ($request, $idempotencyKey, &$order) {
                Log::info('--- Order Process Started ---');
                Log::info('User ID: ' . $request->user()->id . ' | Key: ' . $idempotencyKey);

                $order = $request->user()->orders()->create([
                    'total_amount' => $request->total_amount,
                    'shipping_address' => $request->shipping_address,
                    'phone_number' => $request->phone_number,
                    'payment_method' => $request->payment_method ?? 'cod',
                    'idempotency_key' => $idempotencyKey,
                ]);

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
                Log::info('Order Saved in DB. ID: ' . $order->id);
            });

            // --- EMAIL SECTION ---
            if ($order) {
                try {
                    // Items aur User load karna zaroori hai
                    $order->load(['items.product', 'user']);
                    
                    Log::info("Sending Email to: " . $request->user()->email);
                    
                    Mail::to($request->user()->email)->send(new OrderPlaced($order));
                    
                    Log::info("✅ Email sent successfully for Order ID: " . $order->id);
                } catch (\Exception $mailError) {
                    // Agar sirf email fail ho toh order cancel nahi hoga
                    Log::error("❌ Email Failed for Order {$order->id}: " . $mailError->getMessage());
                }
            }

            return response()->json([
                'message' => 'Order placed successfully!',
                'order_id' => $order->id
            ], 201);

        } catch (\Exception $e) {
            Log::error('🔥 Fatal Order Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        // Yahan items aur product dono load kar rahe hain taake Flutter list mein sab dikhe
        $orders = $request->user()->orders()->with('items.product')->latest()->get();
        return response()->json($orders);
    }
}