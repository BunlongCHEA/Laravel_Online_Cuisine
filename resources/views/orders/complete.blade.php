@extends('layouts.main')

@section('title', 'Order Complete')

@section('content')
    <div class="container">
        <div class="alert alert-success text-center">
            <h1>Thank You!</h1>
            <p>Your order is complete. Enjoy your meal!</p>
        </div>
        <a href="{{ Auth::user()->role === 'admin' ? route('admins.cuisines.index') : route('users.cuisines.index') }}" class="btn btn-primary">Back to Menu</a>
    </div>
@endsection