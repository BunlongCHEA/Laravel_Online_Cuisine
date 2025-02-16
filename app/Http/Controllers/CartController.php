<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Cuisine;
use App\Models\AuditLog;
// use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display cart items
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = array_sum(array_column($cart, 'subtotal'));

        return view('carts.index', compact('cart', 'total'));
    }

    /**
     * Add an item to the cart
     */
    public function add(Request $request)
    {
        $cuisineId = $request->id;
        $quantity = $request->quantity;
        $cuisine = Cuisine::findOrFail($cuisineId);

        $cart = session()->get('cart', []);

        // Check if the item is already in the cart, then update the quantity
        if (isset($cart[$cuisineId])) {
            $cart[$cuisineId]['quantity'] += $quantity;
        } 
        else {
            // Add new item to the cart
            $cart[$cuisineId] = [
                'id' => $cuisine->id,
                'name' => $cuisine->name,
                'price' => $cuisine->price,
                'quantity' => $quantity,
                // 'category_id' => $cuisine->category_id,
                'subtotal' => $cuisine->price * $quantity,
            ];
        }

        // Update session with cart data
        session()->put('cart', $cart);

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null, // If the user is not authenticated, log null
            'email' => $user->email ?? 'Guest',
            'ip_address' => $request->ip(),
            'action' => 'Add',
            'url' => $request->fullUrl(),
            'user_agent' => $request->header('User-Agent'),
            'model' => 'Cart',
            'data' => json_encode([
                // 'cuisine_id' => $cuisineId,
                // 'cuisine_name' => $cuisine->name,
                // 'price' => $cuisine->price,
                // 'quantity' => $quantity,
                // 'subtotal' => isset($cart[$cuisineId]) ? $cart[$cuisineId]['subtotal'] : 0,
                'added_cart' => $cart, // Log the updated cart for traceability
            ]),
        ]);

        return response()->json(['success' => true]); // redirect()->route('carts.index');
    }

    /**
     * Update quantity in the cart
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $change = $request->change;

        $cart = session()->get('cart', []);

        // Log the current state before updating the quantity
        // $originalQuantity = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        // $originalSubtotal = isset($cart[$id]) ? $cart[$id]['subtotal'] : 0;

        // $change : increases or decreases the quantity of an item in the cart based on - change - parameter
        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $change;

            // If the quantity reaches zero or below, the item is removed from the cart.
            if($cart[$id]['quantity'] <= 0){
                unset($cart[$id]);
            } else {
                // Update the subtotal based on the new quantity
                $cart[$id]['subtotal'] = $cart[$id]['quantity'] * $cart[$id]['price'];
            }
        }

        session()->put('cart', $cart);

        // Log the activity
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id ?? null,
            'email' => $user->email ?? 'Guest',
            'ip_address' => $request->ip(),
            'action' => 'Update',
            'url' => $request->fullUrl(),
            'user_agent' => $request->header('User-Agent'),
            'model' => 'Cart',
            'data' => json_encode([
                // 'cuisine_id' => $id,
                // 'cuisine_name' => $cart[$id]['name'], // $cuisine->name,
                // 'price' => $cart[$id]['price'],  // $cuisine->price,
                // 'quantity_change' => $change,
                // 'original_quantity' => $originalQuantity,
                // 'new_quantity' => $cart[$id]['quantity'] ?? 0,
                // 'original_subtotal' => $originalSubtotal,
                // 'new_subtotal' => $cart[$id]['subtotal'] ?? 0,
                'updated_cart' => $cart
            ]),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove an item from the cart
     */
    public function remove(Request $request) {
        $id = $request->id;
        
        $cart = session()->get('cart', []);

        // Log the state before removing the item
        // $itemExists = isset($cart[$id]);

        if(isset($cart[$id])) {
            // $itemName = $cart[$id]['name'];
            // $itemQuantity = $cart[$id]['quantity'];
            // $itemSubtotal = $cart[$id]['subtotal'];
            // $itemPrice = $cart[$id]['price'];

            // Log the activity
            $user = Auth::user();
            AuditLog::create([
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? 'Guest',
                'ip_address' => $request->ip(),
                'action' => 'Remove',
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent'),
                'model' => 'Cart',
                'data' => json_encode([
                    // 'id' => $id,
                    // 'name' => $itemName,
                    // 'price' => $itemPrice,
                    // 'quantity' => $itemQuantity,
                    // 'subtotal' => $itemSubtotal,
                    'deleted_cart' => $cart
                ]),
            ]);

            // Remove a specific item from the cart entirely based on its id
            unset($cart[$id]);            
        }

        session()->put('cart', $cart);

        return response()->json(['success' => true]);
    }

    /**
     * Clear all the resource in session for cart
     */
    public function clear()
    {
        // session()->forget('cart');
        // return redirect()->route('carts.index');
    }
}
