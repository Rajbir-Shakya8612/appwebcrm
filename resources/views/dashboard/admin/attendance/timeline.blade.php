@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Salesperson Location Timeline</h5>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Salesperson</label>
                                <select id="salesperson-select" class="form-control">
                                    <option value="">All Salespersons</option>
                                    @foreach($salespersons as $salesperson)
                                        <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Date</label>
                                <input type="date" id="date-select" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button id="filter-btn" class="btn btn-primary btn-block">Filter</button>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div id="map" style="height: 500px;"></div>

                    <!-- Timeline -->
                    <div class="timeline mt-4">
                        <div id="timeline-container">
                            <!-- Timeline items will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk"></script>
<script>
let map;
let markers = [];
let polyline;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: 0, lng: 0 }
    });
}

function clearMap() {
    markers.forEach(marker => marker.setMap(null));
    markers = [];
    if (polyline) {
        polyline.setMap(null);
        polyline = null;
    }
}

function updateMap(tracks) {
    clearMap();
    
    if (tracks.length === 0) return;
    
    const bounds = new google.maps.LatLngBounds();
    const path = [];
    
    tracks.forEach((track, index) => {
        const position = {
            lat: parseFloat(track.latitude),
            lng: parseFloat(track.longitude)
        };
        
        const marker = new google.maps.Marker({
            position: position,
            map: map,
            title: new Date(track.timestamp).toLocaleString(),
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: '#4285F4',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2
            }
        });
        
        markers.push(marker);
        bounds.extend(position);
        path.push(position);
        
        // Add info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-2">
                    <strong>Time:</strong> ${new Date(track.timestamp).toLocaleString()}<br>
                    <strong>Address:</strong> ${track.address || 'N/A'}<br>
                    <strong>Accuracy:</strong> ${track.accuracy}m
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
    });
    
    // Draw polyline
    if (path.length > 1) {
        polyline = new google.maps.Polyline({
            path: path,
            geodesic: true,
            strokeColor: '#4285F4',
            strokeOpacity: 0.8,
            strokeWeight: 3,
            map: map
        });
    }
    
    // Fit bounds
    map.fitBounds(bounds);
}

function updateTimeline(tracks) {
    const container = document.getElementById('timeline-container');
    container.innerHTML = '';
    
    tracks.forEach(track => {
        const time = new Date(track.timestamp);
        const item = document.createElement('div');
        item.className = 'timeline-item';
        item.innerHTML = `
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <div class="timeline-header">
                    <span class="time">${time.toLocaleTimeString()}</span>
                    <span class="date">${time.toLocaleDateString()}</span>
                </div>
                <div class="timeline-body">
                    <p><strong>Location:</strong> ${track.address || 'N/A'}</p>
                    <p><strong>Accuracy:</strong> ${track.accuracy}m</p>
                </div>
            </div>
        `;
        container.appendChild(item);
    });
}

function loadTracks() {
    const salespersonId = document.getElementById('salesperson-select').value;
    const date = document.getElementById('date-select').value;
    
    fetch(`/admin/location-tracks?salesperson_id=${salespersonId}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMap(data.tracks);
                updateTimeline(data.tracks);
            }
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Load initial data
    loadTracks();
    
    // Filter button click
    document.getElementById('filter-btn').addEventListener('click', loadTracks);
});
</script>
@endpush

@push('styles')
<style>
#map {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
    transform: translateX(-50%);
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    width: 100%;
}

.timeline-marker {
    position: absolute;
    left: 50%;
    top: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #4285F4;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #4285F4;
    transform: translateX(-50%);
}

.timeline-content {
    position: relative;
    width: calc(50% - 30px);
    padding: 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-item:nth-child(odd) .timeline-content {
    margin-left: auto;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    color: #6c757d;
}

.timeline-body {
    color: #212529;
}

.timeline-body p {
    margin-bottom: 5px;
}
</style>
@endpush 