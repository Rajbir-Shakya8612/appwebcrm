@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sales Management</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newSaleModal">
                            <i class="fas fa-plus"></i> New Sale
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportSales()">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sales Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Sales</h5>
                                    <h3>{{ number_format($totalSales ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>This Month</h5>
                                    <h3>{{ number_format($monthlySales ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Average Sale</h5>
                                    <h3>{{ number_format($averageSale ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Total Orders</h5>
                                    <h3>{{ number_format($totalOrders ?? 0) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Salesperson</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $sale->created_at->format('M d, Y') }}</td>
                                    <td>{{ $sale->salesperson->name }}</td>
                                    <td>{{ $sale->customer_name }}</td>
                                    <td>{{ number_format($sale->amount) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewSale({{ $sale->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editSale({{ $sale->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSale({{ $sale->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No sales found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Sale Modal -->
<div class="modal fade" id="newSaleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="saleForm">
                    <div class="mb-3">
                        <label class="form-label">Salesperson</label>
                        <select class="form-select" name="salesperson_id" required>
                            @foreach($salespeople as $salesperson)
                            <option value="{{ $salesperson->id }}">{{ $salesperson->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" name="amount" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSale()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function saveSale() {
    const form = document.getElementById('saleForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.sales.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error creating sale');
        }
    });
}

function viewSale(id) {
    window.location.href = `{{ route("admin.sales.show", "") }}/${id}`;
}

function editSale(id) {
    window.location.href = `{{ route("admin.sales.edit", "") }}/${id}`;
}

function deleteSale(id) {
    if (confirm('Are you sure you want to delete this sale?')) {
        fetch(`{{ route("admin.sales.destroy", "") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting sale');
            }
        });
    }
}

function exportSales() {
    window.location.href = '{{ route("admin.sales.export") }}';
}
</script>
@endpush 