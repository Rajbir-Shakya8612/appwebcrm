// Dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
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

