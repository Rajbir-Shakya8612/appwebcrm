<!-- Edit Lead Modal -->
<div class="modal fade" id="editLeadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lead</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editLeadForm">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Company</label>
                                <input type="text" class="form-control" name="company">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Source</label>
                                <select class="form-control" name="source" required>
                                    <option value="">Select Source</option>
                                    <option value="website">Website</option>
                                    <option value="referral">Referral</option>
                                    <option value="social">Social Media</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Expected Value</label>
                                <input type="number" class="form-control" name="expected_value" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Next Follow Up</label>
                                <input type="date" class="form-control" name="next_follow_up">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="new">New</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="qualified">Qualified</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="negotiation">Negotiation</option>
                                    <option value="converted">Converted</option>
                                    <option value="lost">Lost</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="updateLead">Update Lead</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Edit lead
function editLead(lead) {
    const form = document.getElementById('editLeadForm');
    form.querySelector('[name="id"]').value = lead.id;
    form.querySelector('[name="name"]').value = lead.name;
    form.querySelector('[name="phone"]').value = lead.phone;
    form.querySelector('[name="email"]').value = lead.email || '';
    form.querySelector('[name="company"]').value = lead.company || '';
    form.querySelector('[name="source"]').value = lead.source;
    form.querySelector('[name="expected_value"]').value = lead.expected_value || '';
    form.querySelector('[name="next_follow_up"]').value = lead.next_follow_up || '';
    form.querySelector('[name="status"]').value = lead.status;
    form.querySelector('[name="notes"]').value = lead.notes || '';
    
    $('#editLeadModal').modal('show');
}

// Update lead
document.getElementById('updateLead').addEventListener('click', function() {
    const form = document.getElementById('editLeadForm');
    const formData = new FormData(form);
    const leadId = formData.get('id');
    
    fetch(`/admin/leads/${leadId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const index = leads.findIndex(l => l.id === leadId);
            if (index !== -1) {
                leads[index] = data.lead;
                updateKanbanBoard();
                updateStatistics();
                $('#editLeadModal').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Lead updated successfully'
                });
            }
        }
    });
});

// Delete lead
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
            fetch(`/admin/leads/${leadId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    leads = leads.filter(l => l.id !== leadId);
                    updateKanbanBoard();
                    updateStatistics();
                    
                    Swal.fire(
                        'Deleted!',
                        'Lead has been deleted.',
                        'success'
                    );
                }
            });
        }
    });
}
</script>
@endpush 