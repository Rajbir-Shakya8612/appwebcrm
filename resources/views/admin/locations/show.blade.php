@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Location Details</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" onclick="editLocation({{ $location->id }})">
                            <i class="fas fa-edit"></i> Edit Location
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $location->name }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $location->address }}</td>
                                </tr>
                                <tr>
                                    <th>Pincode</th>
                                    <td>{{ $location->pincode }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $location->is_active ? 'success' : 'danger' }}">
                                            {{ $location->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Contact Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $location->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $location->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $location->notes ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Location Tracking</h5>
                            <div id="map" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Location Modal -->
<div class="modal fade" id="editLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editLocationForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $location->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" required>{{ $location->address }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" value="{{ $location->phone }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $location->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode" value="{{ $location->pincode }}" maxlength="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="is_active">
                                    <option value="1" {{ $location->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$location->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3">{{ $location->notes }}</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateLocation({{ $location->id }})">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
<script>
let map;
let marker;

function initMap() {
    const location = { lat: {{ $location->latitude ?? 0 }}, lng: {{ $location->longitude ?? 0 }} };
    
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: location
    });

    marker = new google.maps.Marker({
        position: location,
        map: map,
        title: '{{ $location->name }}'
    });
}

function editLocation(id) {
    $('#editLocationModal').modal('show');
}

function updateLocation(id) {
    const form = document.getElementById('editLocationForm');
    const formData = new FormData(form);
    
    fetch(`{{ route('admin.locations.update', '') }}/${id}`, {
        method: 'PUT',
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
            alert('Error updating location');
        }
    });
}

// Initialize map when the page loads
window.onload = initMap;
</script>
@endpush 