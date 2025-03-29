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
            return el !== target;
        },
        direction: 'horizontal',
        animation: 150,
        onDrop: function(el, target) {
            const leadId = el.dataset.leadId;
            const newStatusId = target.id.replace('status-', '');
            
            // Update lead status via AJAX
            $.ajax({
                url: `/salesperson/leads/${leadId}/status`,
                method: 'PUT',
                data: {
                    status_id: newStatusId,
                    _token: document.querySelector('meta[name="csrf-token"]').content
                },
                success: function(response) {
                    if (response.success) {
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update lead status'
                    });
                }
            });
        }
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

// Edit lead function
function editLead(leadId) {
    $.get(`/salesperson/leads/${leadId}`, function (lead) {
        Swal.fire({
            width: '700px',
            title: 'Edit Lead',
            customClass: {
                popup: 'swal-wide'
            },
            html: `
                <form id="editLeadForm" class="space-y-4">
                    <input type="hidden" name="lead_id" id="lead_id" value="${lead.id}">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" value="${lead.name}" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" name="phone" id="phone" value="${lead.phone}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="${lead.email}" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company" class="form-label">Company</label>
                            <input type="text" name="company" id="company" value="${lead.company}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="source" class="form-label">Source</label>
                            <select name="source" id="source" class="form-select" required>
                                <option value="website" ${lead.source === 'website' ? 'selected' : ''}>Website</option>
                                <option value="referral" ${lead.source === 'referral' ? 'selected' : ''}>Referral</option>
                                <option value="social" ${lead.source === 'social' ? 'selected' : ''}>Social Media</option>
                                <option value="other" ${lead.source === 'other' ? 'selected' : ''}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" required>${lead.notes || ''}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="expected_amount" class="form-label">Expected Amount</label>
                        <input type="number" name="expected_amount" id="expected_amount" value="${lead.expected_amount}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status_id" class="form-label">Status ID</label>
                        <input type="hidden" name="status_id" id="status_id" value="${lead.status_id}">
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Update Lead',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const form = document.getElementById('editLeadForm');
                const formData = new FormData(form);

                return $.ajax({
                    url: `/salesperson/leads/${leadId}`,
                    method: 'PUT',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Failed to update lead');
                    }
                    return response;
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Lead updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                });
                // Refresh the page to show the updated lead
                window.location.reload();
            }
        }).catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Failed to update lead. Please try again.'
            });
        });
    }).fail(function () {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Failed to load lead data. Please try again.'
        });
    });
}
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