@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Lead Information Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Lead Information</h5>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" onclick="editLead({{ $lead }})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteLead({{ $lead->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Basic Information</h6>
                            <p><strong>Name:</strong> {{ $lead->name }}</p>
                            <p><strong>Phone:</strong> {{ $lead->phone }}</p>
                            <p><strong>Email:</strong> {{ $lead->email ?? 'N/A' }}</p>
                            <p><strong>Company:</strong> {{ $lead->company ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Lead Details</h6>
                            <p><strong>Source:</strong> {{ ucfirst($lead->source) }}</p>
                            <p><strong>Status:</strong> <span class="badge badge-{{ $lead->status_color }}">{{ $lead->status_label }}</span></p>
                            <p><strong>Expected Value:</strong> {{ $lead->formatted_expected_value }}</p>
                            <p><strong>Next Follow Up:</strong> {{ $lead->next_follow_up_formatted ?? 'Not scheduled' }}</p>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-2">Notes</h6>
                            <p>{{ $lead->notes ?? 'No notes available.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lead Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-date">
                                {{ $lead->created_at->format('M d, Y H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h6>Lead Created</h6>
                                <p>Lead was added to the system</p>
                            </div>
                        </div>
                        @foreach($lead->activities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-date">
                                {{ $activity->created_at->format('M d, Y H:i') }}
                            </div>
                            <div class="timeline-content">
                                <h6>{{ $activity->type }}</h6>
                                <p>{{ $activity->description }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <button class="list-group-item list-group-item-action" onclick="updateLeadStatus({{ $lead->id }}, 'contacted')">
                            <i class="fas fa-phone mr-2"></i> Mark as Contacted
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="updateLeadStatus({{ $lead->id }}, 'qualified')">
                            <i class="fas fa-check-circle mr-2"></i> Mark as Qualified
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="updateLeadStatus({{ $lead->id }}, 'proposal')">
                            <i class="fas fa-file-alt mr-2"></i> Send Proposal
                        </button>
                        <button class="list-group-item list-group-item-action" onclick="updateLeadStatus({{ $lead->id }}, 'converted')">
                            <i class="fas fa-check mr-2"></i> Mark as Converted
                        </button>
                        <button class="list-group-item list-group-item-action text-danger" onclick="updateLeadStatus({{ $lead->id }}, 'lost')">
                            <i class="fas fa-times-circle mr-2"></i> Mark as Lost
                        </button>
                    </div>
                </div>
            </div>

            <!-- Follow Up Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Schedule Follow Up</h5>
                </div>
                <div class="card-body">
                    <form id="followUpForm">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="next_follow_up" value="{{ $lead->next_follow_up?->format('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Schedule Follow Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('dashboard.salesperson.leads.edit')

@push('scripts')
<script>
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
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Lead status updated successfully'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

// Schedule follow up
document.getElementById('followUpForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(`/admin/leads/{{ $lead->id }}/follow-up`, {
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
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Follow up scheduled successfully'
            }).then(() => {
                window.location.reload();
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:after {
    content: '';
    position: absolute;
    left: -4px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}

.timeline-date {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
}

.timeline-content h6 {
    margin-bottom: 5px;
}

.timeline-content p {
    margin-bottom: 0;
    font-size: 0.875rem;
}
</style>
@endpush 
@endsection