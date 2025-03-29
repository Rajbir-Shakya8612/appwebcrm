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

    updateCalendarTitle(); // Update heading

    let weekDays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    weekDays.forEach(day => {
        let header = document.createElement("div");
        header.className = "month-header";
        header.innerText = day;
        calendarGrid.appendChild(header);
    });

    for (let i = 0; i < startDay; i++) {
        let emptyCell = document.createElement("div");
        emptyCell.className = "month-day";
        calendarGrid.appendChild(emptyCell);
    }

    function getRandomColor() {
        const colors = ["#f8d7da", "#d1ecf1", "#d4edda", "#fff3cd", "#cce5ff", "#e2e3e5", "#f5c6cb"];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    for (let day = 1; day <= daysInMonth; day++) {
        let date = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        let dayCell = document.createElement("div");
        dayCell.className = "month-day";
        dayCell.innerHTML = `<div class="day-number">${day}</div>`;
        dayCell.style.backgroundColor = getRandomColor();

        let eventFound = events.find(event => event.start.startsWith(date));
        if (eventFound) {
            let eventIndicator = document.createElement("div");
            eventIndicator.className = "event-indicator";
            eventIndicator.innerText = eventFound.title;
            eventIndicator.style.backgroundColor = eventFound.backgroundColor || "#7952b3";
            eventIndicator.style.borderColor = eventFound.borderColor || "#5a3d99";
            eventIndicator.onclick = () => showEventDetails(eventFound);
            dayCell.appendChild(eventIndicator);
        }

        calendarGrid.appendChild(dayCell);
    }
}

function showEventDetails(event) {
    let isMeeting = event.type === "meeting";
    let icon = isMeeting ? "📅" : "✅";
    let color = isMeeting ? "#4CAF50" : "#2196F3";
    let eventTitle = isMeeting ? "Meeting Details" : "Attendance Details";

    let htmlContent = `
            <div style="text-align: left;">
                <h3 style="color: ${color}; font-size: 18px;">${icon} ${event.title}</h3>
                <hr>
                <p><strong>📅 Date:</strong> ${event.start}</p>
                <p><strong>📍 Location:</strong> ${event.location || "N/A"}</p>
                <p><strong>📝 Description:</strong> ${event.description || "No additional details"}</p>
                ${isMeeting ? `<button class="swal2-confirm swal2-styled" onclick="editMeeting(${event.id})">✏ Edit Meeting</button>` : ''}
            </div>
        `;

    Swal.fire({
        title: eventTitle,
        html: htmlContent,
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
        const colors = ["#FFD700", "#FF5733", "#4CAF50", "#2196F3", "#7952b3", "#f8d7da", "#cce5ff"];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    for (let d = startOfWeek; d <= endOfWeek; d.setDate(d.getDate() + 1)) {
        let dayCell = document.createElement("div");         
        let date = d.toISOString().split("T")[0]; // YYYY-MM-DD
        let dayName = d.toLocaleDateString('en-US', { weekday: 'long' }); // Day Name

        // **Day Name Bold + Centered**
        dayCell.innerHTML = `
            <div style="text-align: center; font-weight: bold; font-size: 16px;">
                ${dayName}
            </div>
            <div style="text-align: center; font-size: 14px;">
                ${date}
            </div>
        `;

        let eventFound = events.find(event => event.start.startsWith(date));
        if (eventFound) {
            let eventIndicator = document.createElement("div");
            eventIndicator.className = "event-indicator";
            eventIndicator.innerText = eventFound.title;
            eventIndicator.style.backgroundColor = eventFound.backgroundColor || getRandomColor();
            eventIndicator.style.color = "#fff"; // White text
            eventIndicator.onclick = () => showEventDetails(eventFound);
            dayCell.appendChild(eventIndicator);
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
        const colors = ["#FFD700", "#FF5733", "#4CAF50", "#2196F3", "#7952b3", "#f8d7da", "#cce5ff"];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    let dayCell = document.createElement("div");
    dayCell.className = "month-day";
    dayCell.style.backgroundColor = getRandomColor(); // Box ke liye random color

    // **Day Name Bold + Centered**
    dayCell.innerHTML = `
        <div style="text-align: center; font-weight: bold; font-size: 16px;">
            ${dayName}
        </div>
        <div style="text-align: center; font-size: 14px;">
            ${today}
        </div>
    `;

    let eventFound = events.find(event => event.start.startsWith(today));
    if (eventFound) {
        let eventIndicator = document.createElement("div");
        eventIndicator.className = "event-indicator";
        eventIndicator.innerText = eventFound.title;
        eventIndicator.style.backgroundColor = eventFound.backgroundColor || getRandomColor();
        eventIndicator.style.color = "#fff"; // White text
        eventIndicator.onclick = () => showEventDetails(eventFound);
        dayCell.appendChild(eventIndicator);
    }

    document.getElementById("calendarGrid").appendChild(dayCell);
}



document.getElementById("prevMonth").onclick = () => changeMonth(-1);
document.getElementById("nextMonth").onclick = () => changeMonth(1);
document.getElementById("weekView").onclick = showWeekView;
document.getElementById("dayView").onclick = showDayView;
document.getElementById("monthView").onclick = generateCalendar;

generateCalendar();
