<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Pipeline Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #7952b3;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            background-color: #f5f6f8;
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid #e9ecef;
        }

        .brand-logo {
            width: 32px;
            height: 32px;
        }

        .nav-link {
            color: var(--dark);
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }

        .search-bar {
            max-width: 400px;
            border-radius: 20px;
            background: var(--light);
        }

        .search-bar input {
            border: none;
            background: transparent;
        }

        .search-bar input:focus {
            box-shadow: none;
        }

        .pipeline-header {
            background: white;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .pipeline-stage {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            min-height: 400px;
        }

        .pipeline-stage-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .stage-title {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stage-count {
            background: var(--light);
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            font-size: 0.875rem;
            color: var(--secondary);
        }

        .pipeline-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: grab;
        }

        .pipeline-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }

        .pipeline-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .pipeline-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .opportunity-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .opportunity-company {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .opportunity-value {
            font-weight: 600;
            color: var(--success);
        }

        .pipeline-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e9ecef;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: -8px;
            border: 2px solid white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--light);
            color: var(--secondary);
            border: none;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            background: var(--primary);
            color: white;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-edit {
            background: var(--warning);
            color: white;
            border: none;
            padding: 0.25rem 1rem;
            border-radius: 4px;
        }

        .btn-delete {
            background: var(--danger);
            color: white;
            border: none;
            padding: 0.25rem 1rem;
            border-radius: 4px;
        }

        .color-picker {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .color-option {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .color-option:hover {
            transform: scale(1.1);
        }

        .color-option.selected {
            border-color: var(--primary);
        }

        .pipeline-cards {
            min-height: 200px;
            padding: 0.5rem 0;
        }

        .card-label {
            height: 4px;
            width: 100%;
            border-radius: 2px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                <img src="https://via.placeholder.com/32" alt="CRM" class="brand-logo me-2">
                <a class="navbar-brand" href="#">CRM</a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button">
                        Sales
                    </a>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="search-bar input-group">
                    <span class="input-group-text bg-transparent border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
                
                <button class="btn btn-icon">
                    <i class="far fa-bell"></i>
                </button>
                <img src="https://via.placeholder.com/32" alt="Profile" class="avatar">
            </div>
        </div>
    </nav>

    <!-- Secondary Navigation -->
    <div class="container-fluid px-4 pipeline-header">
        <div class="row align-items-center">
            <div class="col">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Pipeline</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Generate Leads</a>
                    </li>
                </ul>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newOpportunityModal">
                    <i class="fas fa-plus me-2"></i>New
                </button>
            </div>
        </div>
    </div>

    <!-- Pipeline Content -->
    <div class="container-fluid px-4">
        <div class="row flex-nowrap overflow-auto">
            <!-- New Stage -->
            <div class="col-3">
                <div class="pipeline-stage" data-stage="new">
                    <div class="pipeline-stage-header">
                        <div class="stage-title">
                            <span>New</span>
                            <span class="stage-count">0</span>
                        </div>
                        <button class="btn btn-icon" onclick="showNewOpportunityModal('new')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="pipeline-cards">
                        <!-- Cards will be added here -->
                    </div>
                </div>
            </div>

            <!-- Qualified Stage -->
            <div class="col-3">
                <div class="pipeline-stage" data-stage="qualified">
                    <div class="pipeline-stage-header">
                        <div class="stage-title">
                            <span>Qualified</span>
                            <span class="stage-count">0</span>
                        </div>
                        <button class="btn btn-icon" onclick="showNewOpportunityModal('qualified')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="pipeline-cards">
                        <!-- Cards will be added here -->
                    </div>
                </div>
            </div>

            <!-- Proposition Stage -->
            <div class="col-3">
                <div class="pipeline-stage" data-stage="proposition">
                    <div class="pipeline-stage-header">
                        <div class="stage-title">
                            <span>Proposition</span>
                            <span class="stage-count">0</span>
                        </div>
                        <button class="btn btn-icon" onclick="showNewOpportunityModal('proposition')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="pipeline-cards">
                        <!-- Cards will be added here -->
                    </div>
                </div>
            </div>

            <!-- Won Stage -->
            <div class="col-3">
                <div class="pipeline-stage" data-stage="won">
                    <div class="pipeline-stage-header">
                        <div class="stage-title">
                            <span>Won</span>
                            <span class="stage-count">0</span>
                        </div>
                        <button class="btn btn-icon" onclick="showNewOpportunityModal('won')">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="pipeline-cards">
                        <!-- Cards will be added here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Opportunity Modal -->
    <div class="modal fade" id="newOpportunityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Opportunity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="opportunityForm">
                        <input type="hidden" id="opportunityStage" name="stage">
                        <div class="mb-3">
                            <label class="form-label">Opportunity Name</label>
                            <input type="text" class="form-control" id="opportunityName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company</label>
                            <input type="text" class="form-control" id="companyName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Value</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="opportunityValue" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contactEmail">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Phone</label>
                            <input type="tel" class="form-control" id="contactPhone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color Label</label>
                            <div class="color-picker" id="colorPicker">
                                <div class="color-option" style="background: #dc3545" data-color="#dc3545"></div>
                                <div class="color-option" style="background: #28a745" data-color="#28a745"></div>
                                <div class="color-option" style="background: #ffc107" data-color="#ffc107"></div>
                                <div class="color-option" style="background: #17a2b8" data-color="#17a2b8"></div>
                                <div class="color-option" style="background: #6610f2" data-color="#6610f2"></div>
                                <div class="color-option" style="background: #fd7e14" data-color="#fd7e14"></div>
                                <div class="color-option" style="background: #20c997" data-color="#20c997"></div>
                                <div class="color-option" style="background: #e83e8c" data-color="#e83e8c"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveOpportunity()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Menu Modal -->
    <div class="modal fade" id="editMenuModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning w-100" onclick="editOpportunity()">
                            <i class="fas fa-edit me-2"></i>Edit
                        </button>
                        <button class="btn btn-danger w-100" onclick="deleteOpportunity()">
                            <i class="fas fa-trash-alt me-2"></i>Delete
                        </button>
                        <div class="color-picker mt-3" id="editColorPicker">
                            <!-- Color options will be added dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/pipeline.js') }}"></script>
</body>
</html> 