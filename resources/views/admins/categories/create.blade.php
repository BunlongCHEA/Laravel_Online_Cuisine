@extends('layouts.main')

@section('title', 'Create Category')

@section('content')
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Create New Category</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('admins.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Category Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            required
                        />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admins.categories.index') }}" class="btn btn-secondary">Back To Category</a>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection