<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Opportunity - CRM Pipeline</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f6f8;
        }
        .top-bar {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem;
        }
        .content-wrapper {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-badge.won {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-badge.pending {
            background: #fff3e0;
            color: #ef6c00;
        }
        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-date {
            color: #6c757d;
            font-size: 0.875rem;
        }
        .stage-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
        }
        .stage-arrow {
            color: #adb5bd;
        }
        .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .company-logo {
            width: 64px;
            height: 64px;
            border-radius: 8px;
        }
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .social-link {
            color: #6c757d;
            text-decoration: none;
        }
        .social-link:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-bar">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="pipeline.html" class="btn btn-link text-dark">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h5 class="mb-0">Edit Opportunity</h5>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="window.location.href='pipeline.html'">Cancel</button>
                <button class="btn btn-primary" onclick="saveOpportunityChanges()">Save Changes</button>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="row">
            <!-- Main Content -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="company-info">
                            <img src="https://via.placeholder.com/64" alt="Company Logo" class="company-logo">
                            <div>
                                <h4 id="companyNameDisplay">Company Name</h4>
                                <div class="social-links">
                                    <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                                    <a href="#" class="social-link"><i class="fas fa-globe"></i></a>
                                </div>
                            </div>
                        </div>

                        <form id="editOpportunityForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Expected Revenue</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" class="form-control" id="expectedRevenue" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Probability</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="probability" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contact</label>
                                <input type="text" class="form-control" id="contactName" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" id="contactEmail" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="contactPhone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control" id="tags" placeholder="Add tags...">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Internal Notes</label>
                                <textarea class="form-control" id="internalNotes" rows="4"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Status Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Current Stage</label>
                            <select class="form-select" id="currentStage">
                                <option value="new">New</option>
                                <option value="qualified">Qualified</option>
                                <option value="proposition">Proposition</option>
                                <option value="won">Won</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expected Closing</label>
                            <input type="date" class="form-control" id="expectedClosing">
                        </div>
                    </div>
                </div>

                <!-- Activity Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Recent Activity</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-item">
                            <div class="stage-change">
                                <span class="status-badge won">Won</span>
                                <i class="fas fa-arrow-right stage-arrow"></i>
                                <span class="status-badge pending">Pending</span>
                            </div>
                            <div class="activity-date">22 Mar, 6:08 pm</div>
                        </div>
                        <div class="activity-item">
                            <div>Stage changed to Qualified</div>
                            <div class="activity-date">22 Mar, 6:05 pm</div>
                        </div>
                        <div class="activity-item">
                            <div>Opportunity created by Odoo Lead Generation</div>
                            <div class="activity-date">22 Mar, 6:05 pm</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Get opportunity ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const opportunityId = urlParams.get('id');

        // Load opportunity data
        document.addEventListener('DOMContentLoaded', function() {
            if (opportunityId) {
                loadOpportunityData(opportunityId);
            }
        });

        function loadOpportunityData(id) {
            // Get data from localStorage
            const pipelineData = JSON.parse(localStorage.getItem('pipelineData'));
            let opportunity = null;

            // Find the opportunity
            Object.keys(pipelineData).forEach(stage => {
                const found = pipelineData[stage].find(opp => opp.id === parseInt(id));
                if (found) {
                    opportunity = found;
                }
            });

            if (opportunity) {
                // Populate form fields
                document.getElementById('companyNameDisplay').textContent = opportunity.company;
                document.getElementById('expectedRevenue').value = opportunity.value;
                document.getElementById('probability').value = opportunity.probability || 0;
                document.getElementById('contactEmail').value = opportunity.email || '';
                document.getElementById('contactPhone').value = opportunity.phone || '';
                document.getElementById('currentStage').value = getCurrentStage(opportunity.id, pipelineData);
            }
        }

        function getCurrentStage(id, pipelineData) {
            let currentStage = 'new';
            Object.keys(pipelineData).forEach(stage => {
                if (pipelineData[stage].some(opp => opp.id === id)) {
                    currentStage = stage;
                }
            });
            return currentStage;
        }

        function saveOpportunityChanges() {
            // Get data from localStorage
            const pipelineData = JSON.parse(localStorage.getItem('pipelineData'));
            
            // Get form values
            const expectedRevenue = parseFloat(document.getElementById('expectedRevenue').value);
            const probability = parseInt(document.getElementById('probability').value);
            const email = document.getElementById('contactEmail').value;
            const phone = document.getElementById('contactPhone').value;
            const newStage = document.getElementById('currentStage').value;
            
            // Find and update the opportunity
            let oldStage = null;
            let opportunity = null;

            Object.keys(pipelineData).forEach(stage => {
                const index = pipelineData[stage].findIndex(opp => opp.id === parseInt(opportunityId));
                if (index !== -1) {
                    oldStage = stage;
                    opportunity = pipelineData[stage][index];
                    
                    // Update opportunity data
                    opportunity.value = expectedRevenue;
                    opportunity.probability = probability;
                    opportunity.email = email;
                    opportunity.phone = phone;
                    
                    // Remove from old stage if stage has changed
                    if (stage !== newStage) {
                        pipelineData[stage].splice(index, 1);
                    }
                }
            });

            // Add to new stage if stage has changed
            if (oldStage !== newStage && opportunity) {
                pipelineData[newStage].push(opportunity);
            }

            // Save updated data
            localStorage.setItem('pipelineData', JSON.stringify(pipelineData));

            // Redirect back to pipeline view
            window.location.href = 'pipeline.html';
        }
    </script>
</body>
</html> 