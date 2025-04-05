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
                        @if ($attendance && $attendance->status == 'late')
                            <span class="badge bg-warning text-dark" id="checkInStatus">Late</span>
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
                        <p class="h5 fw-bold text-dark" id="workingHours">
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


            <div class="d-flex justify-content-center mt-4">
                @if (!$attendance || !$attendance->check_in_time)
                    <button onclick="checkIn()" class="btn btn-primary-soft me-2" id="checkInButton">
                        <i class="fas fa-sign-in-alt me-2"></i> Check In
                    </button>
                @endif

                @if ($attendance && $attendance->check_in_time && !$attendance->check_out_time)
                    <button onclick="checkOut()" class="btn btn-danger-soft" id="checkOutButton">
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
                                    <label for="lateReason" class=" form-label text-start d-block">Please provide reason for
                                        being late</label>
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
            </div>
            <div class="kanban-board">
                @foreach ($leadStatuses as $status)
                    <div class="kanban-column" data-status-id="{{ $status->id }}">
                        <div class="kanban-column-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h4 class="kanban-column-title me-2">{{ $status->name }}</h4>
                                <button class="btn-add-lead" data-bs-toggle="modal" data-bs-target="#newLeadModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <span class="column-lead-count">{{ $status->leads->count() }}</span>
                        </div>
                        <div class="kanban-cards" data-status-id="{{ $status->id }}">
                            @foreach ($status->leads as $lead)
                                <div class="kanban-card status-{{ strtolower($status->name) }}" data-lead-id="{{ $lead->id }}">
                                    <div class="card-header">
                                        <div>
                                            <h5 class="card-title">{{ $lead->name }}</h5>
                                            <div class="card-company">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $lead->company }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-info">
                                        @if($lead->phone)
                                            <div class="info-row">
                                                <i class="fas fa-phone"></i>
                                                {{ $lead->phone }}
                                            </div>
                                        @endif
                                        @if($lead->email)
                                            <div class="info-row">
                                                <i class="fas fa-envelope"></i>
                                                {{ $lead->email }}
                                            </div>
                                        @endif
                                        @if($lead->address)
                                            <div class="info-row">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ Str::limit($lead->address, 50) }}
                                            </div>
                                        @endif
                                        @if($lead->notes)
                                            <div class="info-row">
                                                <i class="fas fa-sticky-note"></i>
                                                {{ Str::limit($lead->notes, 100) }}
                                            </div>
                                        @endif
                                        @if($lead->follow_up_date)
                                            <div class="follow-up-date">
                                                <i class="fas fa-calendar-alt"></i>
                                                Follow-up: {{ \Carbon\Carbon::parse($lead->follow_up_date)->format('M d, Y') }}
                                            </div>
                                        @endif
                                        <div class="card-rating">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= ($lead->rating ?? 0) ? 'star' : 'star-empty' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="contact-icons">
                                        <a href="tel:{{ $lead->phone }}" class="contact-icon phone">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                        <a href="mailto:{{ $lead->email }}" class="contact-icon email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        <?php
                                            $phone = $lead->phone;
                                           
                                            // Ensure phone starts with +91
                                            if (!preg_match('/^\+91/', $phone)) {
                                                $phone = '+91' . ltrim($phone, '+');
                                            }

                                           
                                        ?>

                                    <a href="https://wa.me/<?= $phone ?>" class="contact-icon whatsapp" target="_blank">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>

                                        <div class="d-flex">
                                            <!-- Eye Icon Button with btn-info background and equal sizing -->
                                           <button class="btn btn-sm btn-light me-1 d-flex align-items-center justify-content-center" 
                                                    style="width: 32px; height: 32px;" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewLeadModal" 
                                                    onclick="viewLeadDetails({{ $lead->id }})">
                                                <i class="fas fa-eye text-info"></i>
                                            </button>


                                            <!-- Edit Icon -->
                                            <button class="btn btn-sm btn-light me-1 d-flex align-items-center justify-content-center" 
                                                    style="width: 32px; height: 32px;" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editLeadModal" 
                                                    onclick="editLeadDetails({{ $lead->id }})">
                                                <i class="fas fa-edit text-primary"></i>
                                            </button>

                                            <!-- Delete Icon -->
                                            <button class="btn btn-sm btn-light d-flex align-items-center justify-content-center" 
                                                    style="width: 32px; height: 32px;" 
                                                    onclick="deleteLeadConfirm({{ $lead->id }})">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

            @include('components.salesperson.lead-form')
        


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

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Notifications & Reminders</h3>
            </div>
            <div class="card-body">
                <!-- Follow-up Reminders -->
                <div class="mb-4">
                    <h5 class="mb-3">Follow-up Reminders</h5>
                    @php
                        $pendingFollowUps = \App\Models\Lead::where('user_id', auth()->id())
                            ->where('follow_up_date', '<=', now()->addDays(7))
                            ->where('follow_up_date', '>=', now())
                            ->orderBy('follow_up_date')
                            ->get();
                    @endphp
                    
                    @if($pendingFollowUps->count() > 0)
                        <div class="list-group">
                            @foreach($pendingFollowUps as $lead)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $lead->name }}</h6>
                                            <small class="text-muted">Follow-up: {{ $lead->follow_up_date->format('M d, Y') }}</small>
                                        </div>
                                        <a href="{{ route('salesperson.leads.show', $lead) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No pending follow-ups</p>
                    @endif
                </div>

                <!-- Notifications -->
                <div>
                    <h5 class="mb-3">Recent Notifications</h5>
                    @php
                        $notifications = auth()->user()->notifications()
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ $notification->read_at ? '' : 'list-group-item-primary' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $notification->title }}</h6>
                                            <small class="text-muted">{{ $notification->message }}</small>
                                        </div>
                                        @if(!$notification->read_at)
                                            <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    Mark as Read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No new notifications</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script src="{{ asset('js/dashboard.js') }}"></script>
        <script src="{{ asset('js/kanban.js') }}"></script>
        <script>
            // Location tracking variables
            let map, marker, locationUpdateInterval, isTracking = false,
                lastLocationUpdate = null;
            let retryCount = 0,
                maxRetries = 3; // Retry limit for failed requests

            // Initialize Google Map
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

            // Get and update location immediately
            function attandancegetCurrentLocation(callback) {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const {
                                latitude,
                                longitude,
                                accuracy
                            } = position.coords;
                            updateLocation(position);
                            callback(latitude, longitude, accuracy);
                        },
                        function(error) {
                            console.error('Geolocation error:', error.message);
                            callback(null, null, null);
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                } else {
                    console.error('Geolocation not supported');
                    callback(null, null, null);
                }
            }

            // Update location and UI
            function updateLocation(position) {
                try {
                    const {
                        latitude,
                        longitude
                    } = position.coords;
                    const location = {
                        lat: latitude,
                        lng: longitude
                    };

                    // Update map and marker
                    map.setCenter(location);
                    marker.setPosition(location);

                    // Update UI
                    document.getElementById('currentLocation').textContent =
                        `Latitude: ${latitude.toFixed(6)}, Longitude: ${longitude.toFixed(6)}`;
                } catch (error) {
                    console.error('Error updating location:', error);
                }
            }

            // Check-in function
            function checkIn() {
                const checkInBtn = document.getElementById('checkInButton');
                checkInBtn.disabled = true;
                checkInBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Checking in...';

                attandancegetCurrentLocation((latitude, longitude, accuracy) => {
                    sendCheckInData(latitude, longitude, accuracy);
                });
            }

            function sendCheckInData(latitude, longitude, accuracy) {
                $.ajax({
                    url: '/salesperson/attendance/checkin',
                    method: 'POST',
                    data: {
                        check_in_location: JSON.stringify({
                            latitude,
                            longitude,
                            accuracy,
                            timestamp: new Date().toISOString()
                        }),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            let checkInTimeFormatted = new Date(response.check_in_time).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            document.getElementById('checkOutTime').textContent = response.time;
                            document.getElementById('checkInButton').style.display = 'none';
                            Swal.fire({
                                icon: 'success',
                                title: 'Checked In Successfully!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        }
                    },
                    complete: function() {
                        document.getElementById('checkInButton').disabled = false;
                        document.getElementById('checkInButton').innerHTML =
                            '<i class="fas fa-sign-in-alt me-2"></i> Check In';
                    }
                });
            }

            // Check-out function
            function checkOut() {
                const checkOutBtn = document.getElementById('checkOutButton');
                checkOutBtn.disabled = true;
                checkOutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Checking out...';

                attandancegetCurrentLocation((latitude, longitude, accuracy) => {
                    sendCheckOutData(latitude, longitude, accuracy);
                });
            }

            function sendCheckOutData(latitude, longitude, accuracy) {
                $.ajax({
                    url: '/salesperson/attendance/checkout',
                    method: 'POST',
                    data: {
                        check_out_location: JSON.stringify({
                            latitude,
                            longitude,
                            accuracy,
                            timestamp: new Date().toISOString()
                        }),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            let checkOutTimeFormatted = new Date(response.check_out_time).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            // ✅ Directly set check-out time from response
                            document.getElementById('checkOutTime').textContent = response.time;
                            document.getElementById('workingHours').textContent = response.working_hours + 'hrs';


                            let checkInTime = new Date(response.check_in_time);
                            let checkOutTime = new Date(response.check_out_time);
                            let workingHours = Math.floor((checkOutTime - checkInTime) / (1000 * 60 * 60));
                            let workingMinutes = Math.floor(((checkOutTime - checkInTime) % (1000 * 60 * 60)) / (
                                1000 * 60));


                            document.getElementById('checkOutButton').style.display = 'none';

                            Swal.fire({
                                icon: 'success',
                                title: 'Checked Out Successfully!',
                                text: `You checked out at ${response.time}. Total working hours: ${response.working_hours}`,
                                timer: 2000,
                                showConfirmButton: false
                            });

                        }
                    },
                    complete: function() {
                        document.getElementById('checkOutButton').disabled = false;
                        document.getElementById('checkOutButton').innerHTML =
                            '<i class="fas fa-sign-out-alt me-2"></i> Check Out';
                    }
                });
            }

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

        
            // Task Form Submission
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('addTaskForm');
                if (form) {
                    form.addEventListener('submit', function (event) {
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
                }
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
