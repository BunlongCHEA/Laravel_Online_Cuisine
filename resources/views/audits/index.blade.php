@extends('layouts.main')

@section('title', 'Audit Logs')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Audit Logs</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Action</th>
                <th>URL</th>
                <th>IP Address</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user ? $log->user->name : 'Guest' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->url }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
