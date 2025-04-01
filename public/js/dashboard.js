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
});

