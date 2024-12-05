@extends('layouts.main')

@section('title', 'Category')

@section('content')
<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h1 class="mb-0">Your Category</h1>
        </div>
        <div class="card-body">
            <!-- Categories Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Category</th>
                            <th>Created Date</th>
                            <th>Modified Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->created_at }}</td>
                                <td>{{ $category->updated_at }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admins.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('admins.categories.destroy', $category->id) }}"
                                        method="POST"
                                        style="display:inline;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admins.cuisines.index') }}" class="btn btn-secondary">Back To Menu</a>
                <a href="{{ route('admins.categories.create') }}" class="btn btn-success">Add New Category</a>
            </div>
        </div>
    </div>
</div>
@endsection