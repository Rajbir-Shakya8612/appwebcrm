@extends('layouts.salesperson')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Attendance Status Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Today's Attendance</h5>
                </div>
                <div class="card-body">
                    <div id="attendance-status">
                        <div class="text-center">
                            <h3 class="mb-4">Loading...</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button id="check-in-btn" class="btn btn-success btn-block" style="display: none;">
                            Check In
                        </button>
                        <button id="check-out-btn" class="btn btn-danger btn-block" style="display: none;">
                            Check Out
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Map -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Live Location</h5>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Calendar -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<script>
let map;
let marker;
let watchId;
let calendar;

// Initialize Google Map
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: 0, lng: 0 }
    });
}

// Get current location
function getCurrentLocation() {
    if (navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(
            position => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                if (!marker) {
                    marker = new google.maps.Marker({
                        position: pos,
                        map: map
                    });
                } else {
                    marker.setPosition(pos);
                }

                map.setCenter(pos);
                updateLocation(pos);
            },
            error => {
                console.error('Error getting location:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    }
}

// Update location on server
function updateLocation(position) {
    fetch('/location/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            latitude: position.lat,
            longitude: position.lng,
            accuracy: 100 // You can get this from position.coords.accuracy
        })
    });
}

// Initialize Calendar
function initCalendar() {
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/attendance/calendar-events',
        eventDidMount: function(info) {
            // Add tooltips or custom styling based on attendance status
            if (info.event.extendedProps.status === 'present') {
                info.el.classList.add('bg-success');
            } else if (info.event.extendedProps.status === 'late') {
                info.el.classList.add('bg-warning');
            } else if (info.event.extendedProps.status === 'absent') {
                info.el.classList.add('bg-danger');
            }
        }
    });
    calendar.render();
}

// Check attendance status
function checkAttendanceStatus() {
    fetch('/attendance/status')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('attendance-status');
            const checkInBtn = document.getElementById('check-in-btn');
            const checkOutBtn = document.getElementById('check-out-btn');

            if (data.attendance) {
                statusDiv.innerHTML = `
                    <div class="text-center">
                        <h3>Status: ${data.attendance.status}</h3>
                        <p>Check In: ${new Date(data.attendance.check_in).toLocaleTimeString()}</p>
                        ${data.attendance.check_out ? 
                            `<p>Check Out: ${new Date(data.attendance.check_out).toLocaleTimeString()}</p>` : 
                            ''}
                    </div>
                `;

                checkInBtn.style.display = 'none';
                checkOutBtn.style.display = data.canCheckOut ? 'block' : 'none';
            } else {
                statusDiv.innerHTML = `
                    <div class="text-center">
                        <h3>Not Checked In</h3>
                    </div>
                `;
                checkInBtn.style.display = 'block';
                checkOutBtn.style.display = 'none';
            }
        });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    getCurrentLocation();
    initCalendar();
    checkAttendanceStatus();

    // Check status every minute
    setInterval(checkAttendanceStatus, 60000);

    // Check In Button
    document.getElementById('check-in-btn').addEventListener('click', function() {
        fetch('/attendance/checkin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                location: marker ? {
                    lat: marker.getPosition().lat(),
                    lng: marker.getPosition().lng()
                } : null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkAttendanceStatus();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        });
    });

    // Check Out Button
    document.getElementById('check-out-btn').addEventListener('click', function() {
        fetch('/attendance/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                location: marker ? {
                    lat: marker.getPosition().lat(),
                    lng: marker.getPosition().lng()
                } : null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                checkAttendanceStatus();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
#map {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.fc-event {
    cursor: pointer;
}

.fc-event:hover {
    opacity: 0.8;
}
</style>
@endpush 