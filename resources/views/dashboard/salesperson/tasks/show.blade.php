@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Task Details</h5>
                    <a href="{{ route('salesperson.tasks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Tasks
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h4>{{ $task->title }}</h4>
                                <p class="text-muted">{{ $task->description }}</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Type</h6>
                                    <p>
                                        <span class="badge bg-{{ $task->type === 'lead' ? 'info' : ($task->type === 'sale' ? 'success' : 'warning') }}">
                                            {{ ucfirst($task->type) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Status</h6>
                                    <p>
                                        <span class="badge bg-{{ $task->status === 'Pending' ? 'secondary' : ($task->status === 'in_progress' ? 'primary' : 'success') }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Due Date</h6>
                                    <p>{{ $task->due_date->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Created At</h6>
                                    <p>{{ $task->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>

                            @if($task->completed_at)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Completed At</h6>
                                    <p>{{ $task->completed_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if($task->status === 'Pending')
                                            <button class="btn btn-primary" onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">
                                                <i class="bi bi-arrow-right"></i> Move to Progress
                                            </button>
                                        @endif
                                        
                                        @if($task->status === 'in_progress')
                                            <button class="btn btn-success" onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                                <i class="bi bi-check2"></i> Mark as completed
                                            </button>
                                        @endif
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

@push('scripts')
<script>
    // Task Status Update
    function updateTaskStatus(taskId, newStatus) {
        axios.patch(`/salesperson/tasks/${taskId}/status`, {
            status: newStatus
        })
        .then(response => {
            showToast('success', 'Task status updated successfully');
            window.location.reload();
        })
        .catch(error => {
            showToast('error', 'Failed to update task status');
        });
    }
</script>
@endpush
@endsection 