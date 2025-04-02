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
                    <!-- Filters -->
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

                    <div class="row">
                        <!-- Map Column -->
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-body p-0">
                                    <div id="map" style="height: 600px; border-radius: 8px;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Column -->
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="timeline-header">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Timeline View</h6>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeTimelineView('day')">Day</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeTimelineView('week')">Week</button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Timeline Scale -->
                                    <div class="timeline-scale">
                                        <div class="time-markers d-none">
                                            @for($hour = 0; $hour < 24; $hour++)
                                                <div class="time-marker">{{ sprintf('%02d:00', $hour) }}</div>
                                            @endfor
                                        </div>
                                    </div>

                                    <!-- Timeline Content -->
                                    <div class="timeline-content" id="timeline-container">
                                        <!-- Timeline items will be added here dynamically -->
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

@push('styles')
<style>
#map {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-scale {
    position: relative;
    margin-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 10px;
}

.time-markers {
    display: flex;
    justify-content: space-between;
    position: relative;
}

.time-marker {
    font-size: 12px;
    color: #6c757d;
    flex: 1;
    text-align: center;
    position: relative;
}

.time-marker::before {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    height: 5px;
    width: 1px;
    background-color: #e9ecef;
}

.timeline-content {
    position: relative;
    height: 480px;
    overflow-y: auto;
}

.timeline-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    border-radius: 4px;
    background: #f8f9fa;
    position: relative;
}

.timeline-item:hover {
    background: #e9ecef;
}

.timeline-item-content {
    flex: 1;
    padding-left: 15px;
}

.timeline-item-time {
    font-weight: bold;
    color: #495057;
}

.timeline-item-details {
    font-size: 13px;
    color: #6c757d;
}

.timeline-item-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4285F4;
    position: absolute;
    left: -6px;
    top: 50%;
    transform: translateY(-50%);
}

.timeline-item.check-in .timeline-item-marker {
    background: #28a745;
}

.timeline-item.check-out .timeline-item-marker {
    background: #dc3545;
}

.timeline-item-line {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-header {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

/* Custom scrollbar for timeline */
.timeline-content::-webkit-scrollbar {
    width: 6px;
}

.timeline-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.timeline-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.timeline-content::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
<script>
let map;
let markers = [];
let polyline;
let currentView = 'day';

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: { lat: 20.5937, lng: 78.9629 },
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
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
            title: new Date(track.time).toLocaleString(),
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: track.type === 'check_in' ? '#28a745' : '#dc3545',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2
            }
        });
        
        markers.push(marker);
        bounds.extend(position);
        path.push(position);
        
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="p-3">
                    <h6 class="mb-2">${track.user}</h6>
                    <p class="mb-1"><strong>Time:</strong> ${new Date(track.time).toLocaleTimeString()}</p>
                    <p class="mb-1"><strong>Type:</strong> ${track.type}</p>
                    <p class="mb-1"><strong>Address:</strong> ${track.address || 'N/A'}</p>
                    <p class="mb-0"><strong>Accuracy:</strong> ${track.accuracy}m</p>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
    });
    
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
    
    map.fitBounds(bounds);
}

function updateTimeline(tracks) {
    const container = document.getElementById('timeline-container');
    container.innerHTML = '';
    
    if (tracks.length === 0) {
        container.innerHTML = '<div class="text-center p-4">No tracking data available</div>';
        return;
    }

    // Add the vertical timeline line
    container.innerHTML = '<div class="timeline-item-line"></div>';
    
    tracks.forEach(track => {
        const time = new Date(track.time);
        const hours = time.getHours();
        const minutes = time.getMinutes();
        const leftPosition = (hours * 60 + minutes) / (24 * 60) * 100;
        
        const item = document.createElement('div');
        item.className = `timeline-item ${track.type}`;
        item.style.marginLeft = `${leftPosition}%`;
        item.innerHTML = `
            <div class="timeline-item-marker"></div>
            <div class="timeline-item-content">
                <div class="timeline-item-time">${time.toLocaleTimeString()}</div>
                <div class="timeline-item-details">
                    ${track.user}<br>
                    ${track.type === 'check_in' ? 'Checked In' : 'Checked Out'}<br>
                    Accuracy: ${track.accuracy}m
                </div>
            </div>
        `;
        
        // Add click event to highlight corresponding marker on map
        item.addEventListener('click', () => {
            const marker = markers[tracks.indexOf(track)];
            if (marker) {
                map.panTo(marker.getPosition());
                map.setZoom(15);
                google.maps.event.trigger(marker, 'click');
            }
        });
        
        container.appendChild(item);
    });
}

function changeTimelineView(view) {
    currentView = view;
    loadTracks(); // Reload data with new view
}

function loadTracks() {
    const salespersonId = document.getElementById('salesperson-select').value;
    const date = document.getElementById('date-select').value;
    
    fetch(`{{ route('admin.locations.tracks') }}?salesperson_id=${salespersonId}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMap(data.data);
                updateTimeline(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading tracks:', error);
            document.getElementById('timeline-container').innerHTML = 
                '<div class="text-center p-4 text-danger">Error loading tracking data</div>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    loadTracks();
    
    document.getElementById('filter-btn').addEventListener('click', loadTracks);
    
    // Add responsiveness to map
    window.addEventListener('resize', function() {
        if (markers.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            markers.forEach(marker => bounds.extend(marker.getPosition()));
            map.fitBounds(bounds);
        }
    });
});
</script>
@endpush

@endsection