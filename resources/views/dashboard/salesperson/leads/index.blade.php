@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12"></div>
            <div class="card mb-4">
                <div class="card-body"></div>
                    <div class="row">
                        <div class="col-md-8">
                            <div style="height: 400px;">
                                <canvas id="leadStatusChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-stats">
                                <h4>Lead Statistics</h4>
                                <div class="list-group">
                                    @foreach($leadStatuses as $status)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $status->name }}
                                        <span class="badge bg-{{ $status->color }} rounded-pill">
                                            {{ $leads->where('status_id', $status->id)->count() }}
                                        </span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Leads Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLeadModal">
                            <i class="fas fa-plus"></i> New Lead
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Lead Status Filter -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-primary active" data-status="all">All</button>
                                @foreach($leadStatuses as $status)
                                <button type="button" class="btn btn-{{ $status->color }}" data-status="{{ $status->id }}">
                                    {{ $status->name }}
                                </button>
                                @endforeach
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
                                <tr data-status="{{ $lead->status_id }}">
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
                                        @php
                                            $leadStatus = $lead->status()->first();
                                        @endphp
                                        @if($leadStatus)
                                            <span class="badge" style="background-color: {{ $leadStatus->color }}">
                                                {{ $leadStatus->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($lead->expected_amount) }}</td>
                                    <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('M d, Y') : '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewLeadModal" onclick="viewLeadDetails({{ $lead->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editLeadModal" onclick="editLeadDetails({{ $lead->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteLeadConfirm({{ $lead->id }})">
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

<!-- View Lead Modal -->
<div class="modal fade" id="viewLeadModal" tabindex="-1" aria-labelledby="viewLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLeadModalLabel">Lead Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="lead-details">
                    <!-- Details will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Lead Modal -->
<div class="modal fade" id="editLeadModal" tabindex="-1" aria-labelledby="editLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLeadModalLabel">Edit Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editLeadForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_lead_id">
                    <input type="hidden" name="latitude" id="edit_latitude">
                    <input type="hidden" name="longitude" id="edit_longitude">
                    <input type="hidden" name="location" id="edit_location">
                    <input type="hidden" name="additional_info" id="additional_info">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status_id" required>
                                    @foreach($leadStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expected Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="expected_amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Follow-up Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="follow_up_date" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6 d-none">
                            <div class="mb-3">
                                <label class="form-label">additional_info <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="additional_info" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
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
                <button type="button" class="btn btn-primary" onclick="updateLead()">Update Lead</button>
            </div>
        </div>
    </div>
</div>

<!-- New Lead Modal -->
<div class="modal fade" id="newLeadModal" tabindex="-1" aria-labelledby="newLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newLeadModalLabel">Add New Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newLeadForm">
                    @csrf
                    <input type="hidden" name="latitude" id="new_latitude">
                    <input type="hidden" name="longitude" id="new_longitude">
                    <input type="hidden" name="location" id="new_location">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status_id" required>
                                    @foreach($leadStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Expected Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="expected_amount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Follow-up Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="follow_up_date" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 d-none">
                            <div class="mb-3">
                                <label class="form-label">additional_info <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="additional_info" rows="3"></textarea>
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
                <button type="button" class="btn btn-primary" onclick="createLead()">Create Lead</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Get current location
function getCurrentLocation() {
    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Get location name using reverse geocoding
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            resolve({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                                location: data.display_name
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            reject(new Error('Unable to get location name'));
                        });
                },
                function(error) {
                    console.error('Error getting location:', error);
                    reject(new Error('Unable to get your current location'));
                }
            );
        } else {
            reject(new Error('Geolocation is not supported by your browser'));
        }
    });
}

// Create new lead
function createLead() {
    const form = document.getElementById('newLeadForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // Show loading state
    Swal.fire({
        title: 'Creating Lead...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Get current location before submitting
    getCurrentLocation()
        .then(locationData => {
            // Add location data to form
            formData.append('latitude', locationData.latitude);
            formData.append('longitude', locationData.longitude);
            formData.append('location', locationData.location);

            // Create additional_info object
            const additionalInfo = {
                additional_info: formData.get('additional_info'),
                location_details: {
                    address: locationData.location,
                    latitude: locationData.latitude,
                    longitude: locationData.longitude,
                    updated_at: new Date().toISOString()
                }
            };

            // Remove additional_info from formData as it will be in additional_info
            formData.delete('additional_info');
            
            // Add additional_info to formData
            formData.append('additional_info', JSON.stringify(additionalInfo));

            // Submit the form
            return fetch('/salesperson/leads', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Failed to create lead');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('newLeadModal'));
                modal.hide();
                
                // Reset form
                form.reset();
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Lead created successfully',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to create lead');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'Failed to create lead. Please try again.',
                icon: 'error'
            });
        });
}

// Update lead
function updateLead() {
    const form = document.getElementById('editLeadForm');
    const id = document.getElementById('edit_lead_id').value;
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Add _method field for PUT request
    formData.append('_method', 'PUT');
    
    // Show loading state
    Swal.fire({
        title: 'Updating Lead...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Get current location before submitting
    getCurrentLocation()
        .then(locationData => {
            // Add location data to form
            formData.append('latitude', locationData.latitude);
            formData.append('longitude', locationData.longitude);
            formData.append('location', locationData.location);

            // Create simple additional_info object
            const additionalInfo = formData.get('location') || '';

            // Submit the form
            return fetch(`/salesperson/leads/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Failed to update lead');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editLeadModal'));
                modal.hide();

                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'Lead updated successfully',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to update lead');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'Failed to update lead. Please try again.',
                icon: 'error'
            });
        });
}

// Filter leads by status
document.querySelectorAll('.btn-group button[data-status]').forEach(button => {
    button.addEventListener('click', function() {
        const statusId = this.dataset.status;
        
        // Update active button
        document.querySelectorAll('.btn-group button').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        // Filter table rows
        document.querySelectorAll('#leadsTable tbody tr').forEach(row => {
            if (statusId === 'all' || row.dataset.status === statusId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

// View lead details
function viewLeadDetails(id) {
    fetch(`/salesperson/leads/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(lead => {
        if (!lead) {
            throw new Error('Lead data not found');
        }

        // Extract additional_info value from JSON
        let additional_info = '';
        try {
            const additionalInfoObj = JSON.parse(lead.additional_info || '{}');
            additional_info = additionalInfoObj.additional_info || '-';
        } catch (e) {
            console.error('Error parsing additional_info:', e);
            additional_info = '-';
        }

        const detailsDiv = document.querySelector('#viewLeadModal .lead-details');
        
        detailsDiv.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <p><strong>Name:</strong> ${lead.name || '-'}</p>
                        <p><strong>Email:</strong> ${lead.email || '-'}</p>
                        <p><strong>Phone:</strong> ${lead.phone || '-'}</p>
                        <p><strong>Company:</strong> ${lead.company || '-'}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <p><strong>Status:</strong> <span class="badge bg-${lead.status ? lead.status.color : 'secondary'}" style="background-color:${lead.status.color}">${lead.status ? lead.status.name : 'Unknown'}</span></p>
                        <p><strong>Expected Amount:</strong> ${lead.expected_amount ? new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(lead.expected_amount) : '-'}</p>
                        <p><strong>Source:</strong> ${lead.source || '-'}</p>
                        <p><strong>Location:</strong> ${lead.location || '-'}</p>
                        <p><strong>Follow-up Date:</strong> ${lead.follow_up_date ? new Date(lead.follow_up_date).toLocaleDateString() : '-'}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3 d-none">
                <div class="col-12">
                    <div class="mb-3">
                        <p><strong>Additional Info:</strong></p>
                        <p>${additional_info}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="mb-3">
                        <p><strong>Description:</strong></p>
                        <p>${lead.description || '-'}</p>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="mb-3">
                        <p><strong>Notes:</strong></p>
                        <p>${lead.notes || '-'}</p>
                    </div>
                </div>
            </div>
        `;

        // Show the modal
        const viewModal = new bootstrap.Modal(document.getElementById('viewLeadModal'));
        viewModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load lead details. Please try again.',
            icon: 'error'
        });
    });
}

// Edit lead
function editLeadDetails(id) {
    fetch(`/salesperson/leads/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(lead => {
        if (!lead) {
            throw new Error('Lead data not found');
        }

        // Populate form fields
        const form = document.getElementById('editLeadForm');
        
        // Set hidden ID field
        document.getElementById('edit_lead_id').value = lead.id;
        
        // Populate text fields
        const fields = [
            'name', 'email', 'phone', 'company', 'source', 
            'expected_amount', 'notes','description'
        ];
        
        fields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.value = lead[field] || '';
            }
        });

        // Handle additional_info field
        const additional_infoInput = form.querySelector('[name="additional_info"]');
        if (additional_infoInput && lead.additional_info) {
            try {
                const parsedInfo = JSON.parse(lead.additional_info);
                additional_infoInput.value = parsedInfo.additional_info || '';
            } catch (e) {
                console.error('Error parsing additional_info:', e);
                additional_infoInput.value = '';
            }
        }

        // Handle follow-up date
        const followUpDateInput = form.querySelector('[name="follow_up_date"]');
        if (followUpDateInput && lead.follow_up_date) {
            const date = new Date(lead.follow_up_date);
            const formattedDate = date.toISOString().split('T')[0];
            followUpDateInput.value = formattedDate;
        }
        
        // Set status dropdown
        const statusSelect = form.querySelector('[name="status_id"]');
        if (statusSelect && lead.status) {
            statusSelect.value = lead.status.id;
        }

        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editLeadModal'));
        editModal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to load lead details. Please try again.',
            icon: 'error'
        });
    });
}

// Delete lead confirmation
function deleteLeadConfirm(id) {
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
            deleteLead(id);
        }
    });
}

// Delete lead
function deleteLead(id) {
    fetch(`/salesperson/leads/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to delete lead');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Deleted!',
                text: 'Lead has been deleted successfully.',
                icon: 'success'
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to delete lead');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: error.message || 'Failed to delete lead. Please try again.',
            icon: 'error'
        });
    });
}

// Close modal and remove backdrop
function closeModal(modalId) {
    const modalElement = document.getElementById(modalId);
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}

// Update the modal close buttons to use the closeModal function
document.querySelectorAll('.btn-close[data-bs-dismiss="modal"]').forEach(button => {
    button.addEventListener('click', function() {
        closeModal(this.closest('.modal').id);
    });
});

// Ensure backdrop is removed when modal is closed
document.addEventListener('hidden.bs.modal', function (event) {
    const modal = event.target;
    if (modal.classList.contains('modal')) {
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
});

// Initialize the chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('leadStatusChart').getContext('2d');
    const statusData = {!! json_encode($leadStatuses->map(function($status) use ($leads) {
        return [
            'name' => $status->name,
            'count' => $leads->where('status_id', $status->id)->count(),
            'color' => $status->color
        ];
    })) !!};

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.name),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    '#3B82F6', // Primary (New Lead)
                    '#F59E0B', // Warning (Contacted)
                    '#10B981', // Success (Qualified)
                    '#8B5CF6', // Purple (Proposal Sent)
                    '#EC4899', // Pink (Negotiation)
                    '#059669', // Green (Closed Won)
                    '#DC2626'  // Red (Closed Lost)
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'Lead Distribution by Status',
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
});
</script>
@endpush
