// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize drag and drop
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
        const oldStatusId = el.parentNode.id.replace('status-', '');

        // Update lead status via AJAX
        $.ajax({
            url: `/salesperson/leads/${leadId}/status`,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            data: {
                status_id: newStatusId,
                _token: document.querySelector('meta[name="csrf-token"]').content
            },
            success: function(response) {
                if (response.success) {
                    // Create activity for status change
                    $.ajax({
                        url: `/salesperson/leads/${leadId}/activity`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        data: {
                            type: 'Status Changed',
                            description: `Lead moved from ${oldStatusId} to ${newStatusId}`,
                            _token: document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Lead status updated successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function() {
                // Revert the card to its original position
                dragula(containers).cancel();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to update lead status'
                });
            }
        });
    });

    // Handle lead actions
    document.querySelectorAll('.lead-action').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.dataset.action;
            const leadId = this.dataset.leadId;

            switch(action) {
                case 'edit':
                    editLead(leadId);
                    break;
                case 'delete':
                    deleteLead(leadId);
                    break;
                case 'call':
                    window.location.href = `tel:${this.dataset.phone}`;
                    break;
                case 'email':
                    window.location.href = `mailto:${this.dataset.email}`;
                    break;
                case 'whatsapp':
                    window.open(`https://wa.me/${this.dataset.phone}`, '_blank');
                    break;
            }
        });
    });

    // Handle meeting calendar
    const calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: JSON.parse(calendarEl.dataset.events || '[]'),
            eventClick: function(info) {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <p class="text-sm text-gray-600">${info.event.extendedProps.description}</p>
                        <p class="text-sm text-gray-600">Location: ${info.event.extendedProps.location}</p>
                        <p class="text-sm text-gray-600">Time: ${info.event.start.toLocaleTimeString()}</p>
                    `,
                    icon: 'info'
                });
            }
        });
        calendar.render();
    }
});

// Delete lead function
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
                url: `/salesperson/leads/${leadId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Lead has been deleted.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Remove the lead card from the DOM
                        document.querySelector(`[data-lead-id="${leadId}"]`).remove();
                    }
                },
                error: function() {
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

function checkIn() {
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            $.ajax({
                url: '/salesperson/attendance/checkin',
                method: 'POST',
                data: {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    address: 'Current Location',
                    _token: document.querySelector('meta[name="csrf-token"]').content
                },
                success: function(response) {
                    if (response.success) {
                        // Update check-in time
                        document.getElementById('checkInTime').textContent = response.time;
                        
                        // Update attendance status
                        updateAttendanceStatus(response);
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Start location tracking
                        isTracking = true;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to check in'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to check in. Please try again.'
                    });
                }
            });
        }, function(error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to get location. Please enable location services.'
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

// Function to update attendance status
function updateAttendanceStatus(response) {
    // Update check-in time
    if (response.check_in_time) {
        document.getElementById('checkInTime').textContent = response.check_in_time;
    }
    
    // Update check-out time
    if (response.check_out_time) {
        document.getElementById('checkOutTime').textContent = response.check_out_time;
    }
    
    // Update working hours
    if (response.working_hours) {
        document.querySelector('.working-hours-card p.h5').textContent = response.working_hours + ' hrs';
    }
    
    // Update late status badge
    const lateBadge = document.querySelector('.check-in-card .badge');
    if (response.status === 'late') {
        if (!lateBadge) {
            const badge = document.createElement('span');
            badge.className = 'badge bg-warning text-dark';
            badge.textContent = 'Late';
            document.querySelector('.check-in-card').appendChild(badge);
        }
    } else if (lateBadge) {
        lateBadge.remove();
    }
    
    // Update buttons
    const checkInBtn = document.querySelector('button[onclick="checkIn()"]');
    const checkOutBtn = document.querySelector('button[onclick="checkOut()"]');
    
    if (response.check_in_time && !response.check_out_time) {
        if (checkInBtn) checkInBtn.style.display = 'none';
        if (checkOutBtn) checkOutBtn.style.display = 'block';
    } else if (response.check_in_time && response.check_out_time) {
        if (checkInBtn) checkInBtn.style.display = 'none';
        if (checkOutBtn) checkOutBtn.style.display = 'none';
    }
}

// Lead Form Submission
document.getElementById('addLeadForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Add CSRF token
    data._token = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch('/salesperson/leads', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: result.message,
                timer: 1500,
                showConfirmButton: false
            });
            // Refresh the page to show the new lead
            window.location.reload();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: result.message || 'Failed to save lead'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to save lead. Please try again.'
        });
    });
}); 