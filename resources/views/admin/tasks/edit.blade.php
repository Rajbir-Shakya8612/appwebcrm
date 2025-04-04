@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Edit Task</h5>
                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Task
                    </a>
                </div>
                <div class="card-body">
                    <form id="editTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="taskTitle" name="title" value="{{ old('title', $task->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" name="description" rows="3" required>{{ old('description', $task->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskType" class="form-label">Type</label>
                            <select class="form-select" id="taskType" name="type" required>
                                <option value="">Select Type</option>
                                <option value="lead" {{ old('type', $task->type) === 'lead' ? 'selected' : '' }}>Lead</option>
                                <option value="sale" {{ old('type', $task->type) === 'sale' ? 'selected' : '' }}>Sale</option>
                                <option value="meeting" {{ old('type', $task->type) === 'meeting' ? 'selected' : '' }}>Meeting</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="taskAssignee" class="form-label">Assign To</label>
                            <select class="form-select" id="taskAssignee" name="assignee_id" required>
                                <option value="">Select Salesperson</option>
                                @foreach ($salespersons as $salesperson)
                                    <option value="{{ $salesperson->id }}" {{ old('assignee_id', $task->assignee_id) == $salesperson->id ? 'selected' : '' }}>
                                        {{ $salesperson->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="taskDueDate" class="form-label">Due Date</label>
                            <input type="datetime-local" class="form-control" id="taskDueDate" name="due_date" 
                                   value="{{ old('due_date', $task->due_date->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskStatus" class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.tasks.show', $task) }}'">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="updateTaskButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Update Task
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const editTaskForm = document.getElementById('editTaskForm');
    const updateTaskButton = document.getElementById('updateTaskButton');
    const spinner = updateTaskButton.querySelector('.spinner-border');

    updateTaskButton.addEventListener('click', async function() {
        if (!editTaskForm.checkValidity()) {
            editTaskForm.reportValidity();
            return;
        }

        // Disable button and show spinner
        updateTaskButton.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const formData = new FormData(editTaskForm);
            const response = await axios.put(`/admin/tasks/{{ $task->id }}`, Object.fromEntries(formData));

            // Show success message
            showToast('success', 'Task updated successfully');

            // Redirect to task details
            window.location.href = `/admin/tasks/{{ $task->id }}`;

        } catch (error) {
            const message = error.response?.data?.message || 'Failed to update task';
            showToast('error', message);
        } finally {
            // Enable button and hide spinner
            updateTaskButton.disabled = false;
            spinner.classList.add('d-none');
        }
    });
</script>
@endpush
@endsection 