@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Add CSRF token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Locations Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLocationModal">
                            <i class="fas fa-plus"></i> New Location
                        </button>
                        <input type="date" class="form-control" id="trackingDate" value="{{ $date }}">
                    </div>
                </div>
                <div class="card-body">
                    <!-- Currently Checked In Users -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Currently Checked In Users</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Check In Time</th>
                                                    <th>Status</th>
                                                    <th>Location</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($checkedInUsers as $attendance)
                                                    @php
                                                        $location = json_decode($attendance->check_in_location, true);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $attendance->user->name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $attendance->status === 'late' ? 'warning' : 'success' }}">
                                                                {{ ucfirst($attendance->status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="https://www.google.com/maps?q={{ $location['latitude'] }},{{ $location['longitude'] }}" 
                                                               target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-map-marker-alt"></i> View Location
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="trackUser({{ $attendance->user_id }})">
                                                                <i class="fas fa-route"></i> Track
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No checked in users found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Tracking Map -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Location Tracking Map</h5>
                                </div>
                                <div class="card-body">
                                    <div id="trackingMap" style="height: 400px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Tracking Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="small-box bg-info p-4">
                                <div class="inner">
                                    <h3 id="totalTracks" class="display-4">0</h3>
                                    <p class="h6">Total Tracks</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-map-marker-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-success p-4">
                                <div class="inner">
                                    <h3 id="uniqueUsers" class="display-4">0</h3>
                                    <p class="h6">Unique Users</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-warning p-4">
                                <div class="inner">
                                    <h3 id="totalDistance" class="display-4">0</h3>
                                    <p class="h6">Total Distance (km)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-route fa-2x"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="small-box bg-danger p-4">
                                <div class="inner">
                                    <h3 id="averageSpeed" class="display-4">0</h3>
                                    <p class="h6">Avg Speed (km/h)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-tachometer-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Tracking History -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Location Tracking History</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="trackingTableBody">
                                            <thead>
                                                <tr>
                                                    <th>User</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Type</th>
                                                    <th>Accuracy</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data will be populated by JavaScript -->
                                            </tbody>
                                        </table>
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

<!-- New Location Modal -->
<div class="modal fade" id="newLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="locationForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode" maxlength="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveLocation()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap" async defer></script>
<script>
let map;
let markers = [];
let path = null;

function initMap() {
    // Initialize map with India center
    map = new google.maps.Map(document.getElementById('trackingMap'), {
        zoom: 5,
        center: { lat: 20.5937, lng: 78.9629 },
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    // Load initial data
    loadTrackingData();
    loadLocationStats();
}

function loadTrackingData() {
    const date = document.getElementById('trackingDate').value;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`{{ route('admin.locations.tracks') }}?date=${date}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Clear existing markers and path
        markers.forEach(marker => marker.setMap(null));
        if (path) path.setMap(null);
        markers = [];

        // Update table
        const tableBody = document.getElementById('trackingTableBody').getElementsByTagName('tbody')[0];
        tableBody.innerHTML = '';

        if (!data.data || data.data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No tracking data found</td></tr>';
            return;
        }

        // Add new markers and table rows
        data.data.forEach((track, index) => {
            const position = {
                lat: parseFloat(track.latitude),
                lng: parseFloat(track.longitude)
            };

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                title: `${track.user} - ${track.type}`,
                label: {
                    text: (index + 1).toString(),
                    color: 'white'
                },
                icon: {
                    url: track.type === 'check_in' ? 
                        'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 
                        'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                }
            });

            // Add info window
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div>
                        <strong>${track.user}</strong><br>
                        Point: ${index + 1}<br>
                        Time: ${new Date(track.time).toLocaleTimeString()}<br>
                        Type: ${track.type}<br>
                        Accuracy: ${track.accuracy || 'N/A'}m
                    </div>
                `
            });

            marker.addListener('click', () => {
                infoWindow.open(map, marker);
            });

            markers.push(marker);

            // Add table row
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${track.user}</td>
                <td>${new Date(track.date).toLocaleDateString()}</td>
                <td>${new Date(track.time).toLocaleTimeString()}</td>
                <td>
                    <span class="badge bg-${track.type === 'check_in' ? 'success' : 'danger'}">
                        Point ${index + 1} - ${track.type}
                    </span>
                </td>
                <td>${track.accuracy || 'N/A'}m</td>
                <td>
                    <a href="${track.google_maps_url}" target="_blank" class="btn btn-sm btn-info">
                        <i class="fas fa-map-marker-alt"></i> View Location
                    </a>
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Create path between points
        const pathCoordinates = data.data.map(track => ({
            lat: parseFloat(track.latitude),
            lng: parseFloat(track.longitude)
        }));

        if (pathCoordinates.length > 1) {
            path = new google.maps.Polyline({
                path: pathCoordinates,
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            path.setMap(map);
        }

        // Fit bounds to show all markers
        if (markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            markers.forEach(marker => bounds.extend(marker.getPosition()));
            map.fitBounds(bounds);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const tableBody = document.getElementById('trackingTableBody').getElementsByTagName('tbody')[0];
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading tracking data. Please try again.</td></tr>';
    });
}

function loadLocationStats() {
    const date = document.getElementById('trackingDate').value;
    
    fetch(`{{ route('admin.locations.stats') }}?date=${date}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalTracks').textContent = data.total_tracks;
            document.getElementById('uniqueUsers').textContent = data.unique_users;
            document.getElementById('totalDistance').textContent = data.total_distance.toFixed(2);
            document.getElementById('averageSpeed').textContent = data.average_speed.toFixed(2);
        });
}

function saveLocation() {
    const form = document.getElementById('locationForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.locations.store") }}', {
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
            alert('Error creating location');
        }
    });
}

// Initialize map when the page loads
document.addEventListener('DOMContentLoaded', () => {
    // Check if Google Maps API is loaded
    if (typeof google === 'undefined') {
        console.error('Google Maps API not loaded');
        return;
    }
    
    // Initialize map if not already initialized
    if (!map) {
        initMap();
    }
});

// Update data when date changes
document.getElementById('trackingDate').addEventListener('change', () => {
    loadTrackingData();
    loadLocationStats();
});
</script>
@endpush 