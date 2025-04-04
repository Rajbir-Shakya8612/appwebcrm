@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">My Tasks</h5>
                    <a href="{{ route('salesperson.tasks.create') }}" class="btn btn-sm btn-primary">Add New</a>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="lead">Lead</option>
                                <option value="sale">Sale</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search tasks...">
                        </div>
                    </div>

                    <!-- Tasks Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            <span class="badge bg-{{ $task->type === 'lead' ? 'info' : ($task->type === 'sale' ? 'success' : 'warning') }}">
                                                {{ ucfirst($task->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status === 'pending' ? 'secondary' : ($task->status === 'in_progress' ? 'primary' : 'success') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $task->due_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                {{-- View Button (Always shown) --}}
                                                <a href="{{ route('salesperson.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Move to Progress (If Pending) --}}
                                                @if($task->status === 'pending')
                                                    <button class="btn btn-sm btn-outline-success" title="Move to Progress"
                                                        onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif

                                                {{-- Mark as Completed (If In Progress) --}}
                                                @if($task->status === 'in_progress')
                                                    <button class="btn btn-sm btn-outline-success" title="Mark as Completed"
                                                        onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-list-task mb-2" style="font-size: 2rem;"></i>
                                            <p class="mb-0">No tasks found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Task Status Update
    function updateTaskStatus(taskId, newStatus) {
        axios.put(`/salesperson/tasks/${taskId}/status`, {
            status: newStatus
        })
        .then(response => {
           Swal.fire({
            title: 'Success!',
            text: 'Task status updated successfully',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.reload();
        });

        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'Failed to update task status',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    // Filter Handlers
    document.getElementById('statusFilter').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('searchInput').addEventListener('input', function() {
        applyFilters();
    });

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchInput').value;

        let url = '/salesperson/tasks?';
        if (status) url += `&status=${status}`;
        if (type) url += `&type=${type}`;
        if (search) url += `&search=${search}`;

        window.location.href = url;
    }
</script>
@endpush
@endsection 