// Laravel se JavaScript me data pass karna
let currentView = "month";  // Default view
let today = new Date();
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();

function updateCalendarTitle() {
    let monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    document.getElementById("calendarTitle").innerText = `${monthNames[currentMonth]} ${currentYear}`;
}

function generateCalendar() {
    let calendarGrid = document.getElementById("calendarGrid");
    calendarGrid.innerHTML = "";
    let firstDay = new Date(currentYear, currentMonth, 1);
    let lastDay = new Date(currentYear, currentMonth + 1, 0);
    let daysInMonth = lastDay.getDate();
    let startDay = firstDay.getDay();

    updateCalendarTitle();

    let weekDays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    weekDays.forEach(day => {
        let header = document.createElement("div");
        header.className = "month-header";
        header.innerText = day;
        calendarGrid.appendChild(header);
    });

    function getRandomColor() {
        const colors = ["#f8d7da", "#d1ecf1", "#d4edda", "#fff3cd", "#cce5ff", "#e2e3e5", "#f5c6cb"];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    for (let i = 0; i < startDay; i++) {
        let emptyCell = document.createElement("div");
        emptyCell.className = "month-day";
        emptyCell.style.backgroundColor = getRandomColor();
        calendarGrid.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        let date = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        let dayCell = document.createElement("div");
        dayCell.className = "month-day";
        dayCell.style.backgroundColor = getRandomColor();
        dayCell.innerHTML = `<div class="day-number">${day}</div>`;

        // Get all events for this date
        let dayEvents = events.filter(event => event.start.startsWith(date));
        
        if (dayEvents.length > 0) {
            let eventsContainer = document.createElement("div");
            eventsContainer.className = "events-container";
            
            // Group events by type
            let eventsByType = dayEvents.reduce((acc, event) => {
                let type = event.id.split('-')[0];
                if (!acc[type]) acc[type] = [];
                acc[type].push(event);
                return acc;
            }, {});

            // Create buttons for each event type
            Object.entries(eventsByType).forEach(([type, typeEvents]) => {
                let button = document.createElement("button");
                button.className = "event-button";
                
                // Set button styles based on event type
                switch(type) {
                    case 'attendance':
                        button.className += " attendance-btn";
                        button.innerHTML = `<i class="fas fa-clock"></i> Attendance`;
                        break;
                    case 'lead':
                        button.className += " lead-btn";
                        button.innerHTML = `<i class="fas fa-user-plus"></i> Lead`;
                        break;
                    case 'meeting':
                        button.className += " meeting-btn";
                        button.innerHTML = `<i class="fas fa-calendar"></i> Meeting`;
                        break;
                    case 'leave':
                        button.className += " leave-btn";
                        button.innerHTML = `<i class="fas fa-sign-out-alt"></i> Leave`;
                        break;
                    case 'task':
                        button.className += " task-btn";
                        button.innerHTML = `<i class="fas fa-tasks"></i> Task`;
                        break;
                }

                button.onclick = () => showEventDetails(typeEvents[0], type);
                eventsContainer.appendChild(button);
            });

            dayCell.appendChild(eventsContainer);
        }

        calendarGrid.appendChild(dayCell);
    }
}

function showEventDetails(event, type) {
    let icon, color, content;
    
    switch(type) {
        case 'attendance':
            icon = "🕒";
            color = event.status === 'present' ? "#28a745" : (event.status === 'late' ? "#ffc107" : "#dc3545");
            content = `
                <p><strong>Status:</strong> ${event.status}</p>
                <p><strong>Check In:</strong> ${event.check_in_time || 'N/A'}</p>
                <p><strong>Check Out:</strong> ${event.check_out_time || 'N/A'}</p>
                <p><strong>Working Hours:</strong> ${event.working_hours || 'N/A'}</p>
            `;
            break;
            
        case 'lead':
            icon = "👥";
            color = "#007bff";
            content = `
                <p><strong>Name:</strong> ${event.title}</p>
                <p><strong>Company:</strong> ${event.company || 'N/A'}</p>
                <p><strong>Phone:</strong> ${event.phone || 'N/A'}</p>
                <p><strong>Email:</strong> ${event.email || 'N/A'}</p>
                <p><strong>Remark:</strong> ${event.notes || 'N/A'}</p>
                <p><strong>Status:</strong> ${event.status || 'N/A'}</p>
            `;
            break;
            
        case 'meeting':
            icon = "📅";
            color = "#6f42c1";
            content = `
                <p><strong>Title:</strong> ${event.title}</p>
                <p><strong>Location:</strong> ${event.location || 'N/A'}</p>
                <p><strong>Description:</strong> ${event.description || 'N/A'}</p>
                <p><strong>Status:</strong> ${event.status || 'N/A'}</p>
                ${event.attendees ? `<p><strong>Attendees:</strong> ${event.attendees}</p>` : ''}
                <button class="swal2-confirm swal2-styled" onclick="editMeeting('${event.id}')">✏ Edit Meeting</button>
            `;
            break;
            
        case 'leave':
            icon = "🏖️";
            color = "#fd7e14";
            content = `
                <p><strong>Type:</strong> ${event.type} Leave</p>
                <p><strong>Status:</strong> ${event.status}</p>
                <p><strong>From:</strong> ${event.start}</p>
                <p><strong>To:</strong> ${event.end}</p>
                <p><strong>Reason:</strong> ${event.reason || 'N/A'}</p>
            `;
            break;
            
        case 'task':
            icon = "📋";
            color = "#20c997";
            content = `
                <p><strong>Title:</strong> ${event.title}</p>
                <p><strong>Priority:</strong> ${event.priority || 'N/A'}</p>
                <p><strong>Status:</strong> ${event.status || 'N/A'}</p>
                <p><strong>Description:</strong> ${event.description || 'N/A'}</p>
                <p><strong>Due Date:</strong> ${event.start}</p>
            `;
            break;
    }

    Swal.fire({
        title: `${icon} ${type.charAt(0).toUpperCase() + type.slice(1)} Details`,
        html: `
            <div style="text-align: left;">
                <div style="border-left: 4px solid ${color}; padding-left: 10px;">
                    ${content}
                </div>
            </div>
        `,
        showCloseButton: true,
        showCancelButton: false,
        showConfirmButton: false,
        customClass: {
            popup: 'info-popup',
            closeButton: 'custom-close-button'
        }
    });
}

function editMeeting(eventId) {
    window.location.href = `/meetings/edit/${eventId}`;
}

function changeMonth(step) {
    currentMonth += step;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar();
}

function showWeekView() {
    let today = new Date();
    let startOfWeek = new Date(today.setDate(today.getDate() - today.getDay())); // Sunday
    let endOfWeek = new Date(today.setDate(today.getDate() + 6)); // Saturday

    document.getElementById("calendarGrid").innerHTML = "";
    updateCalendarTitle();

    function getRandomColor() {
        const colors = ["#f8d7da", "#d1ecf1", "#d4edda", "#fff3cd", "#cce5ff", "#e2e3e5", "#f5c6cb"];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    for (let d = startOfWeek; d <= endOfWeek; d.setDate(d.getDate() + 1)) {
        let dayCell = document.createElement("div");         
        let date = d.toISOString().split("T")[0]; // YYYY-MM-DD
        let dayName = d.toLocaleDateString('en-US', { weekday: 'long' }); // Day Name

        dayCell.style.backgroundColor = getRandomColor();

        // Day Name Bold + Centered
        dayCell.innerHTML = `
            <div style="text-align: center; font-weight: bold; font-size: 16px;">
                ${dayName}
            </div>
            <div style="text-align: center; font-size: 14px;">
                ${date}
            </div>
        `;

        let dayEvents = events.filter(event => event.start.startsWith(date));
        
        if (dayEvents.length > 0) {
            let eventsContainer = document.createElement("div");
            eventsContainer.className = "events-container";
            
            let eventsByType = dayEvents.reduce((acc, event) => {
                let type = event.id.split('-')[0];
                if (!acc[type]) acc[type] = [];
                acc[type].push(event);
                return acc;
            }, {});

            Object.entries(eventsByType).forEach(([type, typeEvents]) => {
                let button = document.createElement("button");
                button.className = "event-button";
                
                switch(type) {
                    case 'attendance':
                        button.className += " attendance-btn";
                        button.innerHTML = `<i class="fas fa-clock"></i> Attendance`;
                        break;
                    case 'lead':
                        button.className += " lead-btn";
                        button.innerHTML = `<i class="fas fa-user-plus"></i> Lead`;
                        break;
                    case 'meeting':
                        button.className += " meeting-btn";
                        button.innerHTML = `<i class="fas fa-calendar"></i> Meeting`;
                        break;
                    case 'leave':
                        button.className += " leave-btn";
                        button.innerHTML = `<i class="fas fa-sign-out-alt"></i> Leave`;
                        break;
                    case 'task':
                        button.className += " task-btn";
                        button.innerHTML = `<i class="fas fa-tasks"></i> Task`;
                        break;
                }

                button.onclick = () => showEventDetails(typeEvents[0], type);
                eventsContainer.appendChild(button);
            });

            dayCell.appendChild(eventsContainer);
        }

        document.getElementById("calendarGrid").appendChild(dayCell);
    }
}

function showDayView() {
    let todayDate = new Date();
    let today = todayDate.toISOString().split("T")[0]; // YYYY-MM-DD
    let dayName = todayDate.toLocaleDateString('en-US', { weekday: 'long' }); // Day name

    document.getElementById("calendarGrid").innerHTML = "";
    updateCalendarTitle();

    function getRandomColor() {
        const colors = ["#f8d7da", "#d1ecf1", "#d4edda", "#fff3cd", "#cce5ff", "#e2e3e5", "#f5c6cb"];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    let dayCell = document.createElement("div");
    dayCell.className = "month-day";
    dayCell.style.backgroundColor = getRandomColor();

    dayCell.innerHTML = `
        <div style="text-align: center; font-weight: bold; font-size: 16px;">
            ${dayName}
        </div>
        <div style="text-align: center; font-size: 14px;">
            ${today}
        </div>
    `;

    let dayEvents = events.filter(event => event.start.startsWith(today));
    
    if (dayEvents.length > 0) {
        let eventsContainer = document.createElement("div");
        eventsContainer.className = "events-container";
        
        let eventsByType = dayEvents.reduce((acc, event) => {
            let type = event.id.split('-')[0];
            if (!acc[type]) acc[type] = [];
            acc[type].push(event);
            return acc;
        }, {});

        Object.entries(eventsByType).forEach(([type, typeEvents]) => {
            let button = document.createElement("button");
            button.className = "event-button";
            
            switch(type) {
                case 'attendance':
                    button.className += " attendance-btn";
                    button.innerHTML = `<i class="fas fa-clock"></i> Attendance`;
                    break;
                case 'lead':
                    button.className += " lead-btn";
                    button.innerHTML = `<i class="fas fa-user-plus"></i> Lead`;
                    break;
                case 'meeting':
                    button.className += " meeting-btn";
                    button.innerHTML = `<i class="fas fa-calendar"></i> Meeting`;
                    break;
                case 'leave':
                    button.className += " leave-btn";
                    button.innerHTML = `<i class="fas fa-sign-out-alt"></i> Leave`;
                    break;
                case 'task':
                    button.className += " task-btn";
                    button.innerHTML = `<i class="fas fa-tasks"></i> Task`;
                    break;
            }

            button.onclick = () => showEventDetails(typeEvents[0], type);
            eventsContainer.appendChild(button);
        });

        dayCell.appendChild(eventsContainer);
    }

    document.getElementById("calendarGrid").appendChild(dayCell);
}

document.getElementById("prevMonth").onclick = () => changeMonth(-1);
document.getElementById("nextMonth").onclick = () => changeMonth(1);
document.getElementById("weekView").onclick = showWeekView;
document.getElementById("dayView").onclick = showDayView;
document.getElementById("monthView").onclick = generateCalendar;

generateCalendar();
