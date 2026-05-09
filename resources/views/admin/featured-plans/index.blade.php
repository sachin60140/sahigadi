@extends('layouts.admin')

@section('title', 'Featured Plans Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-gray-800">Featured Car Plans</h4>
        <a href="{{ route('admin.featured-plans.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add New Plan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Plan Name</th>
                            <th class="py-3">Duration</th>
                            <th class="py-3">Price</th>
                            <th class="py-3">Status</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td class="px-4 py-3 fw-medium">{{ $plan->name }}</td>
                                <td class="py-3">{{ $plan->duration_days }} Days</td>
                                <td class="py-3">₹{{ number_format($plan->price, 2) }}</td>
                                <td class="py-3">
                                    @if($plan->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.featured-plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.featured-plans.destroy', $plan) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No featured plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
