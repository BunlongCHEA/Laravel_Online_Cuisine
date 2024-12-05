@extends('layouts.main')

@section('title', 'Cuisine Details')

@section('content')
<div class="container my-5">
    <div class="card shadow">
        <div class="card-header text-center bg-primary text-white">
            <h1 class="mb-0">{{ $cuisine->name }}</h1>
        </div>
        <div class="card-body">
            @if($cuisine->image)
                <div class="text-center mb-4">
                    <img src="{{ asset('storage/' . $cuisine->image) }}" alt="{{ $cuisine->name }}" class="img-fluid rounded" style="max-width: 500px;">
                </div>
            @endif

            <div class="mb-3">
                <h5><strong>Description:</strong></h5>
                <p class="text-muted">{{ $cuisine->description }}</p>
            </div>

            <div class="mb-3">
                <h5><strong>Category:</strong></h5>
                <p class="text-muted">{{ $cuisine->category ? $cuisine->category->name : 'None' }}</p>
            </div>

            <div class="mb-3">
                <h5><strong>Price ($):</strong></h5>
                <p class="text-muted">{{ $cuisine->price }}</p>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('admins.cuisines.edit', $cuisine->id) }}" class="btn btn-primary">Edit Cuisine</a>

                <form action="{{ route('admins.cuisines.destroy', $cuisine->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this cuisine?')">Delete</button>
                </form>

                <a href="{{ route('admins.cuisines.index') }}" class="btn btn-secondary">Back to Menu</a>
            </div>
        </div>
    </div>
</div>
@endsection