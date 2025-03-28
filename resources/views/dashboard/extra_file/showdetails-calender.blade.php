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

        .month-calendar {
            background: white;
            border-radius: 8px;
            margin: 1rem;
            padding: 1rem;
        }

        .month-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: var(--border-color);
            border: 1px solid var(--border-color);
        }

        .month-header {
            background: #f8f9fa;
            padding: 0.5rem;
            text-align: center;
            font-weight: 500;
        }

        .month-day {
            background: white;
            min-height: 120px;
            padding: 0.5rem;
            position: relative;
        }

        .month-day:hover {
            background: #f8f9fa;
        }

        .day-number {
            font-size: 0.875rem;
            color: var(--secondary);
            margin-bottom: 0.5rem;
        }

        .current-day .day-number {
            background: var(--primary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .attendance-status {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-present {
            background: #28a745;
        }

        .status-absent {
            background: #dc3545;
        }

        .status-leave {
            background: #ffc107;
        }

        .event-indicator {
            background: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-bottom: 0.25rem;
            cursor: pointer;
        }

        .event-indicator:hover {
            background: var(--primary);
            color: white;
        }

        .event-popup {
            position: absolute;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            min-width: 300px;
            z-index: 1000;
        }

        .event-popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .event-popup-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--secondary);
            cursor: pointer;
        }

        .event-popup-content {
            font-size: 0.875rem;
        }

        .event-popup-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .filters {
            display: flex;
            gap: 1rem;
            margin: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-checkbox {
            width: 18px;
            height: 18px;
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
                        Pipeline
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-primary" onclick="showAddEventModal()">
                    <i class="fas fa-plus me-2"></i>New Event
                </button>
            </div>
        </div>
    </nav>

    <!-- Calendar Header -->
    <div class="calendar-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="calendar-navigation">
                <button class="nav-btn" onclick="previousMonth()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-outline-secondary" onclick="goToToday()">Today</button>
                <button class="nav-btn" onclick="nextMonth()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <span class="current-date">March 2025</span>
                <select class="form-select form-select-sm" style="width: 100px;" onchange="changeView(this.value)">
                    <option value="month">Month</option>
                    <option value="week">Week</option>
                    <option value="day">Day</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <div class="filter-group">
            <input type="checkbox" class="filter-checkbox" id="customerFilter" checked>
            <label for="customerFilter">Customer</label>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="undefinedFilter" checked>
                <label class="form-check-label" for="undefinedFilter">Undefined</label>
            </div>
        </div>
        <div class="filter-group">
            <input type="checkbox" class="filter-checkbox" id="salespersonFilter" checked>
            <label for="salespersonFilter">Salesperson</label>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="rajbirFilter" checked>
                <label class="form-check-label" for="rajbirFilter">Rajbir</label>
            </div>
        </div>
    </div>

    <!-- Month Calendar -->
    <div class="month-calendar">
        <div class="month-grid">
            <!-- Headers -->
            <div class="month-header">Sun</div>
            <div class="month-header">Mon</div>
            <div class="month-header">Tue</div>
            <div class="month-header">Wed</div>
            <div class="month-header">Thu</div>
            <div class="month-header">Fri</div>
            <div class="month-header">Sat</div>

            <!-- Calendar days will be added dynamically -->
        </div>
    </div>

    <!-- Event Popup Template -->
    <div class="event-popup" id="eventPopup" style="display: none;">
        <div class="event-popup-header">
            <h6 class="mb-0">Citibank</h6>
            <button class="event-popup-close" onclick="closeEventPopup()">&times;</button>
        </div>
        <div class="event-popup-content">
            <div class="mb-2">
                <i class="far fa-calendar me-2"></i>
                <span>29 March 2025</span>
            </div>
            <div class="mb-2">
                <strong>Expected Revenue:</strong> 0.00
            </div>
            <div class="mb-2">
                <strong>Customer:</strong>
            </div>
            <div class="mb-2">
                <strong>Properties</strong>
            </div>
            <div class="event-popup-actions">
                <button class="btn btn-secondary btn-sm">Edit</button>
                <button class="btn btn-danger btn-sm">Delete</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        let currentDate = new Date();
        let events = [
            {
                id: 1,
                title: 'Citibank',
                date: '2025-03-29',
                expectedRevenue: 0.00,
                type: 'customer'
            }
        ];

        document.addEventListener('DOMContentLoaded', function () {
            renderCalendar();
        });

        function renderCalendar() {
            const grid = document.querySelector('.month-grid');
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Clear existing days after headers
            const headers = Array.from(grid.querySelectorAll('.month-header'));
            grid.innerHTML = '';
            headers.forEach(header => grid.appendChild(header));

            // Add empty cells for days before the 1st
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'month-day';
                grid.appendChild(emptyDay);
            }

            // Add days
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.className = 'month-day';
                if (i === currentDate.getDate() &&
                    month === currentDate.getMonth() &&
                    year === currentDate.getFullYear()) {
                    day.classList.add('current-day');
                }

                // Add day number
                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = i;
                day.appendChild(dayNumber);

                // Add attendance status (random for demo)
                const status = Math.random();
                if (status > 0.7) {
                    const statusDot = document.createElement('div');
                    statusDot.className = 'attendance-status status-present';
                    day.appendChild(statusDot);
                } else if (status > 0.4) {
                    const statusDot = document.createElement('div');
                    statusDot.className = 'attendance-status status-absent';
                    day.appendChild(statusDot);
                }

                // Add events for this day
                const dayEvents = events.filter(event => {
                    const eventDate = new Date(event.date);
                    return eventDate.getDate() === i &&
                        eventDate.getMonth() === month &&
                        eventDate.getFullYear() === year;
                });

                dayEvents.forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = 'event-indicator';
                    eventEl.textContent = event.title;
                    eventEl.onclick = (e) => {
                        e.stopPropagation();
                        showEventPopup(event, e);
                    };
                    day.appendChild(eventEl);
                });

                day.onclick = () => showAddEventModal(i);
                grid.appendChild(day);
            }
        }

        function showEventPopup(event, clickEvent) {
            const popup = document.getElementById('eventPopup');
            popup.style.display = 'block';

            // Position popup near the click
            const rect = clickEvent.target.getBoundingClientRect();
            popup.style.top = (rect.bottom + window.scrollY + 10) + 'px';
            popup.style.left = rect.left + 'px';

            // Update popup content
            popup.querySelector('h6').textContent = event.title;
            popup.querySelector('.event-popup-content span').textContent = event.date;
        }

        function closeEventPopup() {
            document.getElementById('eventPopup').style.display = 'none';
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        }

        function goToToday() {
            currentDate = new Date();
            renderCalendar();
        }

        function changeView(view) {
            // Implement view switching logic
            console.log('Switching to view:', view);
        }

        // Close popup when clicking outside
        document.addEventListener('click', function (e) {
            const popup = document.getElementById('eventPopup');
            if (!popup.contains(e.target) &&
                !e.target.classList.contains('event-indicator')) {
                popup.style.display = 'none';
            }
        });
    </script>
</body>

</html>