<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Calendar</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #7952b3;
            --secondary: #6c757d;
            --border-color: #e9ecef;
        }

        body {
            background-color: #f5f6f8;
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
        }

        .calendar-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
        }

        .calendar-navigation {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-btn {
            background: none;
            border: none;
            color: var(--secondary);
            padding: 0.5rem;
            cursor: pointer;
        }

        .nav-btn:hover {
            color: var(--primary);
        }

        .current-date {
            font-weight: 500;
            margin: 0 1rem;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: 50px 1fr;
            height: calc(100vh - 140px);
            background: white;
            margin: 1rem;
            border-radius: 8px;
            overflow: hidden;
        }

        .time-column {
            border-right: 1px solid var(--border-color);
            padding: 0.5rem;
            background: #f8f9fa;
        }

        .time-slot {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .calendar-content {
            position: relative;
        }

        .hour-row {
            height: 60px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .hour-row:last-child {
            border-bottom: none;
        }

        .current-time-line {
            position: absolute;
            left: 0;
            right: 0;
            border-top: 2px solid #dc3545;
            z-index: 1;
        }

        .event {
            position: absolute;
            left: 1rem;
            right: 1rem;
            background: rgba(121, 82, 179, 0.1);
            border-left: 4px solid var(--primary);
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .add-event-modal .modal-header {
            background: #f8f9fa;
        }

        .attendee {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .attendee-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .mini-calendar {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .mini-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .calendar-day:hover {
            background: #f8f9fa;
        }

        .calendar-day.current {
            background: var(--primary);
            color: white;
        }

        .calendar-day.has-event {
            border: 2px solid var(--primary);
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="https://via.placeholder.com/32" alt="CRM" class="brand-logo">
                <span class="fw-bold">CRM</span>
                <div class="btn-group">
                    <button class="btn btn-link text-dark dropdown-toggle" data-bs-toggle="dropdown">
                        Meetings
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-primary" onclick="showAddEventModal()">
                    <i class="fas fa-plus me-2"></i>New Meeting
                </button>
            </div>
        </div>
    </nav>

    <!-- Calendar Header -->
    <div class="calendar-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="calendar-navigation">
                <button class="nav-btn" onclick="previousWeek()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-outline-secondary" onclick="goToToday()">Today</button>
                <button class="nav-btn" onclick="nextWeek()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <span class="current-date">March 2024</span>
                <select class="form-select form-select-sm" style="width: 100px;">
                    <option>Week</option>
                    <option>Month</option>
                    <option>Day</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="mini-calendar">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>March 2024</span>
                        <div>
                            <button class="nav-btn"><i class="fas fa-chevron-left"></i></button>
                            <button class="nav-btn"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="mini-calendar-grid">
                        <!-- Calendar days will be added dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="calendar-grid">
        <div class="time-column">
            <!-- Time slots will be added dynamically -->
        </div>
        <div class="calendar-content">
            <!-- Hour rows will be added dynamically -->
        </div>
    </div>

    <!-- Add Event Modal -->
    <div class="modal fade add-event-modal" id="addEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg" placeholder="Add title" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Start</label>
                                <input type="datetime-local" class="form-control" required>
                            </div>
                            <div class="col">
                                <label class="form-label">End</label>
                                <input type="datetime-local" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="allDay">
                                <label class="form-check-label" for="allDay">All Day</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attendees</label>
                            <div class="attendee">
                                <div class="attendee-avatar">R</div>
                                <span>Rajbir</span>
                            </div>
                            <button type="button" class="btn btn-link text-primary p-0">
                                <i class="fas fa-plus me-1"></i>Add attendees...
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Videocall URL</label>
                            <button type="button" class="btn btn-link text-primary p-0">
                                <i class="fas fa-plus me-1"></i>Add Odoo meeting
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" placeholder="Describe your meeting"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Discard</button>
                    <button type="button" class="btn btn-primary" onclick="saveEvent()">Save & Close</button>
                    <button type="button" class="btn btn-link">More Options</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            initializeMiniCalendar();
            updateCurrentTimeLine();
        });

        function initializeCalendar() {
            const timeColumn = document.querySelector('.time-column');
            const calendarContent = document.querySelector('.calendar-content');

            // Add time slots and hour rows
            for (let i = 0; i < 24; i++) {
                // Time column
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                timeSlot.textContent = `${i.toString().padStart(2, '0')}:00`;
                timeColumn.appendChild(timeSlot);

                // Calendar content
                const hourRow = document.createElement('div');
                hourRow.className = 'hour-row';
                hourRow.addEventListener('click', (e) => showAddEventModal(i, e.offsetY));
                calendarContent.appendChild(hourRow);
            }
        }

        function initializeMiniCalendar() {
            const grid = document.querySelector('.mini-calendar-grid');
            const daysInMonth = 31; // Example for March
            const firstDay = 5; // Friday (0 = Sunday, 5 = Friday)

            // Add day headers
            const days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day';
                dayHeader.textContent = day;
                grid.appendChild(dayHeader);
            });

            // Add empty cells for days before the 1st
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day';
                grid.appendChild(emptyDay);
            }

            // Add days
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.className = 'calendar-day';
                if (i === 24) day.classList.add('current'); // Current day
                day.textContent = i;
                grid.appendChild(day);
            }
        }

        function updateCurrentTimeLine() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const topPosition = (hours * 60 + minutes) * (60 / 60); // 60px per hour

            const line = document.createElement('div');
            line.className = 'current-time-line';
            line.style.top = `${topPosition}px`;
            document.querySelector('.calendar-content').appendChild(line);
        }

        function showAddEventModal(hour = null, offsetY = null) {
            if (hour !== null) {
                const minutes = Math.floor((offsetY / 60) * 60);
                const startTime = new Date();
                startTime.setHours(hour, minutes, 0, 0);
                
                const endTime = new Date(startTime);
                endTime.setHours(startTime.getHours() + 1);

                // Set default times in the modal
                document.querySelector('input[type="datetime-local"]').value = 
                    startTime.toISOString().slice(0, 16);
                document.querySelectorAll('input[type="datetime-local"]')[1].value = 
                    endTime.toISOString().slice(0, 16);
            }

            const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
            modal.show();
        }

        function saveEvent() {
            // Get form data and save event
            const modal = bootstrap.Modal.getInstance(document.getElementById('addEventModal'));
            modal.hide();
            // Refresh calendar
            initializeCalendar();
        }

        function previousWeek() {
            // Implement previous week navigation
        }

        function nextWeek() {
            // Implement next week navigation
        }

        function goToToday() {
            // Implement go to today functionality
        }
    </script>
</body>
</html> 