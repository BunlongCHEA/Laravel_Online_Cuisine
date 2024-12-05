@extends('layouts.main')

@section('title', 'Cuisine Menu')

@section('content')
<div class="container mt-4">
    <!-- User Profile Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Welcome, {{ Auth::user()->name }}</h4>
            <p>Email: {{ Auth::user()->email }}</p>
            <a href="{{ route('profiles.edit') }}" class="btn btn-info">Edit Profile</a>
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>

    <h1>All Cuisines</h1>

    <div class="d-flex justify-content-end mb-3">
        <div style="margin: 10px;">
            <!-- View Cart Button -->
            <a href="{{ route('carts.index') }}" class="btn btn-info">View Cart</a>
        </div>

        <div style="margin: 10px;">
            <!-- Category Filter Dropdown -->
            <form action="{{ route('users.cuisines.index') }}" method="GET" class="form-inline">
                <select name="category_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

    </div>

    <!-- Cuisine Cards -->
    <div class="row">
        @foreach ($cuisines as $cuisine)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="{{ $cuisine->image ? asset('storage/'.$cuisine->image) : '' }}" 
                         class="card-img-top" alt="{{ $cuisine->name }}" style="max-height: 200px; object-fit: cover;">

                    <div class="card-body">
                        <h5 class="card-title">{{ $cuisine->name }}</h5>
                        <p class="card-text">
                            {{ Str::limit($cuisine->description, 150) }}
                            @if (Str::length($cuisine->description) > 150)
                                <a href="{{ route('users.cuisines.show', $cuisine->id) }}" class="text-primary">Read more</a>
                            @endif
                        </p>
                        <p class="card-text"><strong>Price:</strong> ${{ $cuisine->price }}</p>
                        <p class="card-text"><strong>Category:</strong> {{ $cuisine->category->name ?? 'Uncategorized' }}</p>

                        <!-- Quantity Selector -->
                        <div class="d-flex align-items-center mb-3">
                            <button class="btn btn-secondary btn-sm quantity-btn" data-action="decrease" data-id="{{ $cuisine->id }}">-</button>
                            <span class="mx-2" id="quantity-{{ $cuisine->id }}">0</span>
                            <button class="btn btn-secondary btn-sm quantity-btn" data-action="increase" data-id="{{ $cuisine->id }}">+</button>
                        </div>

                        <!-- Add to Cart Button -->
                        <!-- <a href="{{ route('carts.index') }}" id="add-to-cart-{{ $cuisine->id }}" class="btn btn-primary add-to-cart d-none" data-id="{{ $cuisine->id }}">
                            Add to Cart
                        </a> -->
                    </div>

                    <div class="card-footer">
                        <a href="{{ route('users.cuisines.show', $cuisine->id) }}" class="btn btn-info btn-sm">Show</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Bottom Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $cuisines->links('pagination::bootstrap-4') }}
    </div>

    <!-- Add to Cart Button at the Bottom -->
    <div id="add-to-cart-container" class="fixed-bottom d-none text-center p-1" style="background: #f8f9fa;">
        <a href="{{ route('carts.index') }}" class="btn btn-primary w-100" id="add-to-cart">Add to Cart</a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quantities = {}; // Track quantities of each item
        const addToCartContainer = document.getElementById('add-to-cart-container');

        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function () {
                const action = this.getAttribute('data-action');
                const id = this.getAttribute('data-id');
                const quantitySpan = document.getElementById('quantity-' + id);
                // const addToCartButton = document.getElementById('add-to-cart-' + id);

                let quantity = parseInt(quantitySpan.innerText);
                if (action === 'increase') {
                    quantity++;
                } else if (action === 'decrease' && quantity > 0) {
                    quantity--;
                }
                // Return value back to HTML page after + or - value
                quantitySpan.innerText = quantity;
                
                // Suppose the id of cuisine item is 5, and the user sets its quantity to 3. Then, quantities look like -
                // quantities = { 5 : 3 };
                quantities[id] = quantity;

                // Check if there's at least one item with quantity > 0 to show "Add to Cart"
                // Ex: This creates an array of quantities = { 5: 3, 6: 0, 7: 2 }, then:
                // - Object.values(quantities) : would give [3, 0, 2]
                // - .some(qty => qty > 0)     : checks if at least one value in the array is greater than 0. Return true if qty>0
                if (Object.values(quantities).some(qty => qty > 0)) {
                    // If d-none class removed, show button
                    addToCartContainer.classList.remove('d-none');
                } else {
                    addToCartContainer.classList.add('d-none');
                }
            });
        });

        // AJAX: Select the element with the ID add-to-cart and adds a click event listener to it.
        document.getElementById('add-to-cart').addEventListener('click', function () {
            // Object.entries(quantities) : Example: If quantities is { "1": 2, "3": 1 }, this will create an array like [["1", 2], ["3", 1]]
            // .filter(([id, qty]) => qty > 0) : filters this array to include only entries > 0. If quantities contained { "1": 0, "3": 2 }, only ["3", 2] would be kept.
            // .map(([id, qty]) => ({ id, quantity: qty })) : transforms each remaining key-value pair into an object with id and quantity properties. Example, ["3", 2] would become { id: "3", quantity: 2 }.
            const cartData = Object.entries(quantities)
                .filter(([id, qty]) => qty > 0)
                .map(([id, qty]) => ({ id, quantity: qty }));

            // cartData.forEach(item => { ... }) : iterates over each item in the cartData array
            // fetch : request is sent to the server for each item to URL {{ route('carts.add') }}
            cartData.forEach(item => {
                fetch("{{ route('carts.add') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(item)
                }).then(response => response.json()) // converts the server’s response to JSON.
                .then(data => { // checks if the response indicates success by looking for data.success
                    if (data.success) {
                        window.location.href = "{{ route('carts.index') }}";
                    }
                });
            });
        });
    });
</script>
@endsection