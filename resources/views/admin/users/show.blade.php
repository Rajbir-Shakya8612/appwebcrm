@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Basic Information</h5>
                                    <div class="mb-3">
                                        <label class="fw-bold">Name:</label>
                                        <p>{{ $user->name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Email:</label>
                                        <p>{{ $user->email }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Role:</label>
                                        <p>{{ $user->role->name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Status:</label>
                                        <p>
                                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Created At:</label>
                                        <p>{{ $user->created_at->format('M d, Y H:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Additional Information</h5>
                                    <div class="mb-3">
                                        <label class="fw-bold">Phone:</label>
                                        <p>{{ $user->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">WhatsApp:</label>
                                        <p>{{ $user->whatsapp_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Address:</label>
                                        <p>{{ $user->address ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Pincode:</label>
                                        <p>{{ $user->pincode ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Date of Joining:</label>
                                        <p>{{ $user->date_of_joining ? $user->date_of_joining->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 