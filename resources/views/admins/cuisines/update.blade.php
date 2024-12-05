@extends('layouts.main')

@section('title', 'Modify Cuisine')

@section('content')
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h1 class="card-title mb-0">Modify - {{ $cuisine->name }}</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('admins.cuisines.update', $cuisine->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Cuisine Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Cuisine Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ $cuisine->name }}"
                            class="form-control @error('name') is-invalid @enderror" 
                            required
                        />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea 
                            name="description" 
                            id="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            rows="4"
                        >{{ $cuisine->description }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input 
                            type="number" 
                            name="price" 
                            id="price" 
                            value="{{ $cuisine->price }}"
                            class="form-control @error('price') is-invalid @enderror" 
                            step="0.01" 
                            required
                        />
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select 
                            name="category_id" 
                            id="category_id" 
                            class="form-select @error('category_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ $cuisine->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input 
                            type="file" 
                            name="image" 
                            id="image" 
                            class="form-control @error('image') is-invalid @enderror"
                        />
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admins.cuisines.index') }}" class="btn btn-secondary">Back To Menu</a>
                        <button type="submit" class="btn btn-primary">Modify Cuisine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection