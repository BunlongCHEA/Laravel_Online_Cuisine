@extends('layouts.main')

@section('title', 'Cart')

@section('content')
<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h1 class="mb-0">Your Cart</h1>
        </div>
        <div class="card-body">
            @if (session('cart') && count(session('cart')) > 0)
                <!-- Cart Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach (session('cart') as $id => $item)
                                @php $subtotal = $item['price'] * $item['quantity']; @endphp
                                @php $total += $subtotal; @endphp
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>${{ number_format($item['price'], 2) }}</td>
                                    <td class="text-center">
                                        <!-- Quantity Control Buttons -->
                                        <div class="btn-group">
                                            <button class="btn btn-secondary btn-sm" onclick="updateQuantity('{{ $id }}', -1)">-</button>
                                            <span class="mx-2" id="quantity-{{ $id }}">{{ $item['quantity'] }}</span>
                                            <button class="btn btn-secondary btn-sm" onclick="updateQuantity('{{ $id }}', 1)">+</button>
                                        </div>
                                    </td>
                                    <td>${{ number_format($subtotal, 2) }}</td>
                                    <td class="text-center">
                                        <!-- Remove Item Button -->
                                        <button class="btn btn-danger btn-sm" onclick="removeFromCart('{{ $id }}')">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong>${{ number_format($total, 2) }}</strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admins.cuisines.index') : route('users.cuisines.index') }}" class="btn btn-primary">Back to Menu</a>
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Order Now</button>
                    </form>
                </div>
            @else
                <div class="alert alert-warning text-center" role="alert">
                    Your cart is empty.
                </div>
                <div class="text-center mt-3">
                    <a href="{{ Auth::user()->role === 'admin' ? route('admins.cuisines.index') : route('users.cuisines.index') }}" class="btn btn-primary">Back to Menu</a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Function to update quantity in the cart
    function updateQuantity(id, change) {
        fetch("{{ route('carts.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id: id, change: change })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    // Function to remove item from cart
    function removeFromCart(id) {
        fetch("{{ route('carts.remove') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id: id })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
</script>
@endsection