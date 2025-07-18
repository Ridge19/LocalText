{{-- resources/views/user/ticket/index.blade.php --}}
@extends('layouts.app') {{-- or 'layouts.master' if that’s your base layout --}}

@section('title', 'My Support Tickets')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">My Support Tickets</h1>

    @if ($tickets->isEmpty())
        <div class="alert alert-info">
            You haven’t created any tickets yet.
        </div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ ucfirst($ticket->status) }}</td>
                        <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $ticket->updated_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('user.ticket.view', $ticket->id) }}" class="btn btn-sm btn-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
