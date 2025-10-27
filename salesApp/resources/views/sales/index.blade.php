@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">
            <i class="fas fa-chart-line text-primary me-3"></i>Sales Management
        </h1>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <i class="fas fa-shopping-cart me-1"></i>{{ $totalSales }} Total Sales
            </span>
            <span class="badge bg-success rounded-pill px-3 py-2">
                <i class="fas fa-dollar-sign me-1"></i>${{ number_format($totalRevenue, 2) }} Revenue
            </span>
            <span class="badge bg-warning rounded-pill px-3 py-2">
                <i class="fas fa-clock me-1"></i>{{ $pendingSales }} Pending
            </span>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Sale
        </a>
        <a href="{{ route('sales.export') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>Export CSV
        </a>
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-upload me-2"></i>Import CSV
        </button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
            <i class="fas fa-trash-alt me-2"></i>Delete All
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Sales Transactions
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-receipt me-2"></i>Transaction ID</th>
                        <th><i class="fas fa-user me-2"></i>Customer</th>
                        <th><i class="fas fa-box me-2"></i>Product</th>
                        <th><i class="fas fa-dollar-sign me-2"></i>Unit Price</th>
                        <th><i class="fas fa-sort-numeric-up me-2"></i>Quantity</th>
                        <th><i class="fas fa-calculator me-2"></i>Total Amount</th>
                        <th><i class="fas fa-calendar me-2"></i>Sale Date</th>
                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>
                            <strong class="text-primary">{{ $sale->transaction_id }}</strong>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $sale->customer_name }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $sale->product_name }}</div>
                            @if($sale->inventory)
                                <small class="text-muted">
                                    <i class="fas fa-barcode me-1"></i>{{ $sale->inventory->product_code }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success">${{ number_format($sale->unit_price, 2) }}</strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $sale->quantity }}</span>
                        </td>
                        <td>
                            <strong class="text-primary">${{ number_format($sale->total_amount, 2) }}</strong>
                        </td>
                        <td>
                            {{ $sale->sale_date->format('M d, Y') }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }}">
                                <i class="fas fa-{{ $sale->status === 'completed' ? 'check' : ($sale->status === 'pending' ? 'clock' : 'times') }} me-1"></i>
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary" title="Edit Sale">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete Sale"
                                            onclick="return confirm('Are you sure? This will return stock to inventory.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <h5>No sales found</h5>
                                <p>Start by adding your first sale transaction.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination and History Button -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div>
        {{ $sales->links('pagination::bootstrap-4') }}
    </div>
    <div>
        <a href="{{ route('sales.history') }}" class="btn btn-info">
            <i class="fas fa-history me-2"></i>Transaction History
        </a>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Import Sales CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('sales.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-semibold">Choose CSV File</label>
                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            CSV should have columns: Transaction ID, Customer Name, Product Name, Unit Price, Quantity, Sale Date, Sales Person, Status
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete All Sales
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to delete <strong>ALL {{ $totalSales }} sales records</strong>?</p>
                <div class="bg-light p-3 rounded mb-3">
                    <ul class="text-muted mb-0">
                        <li>All sales transactions will be permanently deleted</li>
                        <li>Stock will be returned to inventory for completed sales</li>
                        <li>Transaction history will be lost</li>
                    </ul>
                </div>
                <p><strong>Type "DELETE ALL SALES" to confirm:</strong></p>
                <input type="text" id="confirmDeleteInput" class="form-control" placeholder="Type here to confirm...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <form action="{{ route('sales.destroy.all') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="fas fa-trash-alt me-2"></i>Delete All Sales
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmInput = document.getElementById('confirmDeleteInput');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmInput && confirmBtn) {
        confirmInput.addEventListener('input', function() {
            if (this.value === 'DELETE ALL SALES') {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('btn-secondary');
                confirmBtn.classList.add('btn-danger');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.remove('btn-danger');
                confirmBtn.classList.add('btn-secondary');
            }
        });
    }
});
</script>
@endsection