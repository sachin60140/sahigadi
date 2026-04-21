@extends('layouts.admin')

@section('title', 'Contact Enquiries - Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">Contact Enquiries</h3>
            <p class="text-muted mb-0">Manage customer messages and contact form submissions.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Sender Name</th>
                            <th class="px-4 py-3">Email Address</th>
                            <th class="px-4 py-3">Subject</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enquiries as $enquiry)
                        <tr class="{{ !$enquiry->is_read ? 'fw-bold bg-light bg-opacity-50' : '' }}">
                            <td class="px-4 py-3">{{ $enquiry->id }}</td>
                            <td class="px-4 py-3">
                                @if($enquiry->is_read)
                                    <span class="badge bg-secondary">Read</span>
                                @else
                                    <span class="badge bg-success">Unread</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $enquiry->name }}</td>
                            <td class="px-4 py-3">
                                <a href="mailto:{{ $enquiry->email }}" class="text-decoration-none">{{ $enquiry->email }}</a>
                            </td>
                            <td class="px-4 py-3 text-truncate" style="max-width: 250px;">
                                {{ $enquiry->subject }}
                            </td>
                            <td class="px-4 py-3 text-muted small">
                                {{ $enquiry->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('admin.contact-enquiries.show', $enquiry->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if(!$enquiry->is_read)
                                <form action="{{ route('admin.contact-enquiries.read', $enquiry->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-check-all"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.contact-enquiries.destroy', $enquiry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this enquiry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 mb-3 d-block"></i>
                                    <h5>No Enquiries Found</h5>
                                    <p>There are no messages from the contact form yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($enquiries->hasPages())
            <div class="p-4 border-top">
                {{ $enquiries->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
