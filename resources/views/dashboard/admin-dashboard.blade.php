@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <!-- Overview Stats -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Total Salespersons</h6>
                                <h4 class="mb-0">{{ $totalSalespersons }}</h4>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> {{ $newSalespersons }} new this month
                                </small>
                            </div>
                            <div class="flex-shrink-0 ms-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-people text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Today's Attendance</h6>
                                <h4 class="mb-0">{{ $todayAttendance }}%</h4>
                                <small class="text-{{ $attendanceChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="bi bi-arrow-{{ $attendanceChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($attendanceChange) }}% vs yesterday
                                </small>
                            </div>
                            <div class="flex-shrink-0 ms-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-calendar-check text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Total Leads</h6>
                                <h4 class="mb-0">{{ $totalLeads }}</h4>
                                <small class="text-{{ $leadChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="bi bi-arrow-{{ $leadChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($leadChange) }}% vs last month
                                </small>
                            </div>
                            <div class="flex-shrink-0 ms-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-person-lines-fill text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Total Sales</h6>
                                <h4 class="mb-0">₹{{ number_format($totalSales) }}</h4>
                                <small class="text-{{ $salesChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="bi bi-arrow-{{ $salesChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($salesChange) }}% vs last month
                                </small>
                            </div>
                            <div class="flex-shrink-0 ms-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                    <i class="bi bi-graph-up text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Board -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Task Board</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="bi bi-plus"></i> Add Task
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="task-board">
                            <div class="task-column">
                                <h6 class="d-flex justify-content-between mb-3">
                                    To Do
                                    <span class="badge bg-secondary">{{ count($todoTasks) }}</span>
                                </h6>
                                @forelse($todoTasks as $task)
                                    <div class="task-card">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">{{ $task->type }}</span>
                                            <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                        </div>
                                        <h6 class="mb-2">{{ $task->title }}</h6>
                                        <p class="small text-muted mb-2">{{ $task->description }}</p>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $task->assignee->photo_url }}" alt="{{ $task->assignee->name }}"
                                                class="rounded-circle" width="24" height="24">
                                            <span class="small text-muted ms-2">{{ $task->assignee->name }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-list-task mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0">No tasks to do</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="task-column">
                                <h6 class="d-flex justify-content-between mb-3">
                                    In Progress
                                    <span class="badge bg-secondary">{{ count($inProgressTasks) }}</span>
                                </h6>
                                @forelse($inProgressTasks as $task)
                                    <div class="task-card">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">{{ $task->type }}</span>
                                            <small class="text-muted">{{ $task->due_date->format('M d') }}</small>
                                        </div>
                                        <h6 class="mb-2">{{ $task->title }}</h6>
                                        <p class="small text-muted mb-2">{{ $task->description }}</p>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $task->assignee->photo_url }}" alt="{{ $task->assignee->name }}"
                                                class="rounded-circle" width="24" height="24">
                                            <span class="small text-muted ms-2">{{ $task->assignee->name }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-list-task mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0">No tasks in progress</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="task-column">
                                <h6 class="d-flex justify-content-between mb-3">
                                    Done
                                    <span class="badge bg-secondary">{{ count($doneTasks) }}</span>
                                </h6>
                                @forelse($doneTasks as $task)
                                    <div class="task-card">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-success">{{ $task->type }}</span>
                                            <small class="text-muted">{{ $task->completed_at->format('M d') }}</small>
                                        </div>
                                        <h6 class="mb-2">{{ $task->title }}</h6>
                                        <p class="small text-muted mb-2">{{ $task->description }}</p>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $task->assignee->photo_url }}"
                                                alt="{{ $task->assignee->name }}" class="rounded-circle" width="24"
                                                height="24">
                                            <span class="small text-muted ms-2">{{ $task->assignee->name }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-list-task mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0">No completed tasks</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts & Timeline -->
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Attendance Overview</h5>
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="attendanceFilterDropdown"
                                data-bs-toggle="dropdown">
                                This Month
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="attendanceFilterDropdown">
                                <li><a class="dropdown-item" href="#" data-filter="week">This Week</a></li>
                                <li><a class="dropdown-item active" href="#" data-filter="month">This Month</a>
                                </li>
                                <li><a class="dropdown-item" href="#" data-filter="year">This Year</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse($recentActivities as $activity)
                                <div class="timeline-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1">{{ $activity->description }}</h6>
                                            <div class="text-muted small">
                                                @if ($activity->type === 'lead')
                                                    <i class="bi bi-person-plus text-primary"></i>
                                                @elseif($activity->type === 'sale')
                                                    <i class="bi bi-cart-check text-success"></i>
                                                @elseif($activity->type === 'attendance')
                                                    <i class="bi bi-calendar-check text-info"></i>
                                                @endif
                                                {{ $activity->details }}
                                            </div>
                                        </div>
                                        <div class="text-muted small">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-calendar2-x mb-2" style="font-size: 2rem;"></i>
                                    <p class="mb-0">No recent activities</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="taskTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="taskDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="taskType" class="form-label">Type</label>
                            <select class="form-select" id="taskType" name="type" required>
                                <option value="">Select Type</option>
                                <option value="lead">Lead</option>
                                <option value="sale">Sale</option>
                                <option value="meeting">Meeting</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="taskAssignee" class="form-label">Assign To</label>
                            <select class="form-select" id="taskAssignee" name="assignee_id" required>
                                <option value="">Select Salesperson</option>
                                @foreach ($salespersons as $salesperson)
                                    <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="taskDueDate" class="form-label">Due Date</label>
                            <input type="datetime-local" class="form-control" id="taskDueDate" name="due_date" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveTaskButton">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Save Task
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Attendance Chart
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceChart = new Chart(attendanceCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($attendanceData->labels) !!},
                    datasets: [{
                            label: 'Present',
                            data: {!! json_encode($attendanceData->present) !!},
                            borderColor: '#10b981',
                            backgroundColor: '#10b98120',
                            fill: true
                        },
                        {
                            label: 'Absent',
                            data: {!! json_encode($attendanceData->absent) !!},
                            borderColor: '#ef4444',
                            backgroundColor: '#ef444420',
                            fill: true
                        },
                        {
                            label: 'Late',
                            data: {!! json_encode($attendanceData->late) !!},
                            borderColor: '#f59e0b',
                            backgroundColor: '#f59e0b20',
                            fill: true
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

            // Add Task Form Handler
            const addTaskForm = document.getElementById('addTaskForm');
            const saveTaskButton = document.getElementById('saveTaskButton');
            const spinner = saveTaskButton.querySelector('.spinner-border');

            saveTaskButton.addEventListener('click', async function() {
                if (!addTaskForm.checkValidity()) {
                    addTaskForm.reportValidity();
                    return;
                }

                // Disable button and show spinner
                saveTaskButton.disabled = true;
                spinner.classList.remove('d-none');

                try {
                    const formData = new FormData(addTaskForm);
                    const response = await axios.post('/api/admin/tasks', Object.fromEntries(formData));

                    // Show success message
                    showToast('success', 'Task created successfully');

                    // Close modal and reset form
                    bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
                    addTaskForm.reset();

                    // Reload page to show new task
                    window.location.reload();

                } catch (error) {
                    const message = error.response?.data?.message || 'Failed to create task';
                    showToast('error', message);
                } finally {
                    // Enable button and hide spinner
                    saveTaskButton.disabled = false;
                    spinner.classList.add('d-none');
                }
            });

            // Attendance Filter Handler
            const attendanceFilterDropdown = document.getElementById('attendanceFilterDropdown');
            const filterLinks = document.querySelectorAll('[data-filter]');

            filterLinks.forEach(link => {
                link.addEventListener('click', async function(e) {
                    e.preventDefault();

                    // Update dropdown text
                    attendanceFilterDropdown.textContent = this.textContent;

                    // Update active state
                    filterLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    try {
                        showLoading();
                        const response = await axios.get(
                            `/api/admin/attendance/overview?filter=${this.dataset.filter}`);

                        // Update chart data
                        attendanceChart.data.labels = response.data.labels;
                        attendanceChart.data.datasets[0].data = response.data.present;
                        attendanceChart.data.datasets[1].data = response.data.absent;
                        attendanceChart.data.datasets[2].data = response.data.late;
                        attendanceChart.update();

                    } catch (error) {
                        showToast('error', 'Failed to load attendance data');
                    } finally {
                        hideLoading();
                    }
                });
            });
        </script>
    @endpush
