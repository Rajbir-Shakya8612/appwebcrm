@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fas fa-clock me-2"></i>Attendance Management
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <button type="button" class="btn btn-primary d-none d-sm-inline-block" onclick="exportAttendance()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <!-- Status Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <i class="fas fa-user-clock"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-secondary mb-1">Today's Status</div>
                                    <h3 class="mb-2" id="todayStatus">{{ $todayStatus ?? 'Not Checked In' }}</h3>
                                    <div id="checkInOutButtons">
                                        @if(!$isCheckedIn)
                                        <button class="btn btn-primary btn-sm" onclick="checkIn()">
                                            <i class="fas fa-sign-in-alt me-1"></i> Check In
                                        </button>
                                        @else
                                        <button class="btn btn-danger btn-sm" onclick="checkOut()">
                                            <i class="fas fa-sign-out-alt me-1"></i> Check Out
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar">
                                        <i class="fas fa-calendar-check"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-secondary mb-1">Monthly Attendance</div>
                                    <h3 class="mb-1">{{ $monthlyAttendance ?? 0 }} Days</h3>
                                    <div class="row g-2 align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-success">Present: {{ $monthlyPresent ?? 0 }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="badge bg-danger">Absent: {{ $monthlyAbsent ?? 0 }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-info text-white avatar">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium text-secondary mb-1">Working Hours</div>
                                    <h3 class="mb-1">{{ $totalWorkingHours ?? 0 }} Hours</h3>
                                    <div class="text-muted">This Month</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt me-2"></i>Attendance Calendar
                    </h3>
                </div>
                <div class="card-body">
                    <div id="attendanceCalendar"></div>
                </div>
            </div>

            <!-- Attendance History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history me-2"></i>Attendance History
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Working Hours</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceHistory as $record)
                                <tr>
                                    <td>{{ $record->date->format('M d, Y') }}</td>
                                    <td>{{ $record->check_in ? $record->check_in->format('h:i A') : '-' }}</td>
                                    <td>{{ $record->check_out ? $record->check_out->format('h:i A') : '-' }}</td>
                                    <td>{{ $record->working_hours ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $record->status === 'present' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $record->location ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty">
                                            <div class="empty-icon">
                                                <i class="fas fa-calendar-times fa-2x text-muted"></i>
                                            </div>
                                            <p class="empty-title">No records found</p>
                                            <p class="empty-subtitle text-muted">
                                                No attendance records are available at the moment.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Permission Modal -->
<div class="modal fade" id="locationPermissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt me-2"></i>Location Permission Required
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Please enable location services to check in/out. This helps us track your attendance accurately.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
:root {
    --primary-color: #206bc4;
    --success-color: #2fb344;
    --danger-color: #d63939;
    --info-color: #4299e1;
}

.page-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-left: 15rem; /* Adjust based on your sidebar width */
    background: #f5f7fb;
}

.page-header {
    padding: 1.5rem 0;
    border-bottom: 1px solid rgba(98, 105, 118, 0.16);
    background: white;
    margin-bottom: 1.5rem;
}

.page-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 500;
    color: #1e293b;
}

.page-body {
    flex: 1;
    padding: 0 0 1.5rem;
}

.card {
    box-shadow: rgba(35, 46, 60, 0.04) 0 2px 4px 0;
    border: 1px solid rgba(98, 105, 118, 0.16);
    background: white;
    position: relative;
    margin-bottom: 1.5rem;
}

.card-sm {
    border-radius: 4px;
}

.avatar {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.2rem;
}

.bg-primary {
    background-color: var(--primary-color) !important;
}

.bg-green {
    background-color: var(--success-color) !important;
}

.bg-info {
    background-color: var(--info-color) !important;
}

.table-vcenter td,
.table-vcenter th {
    vertical-align: middle;
}

.card-table tr:first-child td {
    border-top: 0;
}

.empty {
    text-align: center;
    padding: 2rem;
}

.empty-icon {
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.25rem;
    line-height: 1.4;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.empty-subtitle {
    font-size: 0.875rem;
    line-height: 1.4;
}

.fc-theme-standard td, 
.fc-theme-standard th {
    border-color: rgba(98, 105, 118, 0.16);
}

.fc-event {
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 0.875rem;
}

@media (max-width: 991.98px) {
    .page-wrapper {
        padding-left: 0;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('attendanceCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: {!! json_encode($calendarEvents ?? []) !!},
        eventColor: '#206bc4',
        eventTextColor: '#ffffff',
        height: 'auto',
        contentHeight: 'auto',
        dayMaxEvents: true,
        displayEventTime: false,
        firstDay: 1,
        themeSystem: 'standard'
    });
    calendar.render();
});

function checkIn() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const location = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            };

            fetch('{{ route("salesperson.attendance.checkin") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(location)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error checking in');
                }
            });
        },
        function(error) {
            $('#locationPermissionModal').modal('show');
        }
    );
}

function checkOut() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by your browser');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const location = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            };

            fetch('{{ route("salesperson.attendance.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(location)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error checking out');
                }
            });
        },
        function(error) {
            $('#locationPermissionModal').modal('show');
        }
    );
}

function exportAttendance() {
   console.log('hello')
}
</script>
@endpush