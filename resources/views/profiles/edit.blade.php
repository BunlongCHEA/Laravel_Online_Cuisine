@extends('layouts.main')

@section('title', 'User Profile')

@section('content')
    <div class="container">
        <h2 class="my-4">Edit Profile</h2>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Authenticated User Info -->
        <form method="POST" action="{{ route('profiles.update', $authUser->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" name="name" class="form-control" value="{{ $authUser->name }}" required>
                </div>
            </div>

            <!-- Only admin role can update email for themselves and others, user role can only read the email -->
            @if($authUser->role === 'admin')
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" value="{{ $authUser->email }}" required>
                </div>
            </div>
            @else
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" value="{{ $authUser->email }}" readonly>
                </div>
            </div>
            @endif

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" name="password" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Confirm Password</label>
                <div class="col-sm-10">
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-success">Update Profile</button>
            <a href="{{ Auth::user()->role === 'admin' ? route('admins.cuisines.index') : route('users.cuisines.index') }}" class="btn btn-primary">Back to Menu</a>
        </form>

        
        @if($authUser->role === 'admin')
        <hr>
        <h3>Manage Users</h3>
        <form method="POST" id="admin-update-form" action="{{ route('profiles.update', $authUser->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Select User</label>
                <div class="col-sm-10">
                    <select id="user-select" name="user_id" class="form-select" onchange="populateUserData()">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" 
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="user-details" style="display: none;">
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text" id="admin-name" name="name" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" id="admin-email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Confirm Password</label>
                    <div class="col-sm-10">
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Update User</button>
                <a href="{{ Auth::user()->role === 'admin' ? route('admins.cuisines.index') : route('users.cuisines.index') }}" class="btn btn-primary">Back to Menu</a>
            </div>
        </form>
    @endif

    <script>
        function populateUserData() {
            const selectedUser = document.querySelector('#user-select');
            const userDetails = document.querySelector('#user-details');

            if (selectedUser.value) {
                const name = selectedUser.options[selectedUser.selectedIndex].dataset.name;
                const email = selectedUser.options[selectedUser.selectedIndex].dataset.email;
                const userId = selectedUser.value; // Get the selected user's ID

                // Update the form action to target the selected user's ID
                const form = document.querySelector('#admin-update-form');
                form.action = '/profiles/update/' + userId;

                // Populate the user details in the input fields
                document.querySelector('#admin-name').value = name;
                document.querySelector('#admin-email').value = email;

                // Show the user details section
                userDetails.style.display = 'block';
            } else {
                // Hide the user details section if no user is selected
                userDetails.style.display = 'none';
            }
        }
    </script>
@endsection