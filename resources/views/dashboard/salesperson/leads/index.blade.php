@extends('layouts.salesperson')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Leads</h5>
                    <h2 class="mb-0" id="total-leads">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Converted</h5>
                    <h2 class="mb-0" id="converted-leads">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">In Pipeline</h5>
                    <h2 class="mb-0" id="pipeline-leads">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Lost</h5>
                    <h2 class="mb-0" id="lost-leads">0</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Leads Pipeline</h5>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addLeadModal">
                        <i class="fas fa-plus"></i> Add Lead
                    </button>
                </div>
                <div class="card-body">
                    <div class="kanban-board">
                        <div class="row">
                            <!-- New Leads -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-primary text-white">
                                        <h6 class="mb-0">New</h6>
                                        <span class="badge badge-light" id="new-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="new">
                                        <!-- Leads will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Contacted -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-info text-white">
                                        <h6 class="mb-0">Contacted</h6>
                                        <span class="badge badge-light" id="contacted-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="contacted">
                                        <!-- Leads will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Qualified -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-success text-white">
                                        <h6 class="mb-0">Qualified</h6>
                                        <span class="badge badge-light" id="qualified-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="qualified">
                                        <!-- Leads will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Proposal -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-warning text-white">
                                        <h6 class="mb-0">Proposal</h6>
                                        <span class="badge badge-light" id="proposal-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="proposal">
                                        <!-- Leads will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Negotiation -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-warning text-white">
                                        <h6 class="mb-0">Negotiation</h6>
                                        <span class="badge badge-light" id="negotiation-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="negotiation">
                                        <!-- Leads will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Converted -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-success text-white">
                                        <h6 class="mb-0">Converted</h6>
                                        <span class="badge badge-light" id="converted-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="converted">
                                        <!-- Leads will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Lost -->
                            <div class="col-md-3">
                                <div class="kanban-column">
                                    <div class="kanban-column-header bg-danger text-white">
                                        <h6 class="mb-0">Lost</h6>
                                        <span class="badge badge-light" id="lost-count">0</span>
                                    </div>
                                    <div class="kanban-column-body" data-status="lost">
                                        <!-- Leads will be added here dynamically -->
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

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Lead</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addLeadForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company</label>
                                <input type="text" class="form-control" name="company">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Source</label>
                                <select class="form-control" name="source" required>
                                    <option value="">Select Source</option>
                                    <option value="website">Website</option>
                                    <option value="referral">Referral</option>
                                    <option value="social">Social Media</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expected Value</label>
                                <input type="number" class="form-control" name="expected_value" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Next Follow Up</label>
                                <input type="date" class="form-control" name="next_follow_up">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="negotiation">Negotiation</option>
                                    <option value="converted">Converted</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveLead">Save Lead</button>
            </div>
        </div>
    </div>
</div>

<!-- Lead Card Template -->
<template id="leadCardTemplate">
    <div class="kanban-card" draggable="true">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-2"></h6>
                <p class="card-text mb-2">
                    <small class="text-muted">
                        <i class="fas fa-phone"></i> <span class="phone"></span>
                    </small>
                </p>
                <p class="card-text mb-2">
                    <small class="text-muted">
                        <i class="fas fa-building"></i> <span class="company"></span>
                    </small>
                </p>
                <p class="card-text mb-2">
                    <small class="text-muted">
                        <i class="fas fa-calendar"></i> <span class="next-follow-up"></span>
                    </small>
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge badge-pill expected-value"></span>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary edit-lead">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-lead">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
let leads = [];
let leadCardTemplate = document.getElementById('leadCardTemplate');

// Initialize Sortable for each column
document.querySelectorAll('.kanban-column-body').forEach(column => {
    new Sortable(column, {
        group: 'leads',
        animation: 150,
        onEnd: function(evt) {
            const leadId = evt.item.dataset.leadId;
            const newStatus = evt.to.dataset.status;
            updateLeadStatus(leadId, newStatus);
        }
    });
});

// Load leads
function loadLeads() {
    fetch('/admin/leads')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                leads = data.leads;
                updateKanbanBoard();
                updateStatistics();
            }
        });
}

// Update Kanban board
function updateKanbanBoard() {
    // Clear all columns
    document.querySelectorAll('.kanban-column-body').forEach(column => {
        column.innerHTML = '';
    });

    // Add leads to their respective columns
    leads.forEach(lead => {
        const column = document.querySelector(`.kanban-column-body[data-status="${lead.status}"]`);
        if (column) {
            column.appendChild(createLeadCard(lead));
        }
    });

    // Update counts
    updateColumnCounts();
}

// Create lead card
function createLeadCard(lead) {
    const card = leadCardTemplate.content.cloneNode(true);
    const cardElement = card.querySelector('.kanban-card');
    
    cardElement.dataset.leadId = lead.id;
    
    cardElement.querySelector('.card-title').textContent = lead.name;
    cardElement.querySelector('.phone').textContent = lead.phone;
    cardElement.querySelector('.company').textContent = lead.company || 'N/A';
    cardElement.querySelector('.next-follow-up').textContent = lead.next_follow_up_formatted || 'No follow-up';
    cardElement.querySelector('.expected-value').textContent = lead.formatted_expected_value;
    cardElement.querySelector('.expected-value').className = `badge badge-pill badge-${lead.status_color}`;
    
    // Add event listeners
    cardElement.querySelector('.edit-lead').addEventListener('click', () => editLead(lead));
    cardElement.querySelector('.delete-lead').addEventListener('click', () => deleteLead(lead.id));
    
    return card;
}

// Update lead status
function updateLeadStatus(leadId, newStatus) {
    fetch(`/admin/leads/${leadId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const lead = leads.find(l => l.id === leadId);
            if (lead) {
                lead.status = newStatus;
                updateStatistics();
            }
        }
    });
}

// Update statistics
function updateStatistics() {
    const stats = {
        total: leads.length,
        converted: leads.filter(l => l.status === 'converted').length,
        pipeline: leads.filter(l => l.status !== 'converted' && l.status !== 'lost').length,
        lost: leads.filter(l => l.status === 'lost').length
    };

    document.getElementById('total-leads').textContent = stats.total;
    document.getElementById('converted-leads').textContent = stats.converted;
    document.getElementById('pipeline-leads').textContent = stats.pipeline;
    document.getElementById('lost-leads').textContent = stats.lost;
}

// Update column counts
function updateColumnCounts() {
    document.querySelectorAll('.kanban-column-body').forEach(column => {
        const status = column.dataset.status;
        const count = leads.filter(l => l.status === status).length;
        document.getElementById(`${status}-count`).textContent = count;
    });
}

// Add new lead
document.getElementById('saveLead').addEventListener('click', function() {
    const form = document.getElementById('addLeadForm');
    const formData = new FormData(form);
    
    fetch('/admin/leads', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            leads.push(data.lead);
            updateKanbanBoard();
            updateStatistics();
            $('#addLeadModal').modal('hide');
            form.reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Lead added successfully'
            });
        }
    });
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadLeads();
    
    // Refresh leads every 5 minutes
    setInterval(loadLeads, 300000);
});
</script>
@endpush

@push('styles')
<style>
.kanban-board {
    overflow-x: auto;
    padding: 1rem 0;
}

.kanban-column {
    min-width: 300px;
    margin-right: 1rem;
}

.kanban-column-header {
    padding: 0.75rem;
    border-radius: 0.25rem 0.25rem 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kanban-column-body {
    min-height: 200px;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 0 0 0.25rem 0.25rem;
}

.kanban-card {
    margin-bottom: 0.5rem;
    cursor: move;
}

.kanban-card .card {
    border-radius: 0.25rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kanban-card .card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.kanban-card .card-title {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.kanban-card .card-text {
    font-size: 0.8rem;
}

.kanban-card .badge {
    font-size: 0.8rem;
}

.kanban-card .btn-group {
    font-size: 0.8rem;
}

.sortable-ghost {
    opacity: 0.5;
}

.sortable-drag {
    opacity: 0.8;
}
</style>
@endpush 