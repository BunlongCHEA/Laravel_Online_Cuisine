<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

use App\Models\Order;

class OrderController extends Controller
{
    public function store() {
        // Retrieve the cart from the session
        $cart = session()->get('cart', []);

        // Save each item in the cart to the orders table
        foreach($cart as $item) {
            Order::create([
                'cuisine_id' => $item['id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
                'userOrder' => Auth::id() // Get the logged-in user's ID
            ]);
        }

        session()->forget('cart');
        return redirect()->route('orders.complete');
    }

    public function complete() {
        return view('orders.complete');
    }
}
