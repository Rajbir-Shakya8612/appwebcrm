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
                        {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : '--:--' }}
                    </p>
                </div>
            </div>

            <!-- Check Out Time -->
            <div class="col-md-4 col-12 mb-3">
                <div class="info-card check-out-card shadow-sm text-center p-3">
                    <p class="small fw-semibold text-dark">Check Out Time</p>
                    <p class="h5 fw-bold text-dark" id="checkOutTime">
                        {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') : '--:--' }}
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
                        Latitude: 29.3726381 <br> Longitude: 75.4111531
                    </p>
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
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script>
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
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize location tracking
                if ("geolocation" in navigator) {
                    navigator.geolocation.watchPosition(function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Update location display
                        document.getElementById('currentLocation').textContent =
                            `Latitude: ${latitude}, Longitude: ${longitude}`;

                        // Store location in database
                        $.ajax({
                            url: '/location/today-tracks',
                            method: 'POST',
                            data: {
                                latitude: latitude,
                                longitude: longitude,
                                _token: '{{ csrf_token() }}'
                            }
                        });
                    });
                }

                // Add Lead Form Submission
                $('#addLeadForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '/leads',
                        method: 'POST',
                        data: $(this).serialize() + '&_token={{ csrf_token() }}',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Lead added successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#addLeadForm')[0].reset();
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to add lead'
                            });
                        }
                    });
                });

                // Add Task Form Submission
                $('#addTaskForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '/tasks',
                        method: 'POST',
                        data: $(this).serialize() + '&_token={{ csrf_token() }}',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Task added successfully',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            $('#addTaskForm')[0].reset();
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to add task'
                            });
                        }
                    });
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


            });

            // Attendance Functions
            function checkIn() {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        $.ajax({
                            url: '/salesperson/attendance/checkin',
                            method: 'POST',
                            data: {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    document.getElementById('checkInTime').textContent = response.time;
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

            function checkOut() {
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        $.ajax({
                            url: '/salesperson/attendance/checkout',
                            method: 'POST',
                            data: {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                                _token: $('meta[name="csrf-token"]').attr('content')
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

            // Task Management Functions
            function updateTaskStatus(taskId) {
                $.ajax({
                    url: `/tasks/${taskId}/status`,
                    method: 'PUT',
                    data: {
                        status: 'completed',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Task marked as completed',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update task status'
                        });
                    }
                });
            }

            function deleteTask(taskId) {
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
                        $.ajax({
                            url: `/tasks/${taskId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Task has been deleted.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete task'
                                });
                            }
                        });
                    }
                });
            }

            // Lead Management Functions
            function editLead(leadId) {
                // Implement lead editing functionality
                Swal.fire({
                    title: 'Edit Lead',
                    html: `
                        <form id="editLeadForm" class="space-y-4">
                            <div>
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div>
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control">
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control">
                            </div>
                            <div>
                                <label class="form-label">Description</label>
                                <textarea class="form-control"></textarea>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Save Changes',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        // Implement save functionality
                        return true;
                    }
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
                        $.ajax({
                            url: `/leads/${leadId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Lead has been deleted.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $(`[data-lead-id="${leadId}"]`).remove();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete lead'
                                });
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
