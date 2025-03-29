@extends('layouts.app')

@section('title', 'Salesperson Dashboard')

@section('content')
    <script>
        let events = {!! json_encode($events) !!};
    </script>

    <div class="mb-4">

        <!-- Attendance Section -->
        <div class="attendance-container p-4 rounded shadow-sm">
            <h3 class="h5 text-center mb-4 text-dark fw-bold pb-4" style="border-bottom: 2px solid black;">Attendance</h3>

            <div class="row justify-content-center">
                <!-- Check In Time -->
                <div class="col-md-4 col-12 mb-3">
                    <div class="info-card check-in-card shadow-sm text-center p-3">
                        <p class="small fw-semibold text-dark">Check In Time</p>
                        <p class="h5 fw-bold text-dark" id="checkInTime">
                            @if ($attendance && $attendance->check_in_time)
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                            @else
                                --:--
                            @endif
                        </p>
                        @if ($attendance && $attendance->status === 'late')
                            <span class="badge bg-warning text-dark">Late</span>
                        @endif
                    </div>
                </div>

                <!-- Check Out Time -->
                <div class="col-md-4 col-12 mb-3">
                    <div class="info-card check-out-card shadow-sm text-center p-3">
                        <p class="small fw-semibold text-dark">Check Out Time</p>
                        <p class="h5 fw-bold text-dark" id="checkOutTime">
                            @if ($attendance && $attendance->check_out_time)
                                {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                            @else
                                --:--
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Working Hours -->
                <div class="col-md-4 col-12 mb-3">
                    <div class="info-card working-hours-card shadow-sm text-center p-3">
                        <p class="small fw-semibold text-dark">Working Hours</p>
                        <p class="h5 fw-bold text-dark">
                            @if ($attendance && $attendance->check_in_time && $attendance->check_out_time)
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->diff(\Carbon\Carbon::parse($attendance->check_out_time))->format('%H:%I') }}
                                hrs
                            @else
                                --
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Location -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="info-card location-card shadow-sm text-center p-3">
                        <p class="small fw-semibold text-dark">Current Location</p>
                        <p class="h6 text-dark fw-bold" id="currentLocation">
                            Loading location...
                        </p>
                        <div id="locationMap" style="height: 200px; width: 100%; margin-top: 10px;"></div>
                    </div>
                </div>
            </div>

            <!-- Check In / Check Out Buttons -->
            <div class="d-flex justify-content-center mt-4">
                @if (!$attendance || !$attendance->check_in_time)
                    <button onclick="checkIn()" class="btn btn-primary-soft me-2">
                        <i class="fas fa-sign-in-alt me-2"></i> Check In
                    </button>
                @elseif(!$attendance->check_out_time)
                    <button onclick="checkOut()" class="btn btn-danger-soft">
                        <i class="fas fa-sign-out-alt me-2"></i> Check Out
                    </button>
                @endif
            </div>

            <!-- Late Reason Modal -->
            <div class="modal fade" id="lateReasonModal" tabindex="-1" aria-labelledby="lateReasonModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="lateReasonModalLabel">Late Attendance Reason</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="lateReasonForm">
                                <div class="mb-3">
                                    <label for="lateReason" class="form-label">Please provide reason for being late</label>
                                    <textarea class="form-control" id="lateReason" rows="3" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="submitLateReason()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- Stats Overview -->
        <div class="row g-4 mt-4">
            <div class="col-md-3">
                <div class="bg-white rounded shadow p-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-circle bg-light text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <p class="small text-muted">Total Leads</p>
                            <p class="h5">{{ $totalLeads }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-white rounded shadow p-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-circle bg-light text-success">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <p class="small text-muted">Monthly Sales</p>
                            <p class="h5">₹{{ number_format($monthlySales, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-white rounded shadow p-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-circle bg-light text-warning">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <p class="small text-muted">Today's Meetings</p>
                            <p class="h5">{{ $todayMeetings }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="bg-white rounded shadow p-4">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-circle bg-light text-info">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                        <div class="ms-3">
                            <p class="small text-muted">Target Achievement</p>
                            <p class="h5">{{ $targetAchievement }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Kanban Board -->
        <div class="bg-white rounded shadow mt-4">
            <div class="p-4 border-bottom">
                <h3 class="h6">Lead Pipeline</h3>

                <div class="d-flex">
                    <button onclick="openLeadModal()" class="btn btn-primary me-2">
                        + Add New Lead
                    </button>
                    <button onclick="openModal()" class="btn btn-success">
                        + Add New Task
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div class="d-flex overflow-auto">
                    @foreach ($leadStatuses as $status)
                        <div class="flex-shrink-0 me-3" style="width: 300px;">
                            <div class="bg-light rounded p-3">
                                <h4 class="small text-muted mb-3">{{ $status->name }}</h4>
                                <div class="mb-3" id="status-{{ $status->id }}">
                                    @foreach ($status->leads as $lead)
                                        <div class="bg-white rounded shadow p-3 mb-2 cursor-move"
                                            data-lead-id="{{ $lead->id }}">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h5 class="font-weight-bold text-dark">{{ $lead->name }}</h5>
                                                <span
                                                    class="small text-muted">{{ $lead->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <p class="small text-muted mb-3">{{ Str::limit($lead->description, 100) }}</p>
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <a href="tel:{{ $lead->phone }}" class="text-primary me-2">
                                                        <i class="fas fa-phone"></i>
                                                    </a>
                                                    <a href="mailto:{{ $lead->email }}" class="text-success me-2">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    <a href="https://wa.me/{{ $lead->phone }}" target="_blank"
                                                        class="text-success">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <button onclick="editLead({{ $lead->id }})"
                                                        class="text-primary me-2">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button onclick="deleteLead({{ $lead->id }})"
                                                        class="text-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Lead Modal -->
        <div class="modal fade" id="leadModal" tabindex="-1" aria-labelledby="leadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Increased modal width -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leadModalLabel">Add New Lead</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addLeadForm" class="needs-validation" novalidate>
                            @csrf
                            <input type="hidden" name="lead_id" id="lead_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" name="phone" id="phone" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label">Company</label>
                                    <input type="text" name="company" id="company" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="source" class="form-label">Source</label>
                                    <select name="source" id="source" class="form-select" required>
                                        <option value="">Select Source</option>
                                        <option value="website">Website</option>
                                        <option value="referral">Referral</option>
                                        <option value="social">Social Media</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="">Select Status</option>
                                        @foreach ($leadStatuses as $status)
                                            <option value="1">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expected_amount" class="form-label">Expected Amount</label>
                                    <input type="number" name="expected_amount" id="expected_amount"
                                        class="form-control" min="0" step="0.01" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Lead</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Task Modal -->
        <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addTaskForm" class="space-y-4">
                            @csrf
                            <div class="mb-3">
                                <label for="taskTitle" class="form-label">Title</label>
                                <input type="text" name="title" id="taskTitle" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="taskDescription" class="form-label">Description</label>
                                <textarea name="description" id="taskDescription" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="dueDate" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="dueDate" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Add Task</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Calendar Navigation -->
        <div class="bg-white rounded shadow mt-4 text-center">
            <!-- Calendar Header -->
            <div class="p-4 border-bottom d-flex flex-column align-items-center">
                <h3 id="calendarTitle" class="h5 d-flex align-items-center fw-bold">
                    <i class="fas fa-calendar-alt text-primary me-2"></i> <!-- Calendar Icon -->
                    <span>March 2025</span> <!-- Month & Year Display -->
                </h3>
                <div class="btn-group mt-3" role="group">
                    <button id="prevMonth" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-chevron-left"></i> Prev
                    </button>
                    <button id="monthView" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-th-large"></i> Month
                    </button>
                    <button id="weekView" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-calendar-week"></i> Week
                    </button>
                    <button id="dayView" class="btn btn-outline-dark custom-btn">
                        <i class="fas fa-calendar-day"></i> Day
                    </button>
                    <button id="nextMonth" class="btn btn-outline-dark custom-btn">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <!-- Calendar Body -->
            <div class="month-calendar">
                <div class="month-grid" id="calendarGrid">
                    <!-- Days will be dynamically added here -->
                </div>
            </div>
        </div>



    </div>
    <!-- Event Popup -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Title:</strong> <span id="eventTitle"></span></p>
                    <p><strong>Date:</strong> <span id="eventDate"></span></p>
                    <p><strong>Description:</strong> <span id="eventDescription"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Leads Performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="leadsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Attendance Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Lead Achievement</h5>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-primary lead-achievement" role="progressbar" style="width: 0%">
                            <span class="lead-achievement-text">0%</span>
                        </div>
                    </div>
                    <small class="text-muted">Monthly Lead Target Achievement</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales Achievement</h5>
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-success sales-achievement" role="progressbar" style="width: 0%">
                            <span class="sales-achievement-text">0%</span>
                        </div>
                    </div>
                    <small class="text-muted">Monthly Sales Target Achievement</small>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="{{ asset('js/dashboard.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check in function with improved error handling
                function checkIn() {
                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            $.ajax({
                                url: '/salesperson/attendance/checkin',
                                method: 'POST',
                                data: {
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    address: 'Current Location',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        document.getElementById('checkInTime').textContent =
                                            response.time;
                                        if (response.status === 'late') {
                                            showLateReasonModal();
                                        }
                                        // Start location tracking after successful check-in
                                        isTracking = true;
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: response.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: response.message || 'Failed to check in'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Failed to check in. Please try again.'
                                    });
                                }
                            });
                        }, function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to get location. Please enable location services.'
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Geolocation is not supported by your browser'
                        });
                    }
                }

                // Check out function with improved error handling
                function checkOut() {
                    if ("geolocation" in navigator) {
                        // Stop location tracking before checking out
                        isTracking = false;

                        navigator.geolocation.getCurrentPosition(function(position) {
                            $.ajax({
                                url: '/salesperson/attendance/checkout',
                                method: 'POST',
                                data: {
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    address: 'Current Location',
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        document.getElementById('checkOutTime').textContent =
                                            response.time;
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: response.message,
                                            timer: 1500,
                                            showConfirmButton: false
                                        });
                                        location.reload();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: response.message || 'Failed to check out'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Failed to check out. Please try again.'
                                    });
                                }
                            });
                        }, function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to get location. Please enable location services.'
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Geolocation is not supported by your browser'
                        });
                    }
                }


            });

            // location logics start

            let map;
            let marker;
            let locationUpdateInterval;
            let isTracking = false;
            let lastLocationUpdate = null;

            // Initialize map
            function initMap() {
                map = new google.maps.Map(document.getElementById('locationMap'), {
                    zoom: 15,
                    center: {
                        lat: 0,
                        lng: 0
                    }
                });
                marker = new google.maps.Marker({
                    map: map,
                    position: {
                        lat: 0,
                        lng: 0
                    }
                });
            }

            // Update location with error handling and retry logic
            function updateLocation(position) {
                try {
                    const {
                        latitude,
                        longitude,
                        speed,
                        accuracy
                    } = position.coords;
                    const location = {
                        lat: latitude,
                        lng: longitude
                    };

                    map.setCenter(location);
                    marker.setPosition(location);

                    // Update location display
                    document.getElementById('currentLocation').textContent =
                        `Latitude: ${latitude.toFixed(6)}, Longitude: ${longitude.toFixed(6)}`;

                    // Only record location if tracking is active and enough time has passed
                    if (isTracking && (!lastLocationUpdate || Date.now() - lastLocationUpdate > 30000)) {
                        recordLocation(latitude, longitude, speed, accuracy);
                        lastLocationUpdate = Date.now();
                    }
                } catch (error) {
                    console.error('Error updating location:', error);
                    document.getElementById('currentLocation').textContent =
                        'Error updating location. Retrying...';
                }
            }

            // Record location to server with retry logic
            function recordLocation(latitude, longitude, speed, accuracy) {
                $.ajax({
                    url: '/location/tracks',
                    method: 'POST',
                    data: {
                        latitude: latitude,
                        longitude: longitude,
                        speed: speed,
                        accuracy: accuracy,
                        address: 'Current Location',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (!response.success) {
                            console.error('Failed to record location:', response.message);
                            // Retry after 5 seconds
                            setTimeout(() => recordLocation(latitude, longitude, speed, accuracy), 5000);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error recording location:', xhr);
                        // Retry after 5 seconds
                        setTimeout(() => recordLocation(latitude, longitude, speed, accuracy), 5000);
                    }
                });
            }

            // Start location tracking with improved error handling
            function startLocationTracking() {
                if ("geolocation" in navigator) {
                    const options = {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    };

                    // Get initial position
                    navigator.geolocation.getCurrentPosition(
                        updateLocation,
                        handleLocationError,
                        options
                    );

                    // Start watching position
                    locationUpdateInterval = navigator.geolocation.watchPosition(
                        updateLocation,
                        handleLocationError,
                        options
                    );
                } else {
                    document.getElementById('currentLocation').textContent =
                        'Geolocation is not supported by your browser';
                }
            }

            // Handle location errors
            function handleLocationError(error) {
                let errorMessage = 'Error getting location: ';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Permission denied. Please enable location services.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out.';
                        break;
                    default:
                        errorMessage += error.message;
                }
                console.error('Geolocation error:', error);
                document.getElementById('currentLocation').textContent = errorMessage;
            }

            // locations logics close



            // Show late reason modal
            function showLateReasonModal() {
                const modal = new bootstrap.Modal(document.getElementById('lateReasonModal'));
                modal.show();
            }

            // Submit late reason
            function submitLateReason() {
                const reason = document.getElementById('lateReason').value;
                if (!reason) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please provide a reason for being late'
                    });
                    return;
                }

                $.ajax({
                    url: '/salesperson/attendance/late-reason',
                    method: 'POST',
                    data: {
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('lateReasonModal'));
                            modal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Late reason submitted successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to submit late reason'
                        });
                    }
                });
            }

            // Initialize when document is ready
            document.addEventListener('DOMContentLoaded', function() {
                initMap();
                startLocationTracking();
            });

            function openModal(modalId) {
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            }

            function closeModal(modalId) {
                const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                modal.hide();
            }

            // lead open management
            function openLeadModal(leadId = null) {
                const modal = document.getElementById('leadModal');
                const form = document.getElementById('addLeadForm');
                const modalTitle = document.getElementById('leadModalLabel');
                const leadIdField = document.getElementById('lead_id');

                if (leadId) {
                    // Edit mode
                    modalTitle.textContent = 'Edit Lead';
                    fetch(`/salesperson/leads/${leadId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok: ' + response.statusText);
                            }
                            return response.json();
                        })
                        .then(lead => {
                            form.name.value = lead.name;
                            form.phone.value = lead.phone;
                            form.email.value = lead.email;
                            form.company.value = lead.company;
                            form.description.value = lead.additional_info;
                            form.source.value = lead.source;
                            form.expected_amount.value = lead.expected_amount;
                            form.notes.value = lead.notes;
                            form.status.value = lead.status_id;

                            form.dataset.leadId = leadId;
                            leadIdField.value = leadId;
                        })
                        .catch(error => {
                            console.error('Error fetching lead:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to load lead data. Please try again.'
                            });
                        });
                } else {
                    // Create mode
                    modalTitle.textContent = 'Add New Lead';
                    form.reset(); // Reset the form for new lead
                    delete form.dataset.leadId; // Ensure the lead ID is removed
                }

                new bootstrap.Modal(modal).show();
            }



            function submitLeadForm(event) {
                event.preventDefault();
                const form = event.target;
                const leadId = form.dataset.leadId;
                const url = leadId ? `/salesperson/leads/${leadId}` : '/salesperson/leads';
                const method = leadId ? 'PUT' : 'POST';

                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                // Add CSRF token
                data._token = '{{ csrf_token() }}';

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to save lead'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to save lead. Please try again.'
                        });
                    });

            }

            function deleteLead(leadId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/salesperson/leads/${leadId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: result.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: result.message || 'Failed to delete lead'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete lead. Please try again.'
                                });
                            });
                    }
                });
            }


            // Lead Form Submission
            document.getElementById('addLeadForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const form = this;
                const leadId = document.getElementById('lead_id').value;
                const url = '/salesperson/leads';
                const method = 'POST';

                const formData = new FormData(form);

                fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(Object.fromEntries(formData.entries()))
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to save lead'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to save lead. Please try again.'
                        });
                    });
            });


            // Task Form Submission
            document.getElementById('addTaskForm').addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);

                fetch('/salesperson/tasks', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message || 'Failed to add task'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to add task. Please try again.'
                        });
                    });
            });


            // Initialize Dragula for lead status changes
            const containers = document.querySelectorAll('[id^="status-"]');
            dragula(containers, {
            moves: function(el) {
                return el.classList.contains('cursor-move');
            },
            accepts: function(el, target) {
                return target.id !== el.parentNode.id;
            },
            direction: 'horizontal',
            revertOnSpill: true
            }).on('drop', function(el, target) {
                const leadId = el.dataset.leadId;
                const newStatusId = target.id.replace('status-', '');

                fetch(`/salesperson/leads/${leadId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status_id: newStatusId,
                        _token: '{{ csrf_token() }}'
                    })
                })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message || 'Failed to update lead status'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update lead status. Please try again.'
                });
            });
            });

            // Event Modal
            function handleEventClick(info) {
                const eventId = info.event.id;
                const eventType = eventId.split('-')[0]; // 'lead' or 'meeting'

                if (eventType === 'lead') {
                    const leadId = eventId.split('-')[1];
                    fetch(`/salesperson/leads/${leadId}`)
                        .then(response => response.json())
                        .then(lead => {
                            document.getElementById('eventTitle').textContent = lead.name;
                            document.getElementById('eventDate').textContent = new Date(lead.created_at)
                                .toLocaleDateString();
                            document.getElementById('eventDescription').innerHTML = `
                                <p><strong>Company:</strong> ${lead.company}</p>
                                <p><strong>Phone:</strong> ${lead.phone}</p>
                                <p><strong>Email:</strong> ${lead.email}</p>
                                <p><strong>Expected Amount:</strong> ₹${lead.expected_value}</p>
                                <p><strong>Status:</strong> ${lead.status.name}</p>
                                <p><strong>Description:</strong> ${lead.description}</p>
                            `;

                            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                            modal.show();
                        });
                } else if (eventType === 'meeting') {
                    const meetingId = eventId.split('-')[1];
                    fetch(`/salesperson/meetings/${meetingId}`)
                        .then(response => response.json())
                        .then(meeting => {
                            document.getElementById('eventTitle').textContent = meeting.title;
                            document.getElementById('eventDate').textContent = new Date(meeting.meeting_date)
                                .toLocaleString();
                            document.getElementById('eventDescription').innerHTML = `
                                <p><strong>Location:</strong> ${meeting.location}</p>
                                <p><strong>Attendees:</strong> ${meeting.attendees.join(', ')}</p>
                                <p><strong>Status:</strong> ${meeting.status}</p>
                                <p><strong>Description:</strong> ${meeting.description}</p>
                                ${meeting.notes ? `<p><strong>Notes:</strong> ${meeting.notes}</p>` : ''}
                            `;

                            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                            modal.show();
                        });
                }
            }

            // Performance Charts
            document.addEventListener('DOMContentLoaded', function() {
                // Leads Chart
                const leadsCtx = document.getElementById('leadsChart').getContext('2d');
                new Chart(leadsCtx, {
                    type: 'line',
                    data: {
                        labels: @json($performanceData['labels']),
                        datasets: [{
                            label: 'Leads',
                            data: @json($performanceData['leads']),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Attendance Chart
                const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
                new Chart(attendanceCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($performanceData['labels']),
                        datasets: [{
                                label: 'Present',
                                data: @json($performanceData['attendance']['present']),
                                backgroundColor: '#10B981',
                                borderColor: '#059669',
                                borderWidth: 1
                            },
                            {
                                label: 'Late',
                                data: @json($performanceData['attendance']['late']),
                                backgroundColor: '#F59E0B',
                                borderColor: '#D97706',
                                borderWidth: 1
                            },
                            {
                                label: 'Absent',
                                data: @json($performanceData['attendance']['absent']),
                                backgroundColor: '#EF4444',
                                borderColor: '#DC2626',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Update achievement indicators if available
                @if ($performanceData['achievements'])
                    const achievements = @json($performanceData['achievements']);
                    document.querySelector('.lead-achievement').style.width = achievements.leads + '%';
                    document.querySelector('.sales-achievement').style.width = achievements.sales + '%';
                    document.querySelector('.lead-achievement-text').textContent = Math.round(achievements.leads) + '%';
                    document.querySelector('.sales-achievement-text').textContent = Math.round(achievements.sales) +
                        '%';
                @endif
            });
        </script>
    @endpush
@endsection
