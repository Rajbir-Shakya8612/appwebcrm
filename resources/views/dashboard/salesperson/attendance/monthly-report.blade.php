@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Days</h5>
                    <h2 class="mb-0">{{ $stats['total_days'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Present</h5>
                    <h2 class="mb-0">{{ $stats['present'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Late</h5>
                    <h2 class="mb-0">{{ $stats['late'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Absent</h5>
                    <h2 class="mb-0">{{ $stats['absent'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Chart -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Monthly Attendance Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Attendance Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="attendancePieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Attendance Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Detailed Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Total Hours</th>
                                    <th>Late Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $record)
                                <tr>
                                    <td>{{ $record->date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $record->check_in ? $record->check_in->format('h:i A') : '-' }}</td>
                                    <td>{{ $record->check_out ? $record->check_out->format('h:i A') : '-' }}</td>
                                    <td>{{ $record->total_hours ? number_format($record->total_hours, 1) : '-' }}</td>
                                    <td>{{ $record->late_reason ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Prepare data for charts
const attendanceData = {
    labels: {!! json_encode($attendance->pluck('date')->map(function($date) {
        return $date->format('d M');
    })) !!},
    datasets: [{
        label: 'Status',
        data: {!! json_encode($attendance->map(function($record) {
            return $record->status === 'present' ? 1 : ($record->status === 'late' ? 0.5 : 0);
        })) !!},
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    }]
};

const pieData = {
    labels: ['Present', 'Late', 'Absent', 'Leave'],
    datasets: [{
        data: [
            {{ $stats['present'] }},
            {{ $stats['late'] }},
            {{ $stats['absent'] }},
            {{ $stats['leave'] }}
        ],
        backgroundColor: [
            'rgba(75, 192, 192, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(153, 102, 255, 0.8)'
        ]
    }]
};

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    // Line Chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: attendanceData,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 1,
                    ticks: {
                        stepSize: 0.5,
                        callback: function(value) {
                            if (value === 1) return 'Present';
                            if (value === 0.5) return 'Late';
                            return 'Absent';
                        }
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('attendancePieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: pieData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
    padding: 0.5em 0.75em;
}

.table th {
    background-color: #f8f9fa;
}
</style>
@endpush 
@endsection