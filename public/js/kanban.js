document.addEventListener('DOMContentLoaded', function() {
    const kanbanCards = document.querySelectorAll('.kanban-card');
    const kanbanColumns = document.querySelectorAll('.kanban-cards');
    let draggedCard = null;

    // Add drag event listeners to each card
    kanbanCards.forEach(card => {
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
        card.setAttribute('draggable', true);
    });

    // Add drop event listeners to each column
    kanbanColumns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
    });

    function handleDragStart(e) {
        draggedCard = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', this.dataset.leadId);
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        draggedCard = null;
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDrop(e) {
        e.preventDefault();
        if (draggedCard) {
            const leadId = e.dataTransfer.getData('text/plain');
            const newStatusId = this.dataset.statusId;
            const oldColumn = draggedCard.parentElement;
            
            // Update the card's position in the DOM
            this.appendChild(draggedCard);
            
            // Update lead counts in columns
            updateColumnCounts(oldColumn, this);
            
            // Send AJAX request to update status
            updateLeadStatus(leadId, newStatusId);
        }
        return false;
    }

    function updateColumnCounts(oldColumn, newColumn) {
        // Update old column count
        const oldCountEl = oldColumn.parentElement.querySelector('.column-lead-count');
        let oldCount = parseInt(oldCountEl.textContent);
        oldCountEl.textContent = oldCount - 1;

        // Update new column count
        const newCountEl = newColumn.parentElement.querySelector('.column-lead-count');
        let newCount = parseInt(newCountEl.textContent);
        newCountEl.textContent = newCount + 1;
    }

    function updateLeadStatus(leadId, statusId) {
        fetch(`/salesperson/leads/${leadId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                status_id: statusId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                Swal.fire({
                    icon: 'success',
                    title: 'Status Updated',
                    text: 'Lead status has been updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'Failed to update lead status'
            });
        });
    }

    // Function to generate star rating HTML
    function generateStarRating(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<i class="fas fa-star ${i <= rating ? 'star' : 'star-empty'}"></i>`;
        }
        return stars;
    }

    // Function to add new lead to specific column
    window.addNewLead = function(statusId) {
        const modal = document.getElementById('leadModal');
        const form = document.getElementById('addLeadForm');
        form.querySelector('#status').value = statusId;
        new bootstrap.Modal(modal).show();
    }

    // Handle lead form submission
    document.addEventListener('DOMContentLoaded', function () {
        const addLeadForm = document.getElementById('addLeadForm');
        if (addLeadForm) {
            addLeadForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('/salesperson/leads', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const column = document.querySelector(`[data-status-id="${data.lead.status_id}"] .kanban-cards`);
                            const newCard = createLeadCard(data.lead);
                            column.appendChild(newCard);

                            const countEl = column.parentElement.querySelector('.column-lead-count');
                            let count = parseInt(countEl.textContent);
                            countEl.textContent = count + 1;

                            bootstrap.Modal.getInstance(document.getElementById('leadModal')).hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error(data.message || 'Failed to add lead');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Failed to add lead'
                        });
                    });
            });
        }
    });


    // Function to create a new lead card
    function createLeadCard(lead) {
        const card = document.createElement('div');
        card.className = 'kanban-card';
        card.setAttribute('draggable', true);
        card.dataset.leadId = lead.id;
        
        card.innerHTML = `
            <div class="card-header">
                <div>
                    <h5 class="card-title">${lead.name}</h5>
                    <div class="card-company">${lead.company}</div>
                </div>
                <div class="card-rating">
                    ${generateStarRating(lead.rating || 0)}
                </div>
            </div>
            <div class="card-info">
                <div class="contact-icons">
                    <a href="tel:${lead.phone}" class="contact-icon phone">
                        <i class="fas fa-phone"></i>
                    </a>
                    <a href="mailto:${lead.email}" class="contact-icon email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="https://wa.me/${lead.phone}" class="contact-icon whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
                <div class="card-badges">
                    ${lead.tags ? lead.tags.map(tag => `<span class="badge badge-${tag.toLowerCase()}">${tag}</span>`).join('') : ''}
                </div>
                <div class="follow-up-date">
                    <i class="fas fa-calendar-alt"></i> Follow-up: ${new Date(lead.follow_up_date).toLocaleDateString()}
                </div>
            </div>
            <div class="edit-delete-icons">
                <i class="fas fa-edit edit-icon" onclick="editLead(${lead.id})"></i>
                <i class="fas fa-trash delete-icon" onclick="deleteLead(${lead.id})"></i>
            </div>
        `;
        
        // Add drag event listeners
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragend', handleDragEnd);
        
        return card;
    }
}); 