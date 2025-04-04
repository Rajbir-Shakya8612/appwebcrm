<!-- Task Form Component -->
<div class="modal fade" id="{{ $modalId ?? 'taskModal' }}" tabindex="-1" aria-labelledby="{{ $modalId ?? 'taskModal' }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId ?? 'taskModal' }}Label">{{ $title ?? 'Add New Task' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="{{ $formId ?? 'taskForm' }}" class="needs-validation" novalidate>
                    @csrf
                    @if(isset($task))
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ $task->id }}">
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="{{ $task->title ?? '' }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $task->description ?? '' }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="due_date" value="{{ isset($task) ? $task->due_date->format('Y-m-d') : '' }}" required min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority <span class="text-danger">*</span></label>
                        <select class="form-select" name="priority" required>
                            <option value="low" {{ isset($task) && $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ isset($task) && $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ isset($task) && $task->priority == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>

                    @if(isset($lead))
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="{{ $submitFunction ?? 'submitTaskForm()' }}">{{ $submitButtonText ?? 'Save Task' }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitTaskForm() {
    const form = document.getElementById('{{ $formId ?? 'taskForm' }}');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Show loading state
    Swal.fire({
        title: '{{ isset($task) ? 'Updating' : 'Creating' }} Task...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit the form
    const url = '{{ isset($task) ? "/salesperson/tasks/" . $task->id : "/salesperson/tasks" }}';
    const method = '{{ isset($task) ? "PUT" : "POST" }}';

    fetch(url, {
        method: method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('{{ $modalId ?? 'taskModal' }}'));
            modal.hide();

            // Show success message
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to {{ isset($task) ? "update" : "create" }} task');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: error.message || 'Failed to {{ isset($task) ? "update" : "create" }} task. Please try again.',
            icon: 'error'
        });
    });
}
</script>
@endpush 