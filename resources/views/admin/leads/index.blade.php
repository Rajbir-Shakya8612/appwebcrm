@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Leads Chart Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="leadsChart"></canvas>
                    </div>
                    <div class="chart-legend mt-4 text-center">
                        <div class="d-flex justify-content-center gap-4">
                            <div class="legend-item">
                                <span class="badge bg-primary">New leads</span>
                                <span class="ms-2">{{ $leadStats['new'] ?? 0 }}</span>
                            </div>
                            <div class="legend-item">
                                <span class="badge bg-danger">Pending Leads</span>
                                <span class="ms-2">{{ $leadStats['contacted'] ?? 0 }}</span>
                            </div>
                            <div class="legend-item">
                                <span class="badge bg-success">Confirm leads</span>
                                <span class="ms-2">{{ $leadStats['qualified'] ?? 0 }}</span>
                            </div>
                            <div class="legend-item">
                                <span class="badge bg-warning">Close Leads</span>
                                <span class="ms-2">{{ $leadStats['converted'] ?? 0 }}</span>
                            </div>
                            <div class="legend-item">
                                <span class="badge bg-info">Transfer Leads</span>
                                <span class="ms-2">{{ $leadStats['lost'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Leads Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary me-2" onclick="openNewLeadModal()">
                            <i class="fas fa-plus"></i> New Lead
                        </button>
                        <button type="button" class="btn btn-success" onclick="handleExport()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Lead Status Filter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-outline-primary active" data-status="all">All</button>
                                <button type="button" class="btn btn-outline-primary" data-status="new">New leads</button>
                                <button type="button" class="btn btn-outline-primary" data-status="contacted">Pending Leads</button>
                                <button type="button" class="btn btn-outline-primary" data-status="qualified">Confirm leads</button>
                                <button type="button" class="btn btn-outline-primary" data-status="converted">Close Leads</button>
                                <button type="button" class="btn btn-outline-primary" data-status="lost">Transfer Leads</button>
                            </div>
                        </div>
                    </div>

                    <!-- Leads Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="leadsTable">
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
                                <tr data-lead-id="{{ $lead->id }}">
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
                                        <span class="badge bg-{{ \App\Helpers\LeadHelper::getStatusColor($lead->status) }}">
                                            {{ ucfirst($lead->status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($lead->expected_amount) }}</td>
                                    <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('M d, Y') : '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewLeadDetails({{ json_encode($lead) }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editLeadDetails({{ json_encode($lead) }})">
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

@push('styles')
<style>
.chart-container {
    margin: 0 auto;
    max-width: 800px;
}
.chart-legend {
    margin-top: 2rem;
}
.legend-item {
    display: inline-flex;
    align-items: center;
    margin: 0 1rem;
}
.btn-group {
    flex-wrap: nowrap;
}
.btn-group .btn {
    white-space: nowrap;
    flex: 1;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Status color mapping
const STATUS_COLORS = {
    'new': 'primary',
    'contacted': 'danger',
    'qualified': 'success',
    'converted': 'warning',
    'lost': 'info'
};

// Status label mapping
const STATUS_LABELS = {
    'new': 'New leads',
    'contacted': 'Pending Leads',
    'qualified': 'Confirm leads',
    'converted': 'Close Leads',
    'lost': 'Transfer Leads'
};

// Get status color helper function
function getStatusColor(status) {
    return STATUS_COLORS[status] || 'primary';
}

// Get status label helper function
function getStatusLabel(status) {
    return STATUS_LABELS[status] || status;
}

// Initialize the leads chart
function initLeadsChart() {
    const ctx = document.getElementById('leadsChart').getContext('2d');
    const data = @json($leadStats);
    
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.values(STATUS_LABELS),
            datasets: [{
                data: [
                    data.new || 0,
                    data.contacted || 0,
                    data.qualified || 0,
                    data.converted || 0,
                    data.lost || 0
                ],
                backgroundColor: [
                    '#0d6efd',
                    '#dc3545',
                    '#198754',
                    '#ffc107',
                    '#0dcaf0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Leads Distribution',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: {
                        top: 10,
                        bottom: 30
                    }
                }
            }
        }
    });
}

// Handle export button click
function handleExport() {
    Swal.fire({
        title: 'Export Leads',
        text: 'Do you want to export all leads data?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, export',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("admin.leads.export") }}';
        }
    });
}

// View lead details
function viewLeadDetails(lead) {
    Swal.fire({
        title: 'Lead Details',
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> ${lead.name}</p>
                        <p><strong>Phone:</strong> ${lead.phone || '-'}</p>
                        <p><strong>Email:</strong> ${lead.email || '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(lead.status)}">${getStatusLabel(lead.status)}</span></p>
                        <p><strong>Location:</strong> ${lead.location || '-'}</p>
                        <p><strong>Expected Amount:</strong> ${lead.expected_amount ? new Intl.NumberFormat().format(lead.expected_amount) : '-'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Notes:</strong></p>
                        <p>${lead.notes || '-'}</p>
                    </div>
                </div>
            </div>
        `,
        width: '600px',
        showCloseButton: true,
        showConfirmButton: false
    });
}

// Edit lead details
function editLeadDetails(lead) {
    Swal.fire({
        title: 'Edit Lead',
        html: `
            <form id="editLeadForm" class="text-start">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="${lead.name}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" value="${lead.phone || ''}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="${lead.email || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            ${Object.entries(STATUS_LABELS).map(([value, label]) => `
                                <option value="${value}" ${lead.status === value ? 'selected' : ''}>${label}</option>
                            `).join('')}
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location" value="${lead.location || ''}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Expected Amount</label>
                        <input type="number" class="form-control" name="expected_amount" value="${lead.expected_amount || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Follow-up Date</label>
                        <input type="date" class="form-control" name="follow_up_date" value="${lead.follow_up_date || ''}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3">${lead.notes || ''}</textarea>
                    </div>
                </div>
            </form>
        `,
        width: '800px',
        showCancelButton: true,
        confirmButtonText: 'Update Lead',
        cancelButtonText: 'Cancel',
        showCloseButton: true,
        preConfirm: () => {
            const form = document.getElementById('editLeadForm');
            const formData = new FormData(form);
            return fetch(`{{ route("admin.leads.update", "") }}/${lead.id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Error updating lead');
                }
                return data;
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Success!',
                text: 'Lead updated successfully',
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        }
    }).catch(error => {
        Swal.fire({
            title: 'Error!',
            text: error.message,
            icon: 'error'
        });
    });
}

// Delete lead
function deleteLead(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route("admin.leads.destroy", "") }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Deleted!',
                        'Lead has been deleted.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        'Failed to delete lead.',
                        'error'
                    );
                }
            });
        }
    });
}

// Filter leads
function filterLeads(status) {
    window.location.href = status === 'all' 
        ? '{{ route("admin.leads") }}'
        : `{{ route("admin.leads.by-status", "") }}/${status}`;
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initLeadsChart();
    
    // Initialize status filter buttons
    document.querySelectorAll('.btn-group button').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterLeads(this.dataset.status);
        });
    });
});
</script>
@endpush
@endsection