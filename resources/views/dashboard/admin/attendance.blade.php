@extends('layouts.admin')

@section('title', 'Attendance Management')

@push('styles')
<!-- Add DataTables CSS -->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
<!-- Add Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .stat-card {
        padding: 1.5rem;
        background: linear-gradient(45deg, #fff, #f8f9fa);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    .primary-gradient {
        background: linear-gradient(45deg, #4e73df, #224abe);
    }
    .success-gradient {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }
    .danger-gradient {
        background: linear-gradient(45deg, #e74a3b, #be2617);
    }
    .warning-gradient {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
    }
    .chart-container {
        position: relative;
        height: 350px;
    }
    .btn-modern {
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .btn-modern i {
        font-size: 1.1rem;
    }
    .btn-modern:hover {
        transform: translateY(-2px);
    }
    .filter-card {
        background: #fff;
        border-radius: 15px;
        padding: 1.5rem;
    }
    .table {
        vertical-align: middle;
    }
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 600;
        border-bottom: none;
    }
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
    }
    .search-box {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border: 1px solid #e3e6f0;
    }
    .pagination {
        gap: 5px;
    }
    .page-link {
        border-radius: 8px;
        padding: 0.5rem 1rem;
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 10px;
        padding: 0.3rem 2rem 0.3rem 1rem;
        background-position: right 0.5rem center;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border: 1px solid #e3e6f0;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate-button {
        border-radius: 8px;
        margin: 0 2px;
    }
    
    .avatar {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        color: #4e73df;
        background-color: #edf2ff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Attendance Management</h1>
            <p class="text-muted mb-0">Monitor and manage employee attendance</p>
        </div>
        <div class="d-flex gap-3">
            <button class="btn btn-modern btn-primary" data-bs-toggle="modal" data-bs-target="#bulkAttendanceModal">
                <i class="fas fa-plus-circle"></i> Bulk Update
            </button>
            <button class="btn btn-modern btn-success" id="exportAttendance">
                <i class="fas fa-file-export"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card mb-4">
        <form id="attendanceFilterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-muted">Select User</label>
                <select class="form-select" id="userFilter">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted">Date Range</label>
                <input type="date" class="form-control" id="dateFilter" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted">Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="late">Late</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-modern btn-primary w-100">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-primary text-uppercase mb-2">Today's Attendance</h6>
                        <h2 class="mb-0 fw-bold">{{ $todayAttendance }}%</h2>
                    </div>
                    <div class="stat-icon primary-gradient text-white">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-success text-uppercase mb-2">Present Today</h6>
                        <h2 class="mb-0 fw-bold" id="presentCount">0</h2>
                    </div>
                    <div class="stat-icon success-gradient text-white">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-danger text-uppercase mb-2">Absent Today</h6>
                        <h2 class="mb-0 fw-bold" id="absentCount">0</h2>
                    </div>
                    <div class="stat-icon danger-gradient text-white">
                        <i class="fas fa-user-times"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-warning text-uppercase mb-2">Late Today</h6>
                        <h2 class="mb-0 fw-bold" id="lateCount">0</h2>
                    </div>
                    <div class="stat-icon warning-gradient text-white">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header py-3 bg-transparent">
                    <h6 class="m-0 fw-bold text-primary">Attendance Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header py-3 bg-transparent">
                    <h6 class="m-0 fw-bold text-primary">Attendance Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendancePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card">
        <div class="card-header py-3 bg-transparent">
            <h6 class="m-0 fw-bold text-primary">Attendance Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="attendanceTable" width="100%">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Attendance Modal -->
<div class="modal fade" id="bulkAttendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Bulk Update Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkAttendanceForm">
                    <div class="mb-4">
                        <label class="form-label">Select Date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <select class="form-select form-select-sm" name="attendance[{{ $user->id }}][status]">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" name="attendance[{{ $user->id }}][check_in]">
                                    </td>
                                    <td>
                                        <input type="time" class="form-control form-control-sm" name="attendance[{{ $user->id }}][check_out]">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveBulkAttendance">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Edit Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAttendanceForm">
                    <input type="hidden" name="attendance_id" id="attendance_id">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check In Time</label>
                        <input type="time" class="form-control" name="check_in" id="edit_check_in">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Check Out Time</label>
                        <input type="time" class="form-control" name="check_out" id="edit_check_out">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEditAttendance">Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Add DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<!-- Add Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
// Configure Toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
};

$(document).ready(function() {
    // Initialize Overview Chart
    const overviewCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(overviewCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Present',
                data: {!! json_encode($chartData['present']) !!},
                borderColor: '#10b981',
                backgroundColor: '#10b98120',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Absent',
                data: {!! json_encode($chartData['absent']) !!},
                borderColor: '#ef4444',
                backgroundColor: '#ef444420',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Late',
                data: {!! json_encode($chartData['late']) !!},
                borderColor: '#f59e0b',
                backgroundColor: '#f59e0b20',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    padding: 10,
                    backgroundColor: '#fff',
                    titleColor: '#000',
                    bodyColor: '#666',
                    borderColor: '#e9ecef',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' employees';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Initialize Distribution Chart
    const distributionCtx = document.getElementById('attendancePieChart').getContext('2d');
    const attendancePieChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [
                    {{ $presentCount }},
                    {{ $absentCount }},
                    {{ $lateCount }}
                ],
                backgroundColor: [
                    '#10b981',
                    '#ef4444',
                    '#f59e0b'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#000',
                    bodyColor: '#666',
                    borderColor: '#e9ecef',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + ' employees';
                        }
                    }
                }
            }
        }
    });

    // Initialize DataTable
    const table = $('#attendanceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.attendance") }}',
            data: function(d) {
                d.user_id = $('#userFilter').val();
                d.date = $('#dateFilter').val();
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            { 
                data: 'user_name',
                name: 'user_name'
            },
            { 
                data: 'formatted_date',
                name: 'date'
            },
            { 
                data: 'formatted_status',
                name: 'status'
            },
            { 
                data: 'formatted_check_in',
                name: 'check_in'
            },
            { 
                data: 'formatted_check_out',
                name: 'check_out'
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-end'
            }
        ],
        order: [[1, 'desc']],
        pageLength: 10,
        language: {
            search: "",
            searchPlaceholder: "Search records...",
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        },
        drawCallback: function() {
            // Reinitialize tooltips after table draw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Filter form submission
    $('#attendanceFilterForm').on('submit', function(e) {
        e.preventDefault();
        table.draw();
        loadAttendanceData();
    });

    // Edit Attendance
    $(document).on('click', '.edit-attendance', function() {
        const attendanceId = $(this).data('id');
        
        $.ajax({
            url: `/admin/attendance/${attendanceId}`,
            method: 'GET',
            success: function(response) {
                $('#attendance_id').val(response.id);
                $('#edit_status').val(response.status);
                $('#edit_check_in').val(response.check_in);
                $('#edit_check_out').val(response.check_out);
                $('#editAttendanceModal').modal('show');
            },
            error: function(xhr) {
                toastr.error('Failed to load attendance details');
            }
        });
    });

    // Save Edit Attendance
    $('#saveEditAttendance').on('click', function() {
        const attendanceId = $('#attendance_id').val();
        const formData = $('#editAttendanceForm').serialize();
        
        $.ajax({
            url: `/admin/attendance/${attendanceId}`,
            method: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#editAttendanceModal').modal('hide');
                table.draw();
                loadAttendanceData();
                toastr.success('Attendance updated successfully');
            },
            error: function(xhr) {
                toastr.error('Error updating attendance');
            }
        });
    });

    // Load Attendance Data
    function loadAttendanceData() {
        $.ajax({
            url: '{{ route("admin.attendance.overview") }}',
            method: 'GET',
            data: {
                user_id: $('#userFilter').val(),
                date: $('#dateFilter').val(),
                status: $('#statusFilter').val()
            },
            success: function(response) {
                // Update Overview Chart
                attendanceChart.data.labels = response.labels;
                attendanceChart.data.datasets[0].data = response.present;
                attendanceChart.data.datasets[1].data = response.absent;
                attendanceChart.data.datasets[2].data = response.late;
                attendanceChart.update();

                // Update Distribution Chart
                attendancePieChart.data.datasets[0].data = [
                    response.present[response.present.length - 1] || 0,
                    response.absent[response.absent.length - 1] || 0,
                    response.late[response.late.length - 1] || 0
                ];
                attendancePieChart.update();

                // Update Stats
                $('#todayAttendance').text(response.todayAttendance + '%');
                $('#presentCount').text(response.presentCount);
                $('#absentCount').text(response.absentCount);
                $('#lateCount').text(response.lateCount);
            }
        });
    }

    // Initial load
    loadAttendanceData();

    // Bulk Attendance Save
    $('#saveBulkAttendance').on('click', function() {
        const formData = $('#bulkAttendanceForm').serialize();
        
        $.ajax({
            url: '{{ route("admin.attendance.bulk-update") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#bulkAttendanceModal').modal('hide');
                loadAttendanceData();
                toastr.success('Attendance updated successfully');
            },
            error: function(xhr) {
                toastr.error('Error updating attendance');
            }
        });
    });

    // Export Attendance
    $('#exportAttendance').on('click', function() {
        const userId = $('#userFilter').val();
        const date = $('#dateFilter').val();
        const status = $('#statusFilter').val();

        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Exporting...');

        // Create form and submit
        const form = $('<form>', {
            'method': 'GET',
            'action': '{{ route("admin.attendance.export") }}'
        });

        if (userId) form.append($('<input>', { 'type': 'hidden', 'name': 'user_id', 'value': userId }));
        if (date) form.append($('<input>', { 'type': 'hidden', 'name': 'date', 'value': date }));
        if (status) form.append($('<input>', { 'type': 'hidden', 'name': 'status', 'value': status }));

        $('body').append(form);
        form.submit();
        form.remove();

        // Reset button state after 2 seconds
        setTimeout(() => {
            $(this).prop('disabled', false).html('<i class="fas fa-file-export"></i> Export Data');
        }, 2000);
    });
});
</script>
@endpush 