@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Task Management</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bi bi-plus"></i> Add Task
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="lead">Lead</option>
                                <option value="sale">Sale</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="assigneeFilter">
                                <option value="">All Assignees</option>
                                @foreach($salespersons as $salesperson)
                                    <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search tasks...">
                        </div>
                    </div>

                    <!-- Tasks Table -->
                    <div class="table-responsive position-relative">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Assignee</th>
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
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $task->assignee->photo_url }}" alt="{{ $task->assignee->name }}" 
                                                     class="rounded-circle me-2" width="24" height="24">
                                                {{ $task->assignee->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status === 'pending' ? 'secondary' : ($task->status === 'in_progress' ? 'primary' : 'success') }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $task->due_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-link btn-sm p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 10000;">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.tasks.show', $task) }}">
                                                            <i class="bi bi-eye me-2"></i> View Details
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.tasks.edit', $task) }}">
                                                            <i class="bi bi-pencil me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    @if($task->status === 'pending')
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'in_progress')">
                                                                <i class="bi bi-arrow-right me-2"></i> Move to Progress
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($task->status === 'in_progress')
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="updateTaskStatus({{ $task->id }}, 'completed')">
                                                                <i class="bi bi-check2 me-2"></i> Mark as Done
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteTask({{ $task->id }})">
                                                            <i class="bi bi-trash me-2"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-list-task mb-2" style="font-size: 2rem;"></i>
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

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="taskTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="taskDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskType" class="form-label">Type</label>
                        <select class="form-select" id="taskType" name="type" required>
                            <option value="">Select Type</option>
                            <option value="lead">Lead</option>
                            <option value="sale">Sale</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskAssignee" class="form-label">Assign To</label>
                        <select class="form-select" id="taskAssignee" name="assignee_id" required>
                            <option value="">Select Salesperson</option>
                            @foreach ($salespersons as $salesperson)
                                <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="taskDueDate" class="form-label">Due Date</label>
                        <input type="datetime-local" class="form-control" id="taskDueDate" name="due_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTaskButton">
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    Save Task
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Task Form Handler
    const addTaskForm = document.getElementById('addTaskForm');
    const saveTaskButton = document.getElementById('saveTaskButton');
    const spinner = saveTaskButton.querySelector('.spinner-border');

    saveTaskButton.addEventListener('click', async function() {
        if (!addTaskForm.checkValidity()) {
            addTaskForm.reportValidity();
            return;
        }

        // Disable button and show spinner
        saveTaskButton.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const formData = new FormData(addTaskForm);
            const response = await axios.post('/admin/tasks', Object.fromEntries(formData));

            // Show success message
            showToast('success', 'Task created successfully');

            // Close modal and reset form
            bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
            addTaskForm.reset();

            // Reload page to show new task
            window.location.reload();

        } catch (error) {
            const message = error.response?.data?.message || 'Failed to create task';
            showToast('error', message);
        } finally {
            // Enable button and hide spinner
            saveTaskButton.disabled = false;
            spinner.classList.add('d-none');
        }
    });

    // Task Status Update
    function updateTaskStatus(taskId, newStatus) {
        axios.put(`/admin/tasks/${taskId}/status`, {
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

    // Task Edit
    function editTask(taskId) {
        axios.get(`/admin/tasks/${taskId}`)
            .then(response => {
                const task = response.data;
                // Populate form fields
                document.getElementById('taskTitle').value = task.title;
                document.getElementById('taskDescription').value = task.description;
                document.getElementById('taskType').value = task.type;
                document.getElementById('taskAssignee').value = task.assignee_id;
                document.getElementById('taskDueDate').value = task.due_date;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('addTaskModal'));
                modal.show();
            })
            .catch(error => {
                showToast('error', 'Failed to load task details');
            });
    }

    // Task Delete
    function deleteTask(taskId) {
        if (confirm('Are you sure you want to delete this task?')) {
            axios.delete(`/admin/tasks/${taskId}`)
                .then(response => {
                    showToast('success', 'Task deleted successfully');
                    window.location.reload();
                })
                .catch(error => {
                    showToast('error', 'Failed to delete task');
                });
        }
    }

    // Filter Handlers
    document.getElementById('statusFilter').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('typeFilter').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('assigneeFilter').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('searchInput').addEventListener('input', function() {
        applyFilters();
    });

    function applyFilters() {
        const status = document.getElementById('statusFilter').value;
        const type = document.getElementById('typeFilter').value;
        const assignee = document.getElementById('assigneeFilter').value;
        const search = document.getElementById('searchInput').value;

        let url = '/admin/tasks?';
        if (status) url += `&status=${status}`;
        if (type) url += `&type=${type}`;
        if (assignee) url += `&assignee=${assignee}`;
        if (search) url += `&search=${search}`;

        window.location.href = url;
    }
</script>
@endpush
@endsection 