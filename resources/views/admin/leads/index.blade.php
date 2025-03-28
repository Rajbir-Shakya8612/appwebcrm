@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leads Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLeadModal">
                            <i class="fas fa-plus"></i> New Lead
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportLeads()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Lead Status Filter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('all')">All</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('new')">New</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('contacted')">Contacted</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('qualified')">Qualified</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('converted')">Converted</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('lost')">Lost</button>
                                <button type="button" class="btn btn-outline-primary" onclick="filterLeads('shared')">Shared</button>
                            </div>
                        </div>
                    </div>

                    <!-- Leads Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Expected Amount</th>
                                    <th>Follow-up Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leads as $lead)
                                <tr>
                                    <td>{{ $lead->name }}</td>
                                    <td>
                                        <div>{{ $lead->phone }}</div>
                                        <div class="text-muted">{{ $lead->email }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $lead->location }}</div>
                                        <div class="text-muted">{{ $lead->pincode }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getStatusColor($lead->status) }}">
                                            {{ ucfirst($lead->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($lead->expected_amount) }}</td>
                                    <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('M d, Y') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewLead({{ $lead->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editLead({{ $lead->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteLead({{ $lead->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No leads found</td>
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

<!-- New Lead Modal -->
<div class="modal fade" id="newLeadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="leadForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="converted">Converted</option>
                                    <option value="lost">Lost</option>
                                    <option value="shared">Shared</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode" maxlength="10">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Expected Amount</label>
                                <input type="number" class="form-control" name="expected_amount">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Follow-up Date</label>
                                <input type="date" class="form-control" name="follow_up_date">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Source</label>
                                <input type="text" class="form-control" name="source">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveLead()">Save Lead</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function saveLead() {
    const form = document.getElementById('leadForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.leads.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error creating lead');
        }
    });
}

function viewLead(id) {
    window.location.href = `{{ route("admin.leads.show", "") }}/${id}`;
}

function editLead(id) {
    window.location.href = `{{ route("admin.leads.edit", "") }}/${id}`;
}

function deleteLead(id) {
    if (confirm('Are you sure you want to delete this lead?')) {
        fetch(`{{ route("admin.leads.destroy", "") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting lead');
            }
        });
    }
}

function filterLeads(status) {
    window.location.href = `{{ route("admin.leads.index") }}?status=${status}`;
}

function getStatusColor(status) {
    const colors = {
        'new': 'primary',
        'contacted': 'info',
        'qualified': 'warning',
        'converted': 'success',
        'lost': 'danger',
        'shared': 'secondary'
    };
    return colors[status] || 'primary';
}

function exportLeads() {
    window.location.href = '{{ route("admin.leads.export") }}';
}
</script>
@endpush 