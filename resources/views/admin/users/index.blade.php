@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newUserModal">
                            <i class="fas fa-plus"></i> New User
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role->name }}', {{ $user->is_active ? 'true' : 'false' }}, '{{ $user->created_at->format('M d, Y H:i A') }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', {{ $user->role_id }}, {{ $user->is_active ? '1' : '0' }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New User Modal -->
<div class="modal fade" id="newUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role_id" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Name:</div>
                            <div class="col-md-8" id="view-name"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Email:</div>
                            <div class="col-md-8" id="view-email"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Role:</div>
                            <div class="col-md-8" id="view-role"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Status:</div>
                            <div class="col-md-8" id="view-status"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Created At:</div>
                            <div class="col-md-8" id="view-created"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit-name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="edit-email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role_id" id="edit-role" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="is_active" id="edit-status">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateUser()">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function saveUser() {
    const form = document.getElementById('userForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.users.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error creating user',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'Error creating user',
            icon: 'error'
        });
    });
}

function viewUser(id, name, email, role, isActive, createdAt) {
    document.getElementById('view-name').textContent = name;
    document.getElementById('view-email').textContent = email;
    document.getElementById('view-role').textContent = role;
    document.getElementById('view-status').textContent = isActive ? 'Active' : 'Inactive';
    document.getElementById('view-status').className = isActive ? 'text-success' : 'text-danger';
    document.getElementById('view-created').textContent = createdAt;
    
    new bootstrap.Modal(document.getElementById('viewUserModal')).show();
}

function editUser(id, name, email, roleId, isActive) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value = roleId;
    document.getElementById('edit-status').value = isActive ? '1' : '0';
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function updateUser() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const id = formData.get('id');
    
    fetch(`{{ route("admin.users.update", "") }}/${id}`, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error updating user',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: 'Error updating user',
            icon: 'error'
        });
    });
}

function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route("admin.users.destroy", "") }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error deleting user',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Error deleting user',
                    icon: 'error'
                });
            });
        }
    });
}
</script>
@endpush 