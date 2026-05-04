<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::find($item_id);
        $address = auth()->user()->addresses()->first();

        return view('purchase.show', [
            'item' => $item,
            'address' => $address,
        ]);
    }

    public function store(Request $request, $item_id)
    {
        \Log::info('Purchase attempt for item: ' . $item_id);
        \Log::info('Request data: ' . json_encode($request->all()));

        $item = Item::find($item_id);

        if (!$item) {
            \Log::error('Item not found: ' . $item_id);
            return redirect()->route('items.index')->with('error', '商品が見つかりません');
        }

        \Log::info('Item found: ' . $item->name);
        \Log::info('Creating order with seller_id: ' . $item->seller_id . ', buyer_id: ' . auth()->id());

        try {
            Order::create([
                'item_id' => $item_id,
                'buyer_id' => auth()->id(),
                'seller_id' => $item->seller_id,
                'address_id' => $request->input('address_id'),
                'payment_method' => $request->input('payment_method'),
                'status' => 'pending',
            ]);

            \Log::info('Order created successfully');

            $item->update(['status' => 'sold']);

            \Log::info('Item status updated to sold');
        } catch (\Exception $e) {
            \Log::error('Error creating order: ' . $e->getMessage());
            return redirect()->route('items.index')->with('error', $e->getMessage());
        }

        return redirect()->route('items.index');
    }
}
