@extends('layouts.app')

@section('title', 'Salesperson Dashboard')

@section('content')
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
                        @if($attendance && $attendance->check_in_time)
                            {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                        @else
                            --:--
                        @endif
                    </p>
                    @if($attendance && $attendance->status === 'late')
                        <span class="badge bg-warning text-dark">Late</span>
                    @endif
                </div>
            </div>

            <!-- Check Out Time -->
            <div class="col-md-4 col-12 mb-3">
                <div class="info-card check-out-card shadow-sm text-center p-3">
                    <p class="small fw-semibold text-dark">Check Out Time</p>
                    <p class="h5 fw-bold text-dark" id="checkOutTime">
                        @if($attendance && $attendance->check_out_time)
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
                            {{ \Carbon\Carbon::parse($attendance->check_in_time)->diff(\Carbon\Carbon::parse($attendance->check_out_time))->format('%H:%I') }} hrs
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
        <div class="modal fade" id="lateReasonModal" tabindex="-1" aria-labelledby="lateReasonModalLabel" aria-hidden="true">
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
                    <button onclick="openModal('leadModal')" class="btn btn-primary me-2">
                        + Add New Lead
                    </button>
                    <button onclick="openModal('taskModal')" class="btn btn-success">
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
                                                    <button onclick="deleteLead({{ $lead->id }})" class="text-danger">
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
        <div id="leadModal" class="modal fade" tabindex="-1" aria-labelledby="leadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leadModalLabel">Add New Lead</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addLeadForm" class="space-y-4">
                            <input type="text" name="name" placeholder="Name" required class="form-control mb-2">
                            <input type="tel" name="phone" placeholder="Phone" class="form-control mb-2">
                            <input type="email" name="email" placeholder="Email" class="form-control mb-2">
                            <textarea name="address" placeholder="Address" class="form-control mb-2"></textarea>
                            <input type="number" name="expected_amount" placeholder="Expected Amount"
                                class="form-control mb-2">
                            <button type="submit" class="btn btn-primary w-100">
                                Add Lead
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Modal -->
        <div id="taskModal" class="modal fade" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addTaskForm" class="space-y-4">
                            <input type="text" name="title" placeholder="Title" required class="form-control mb-2">
                            <textarea name="description" placeholder="Description" class="form-control mb-2"></textarea>
                            <input type="date" name="due_date" class="form-control mb-2">
                            <select name="priority" class="form-select mb-2">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            <button type="submit" class="btn btn-success w-100">
                                Add Task
                            </button>
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

    @push('scripts')
     <script>
        let events = @json($events);
    </script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBl5N0v6zO372f3-RU-mSKNAMyN1Cu0Rzk"></script>
        <script>
            let map;
            let marker;
            let locationUpdateInterval;
            let isTracking = false;

            // Initialize map
            function initMap() {
                map = new google.maps.Map(document.getElementById('locationMap'), {
                    zoom: 15,
                    center: { lat: 0, lng: 0 }
                });
                marker = new google.maps.Marker({
                    map: map,
                    position: { lat: 0, lng: 0 }
                });
            }

            // Update location
            function updateLocation(position) {
                const { latitude, longitude, speed, accuracy } = position.coords;
                const location = { lat: latitude, lng: longitude };
                
                map.setCenter(location);
                marker.setPosition(location);
                
                // Update location display
                document.getElementById('currentLocation').textContent = 
                    `Latitude: ${latitude.toFixed(6)}, Longitude: ${longitude.toFixed(6)}`;
                
                // Record location if tracking is active
                if (isTracking) {
                    recordLocation(latitude, longitude, speed, accuracy);
                }
            }

            // Record location to server
            function recordLocation(latitude, longitude, speed, accuracy) {
                $.ajax({
                    url: '/location/tracks',
                    method: 'POST',
                    data: {
                        latitude: latitude,
                        longitude: longitude,
                        speed: speed,
                        accuracy: accuracy,
                        address: 'Current Location', // You can use reverse geocoding here
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (!response.success) {
                            console.error('Failed to record location:', response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error recording location:', xhr);
                    }
                });
            }

            // Start location tracking
            function startLocationTracking() {
                if ("geolocation" in navigator) {
                    navigator.geolocation.watchPosition(
                        updateLocation,
                        function(error) {
                            console.error('Geolocation error:', error);
                            document.getElementById('currentLocation').textContent = 
                                'Error getting location: ' + error.message;
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                } else {
                    document.getElementById('currentLocation').textContent = 
                        'Geolocation is not supported by your browser';
                }
            }

            // Check in function
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
                                    document.getElementById('checkInTime').textContent = response.time;
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
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to check in'
                                });
                            }
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

            // Check out function
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
                                    document.getElementById('checkOutTime').textContent = response.time;
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    location.reload();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to check out'
                                });
                            }
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
                var modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            }

            function closeModal(modalId) {
                var modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                modal.hide();
            }

            document.getElementById('addLeadForm').addEventListener('submit', function(event) {
                event.preventDefault();
                alert('Lead Added Successfully!');
                closeModal('leadModal');
                this.reset();
            });

            document.getElementById('addTaskForm').addEventListener('submit', function(event) {
                event.preventDefault();
                alert('Task Added Successfully!');
                closeModal('taskModal');
                this.reset();
            });

            // Initialize Dragula
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

                // Update lead status via AJAX
                $.ajax({
                    url: `/leads/${leadId}/status`,
                    method: 'PUT',
                    data: {
                        status_id: newStatusId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Lead status updated successfully',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update lead status'
                        });
                    }
                });
            });

            // calendar show data


        </script>
    @endpush
@endsection
