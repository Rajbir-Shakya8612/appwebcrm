@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Location Details</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-warning" onclick="editLocation({{ $location->id }})">
                            <i class="fas fa-edit"></i> Edit Location
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteLocation({{ $location->id }})">
                            <i class="fas fa-trash"></i> Delete Location
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $location->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-{{ $location->is_active ? 'success' : 'danger' }}">
                                                    {{ $location->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $location->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $location->updated_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Contact Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $location->address }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pincode</th>
                                            <td>{{ $location->pincode }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $location->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $location->email }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Notes</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $location->notes ?? 'No notes available.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Location Map</h5>
                                </div>
                                <div class="card-body">
                                    <div id="map" style="height: 400px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
<script>
function initMap() {
    const location = {!! json_encode($location) !!};
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: location.latitude || 0, lng: location.longitude || 0 }
    });

    if (location.latitude && location.longitude) {
        new google.maps.Marker({
            position: { lat: location.latitude, lng: location.longitude },
            map: map,
            title: location.name
        });
    }
}

function editLocation(id) {
    window.location.href = `{{ route("admin.locations.edit", "") }}/${id}`;
}

function deleteLocation(id) {
    if (confirm('Are you sure you want to delete this location?')) {
        fetch(`{{ route("admin.locations.destroy", "") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.locations.index") }}';
            } else {
                alert('Error deleting location');
            }
        });
    }
}

// Initialize map when the page loads
window.onload = initMap;
</script>
@endpush 