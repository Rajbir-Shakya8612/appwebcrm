@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Performance Dashboard</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-primary" onclick="filterPerformance('daily')">Daily</button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterPerformance('weekly')">Weekly</button>
                            <button type="button" class="btn btn-outline-primary" onclick="filterPerformance('monthly')">Monthly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Performance Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Sales</h5>
                                    <h3>{{ number_format($totalSales ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Leads Generated</h5>
                                    <h3>{{ number_format($leadsGenerated ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Conversion Rate</h5>
                                    <h3>{{ number_format($conversionRate ?? 0, 1) }}%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Target Achievement</h5>
                                    <h3>{{ number_format($targetAchievement ?? 0, 1) }}%</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Charts -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Sales Trend</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Lead Status Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="leadsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Detailed Performance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Sales Amount</th>
                                                    <th>Leads Generated</th>
                                                    <th>Meetings</th>
                                                    <th>Follow-ups</th>
                                                    <th>Conversion Rate</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($performanceData as $data)
                                                <tr>
                                                    <td>{{ $data->date }}</td>
                                                    <td>{{ number_format($data->sales_amount) }}</td>
                                                    <td>{{ number_format($data->leads_generated) }}</td>
                                                    <td>{{ number_format($data->meetings) }}</td>
                                                    <td>{{ number_format($data->follow_ups) }}</td>
                                                    <td>{{ number_format($data->conversion_rate, 1) }}%</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No performance data available</td>
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($salesLabels ?? []) !!},
        datasets: [{
            label: 'Sales Amount',
            data: {!! json_encode($salesData ?? []) !!},
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Leads Chart
const leadsCtx = document.getElementById('leadsChart').getContext('2d');
new Chart(leadsCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($leadsLabels ?? []) !!},
        datasets: [{
            data: {!! json_encode($leadsData ?? []) !!},
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)'
            ]
        }]
    },
    options: {
        responsive: true
    }
});

function filterPerformance(period) {
    window.location.href = `{{ route("salesperson.performance.index") }}?period=${period}`;
}
</script>
@endpush 