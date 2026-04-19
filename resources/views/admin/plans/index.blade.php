@extends('layouts.admin')

@section('title', 'Manage Plans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subscription Plans</h2>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Plan
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Listing Limit</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td><strong>{{ $plan->name }}</strong></td>
                        <td>₹{{ number_format($plan->price) }}</td>
                        <td>{{ $plan->listing_limit }} listings</td>
                        <td>{{ $plan->duration_days }} days</td>
                        <td>
                            @if($plan->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this plan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No plans found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
