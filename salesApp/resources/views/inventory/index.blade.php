@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">
            <i class="fas fa-boxes text-primary me-3"></i>Inventory Management
        </h1>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <span class="badge bg-primary rounded-pill px-3 py-2">
                <i class="fas fa-cube me-1"></i>{{ $totalProducts }} Products
            </span>
            <span class="badge bg-success rounded-pill px-3 py-2">
                <i class="fas fa-dollar-sign me-1"></i>${{ number_format($totalValue, 2) }} Total Value
            </span>
            <span class="badge bg-warning rounded-pill px-3 py-2">
                <i class="fas fa-exclamation-triangle me-1"></i>{{ $lowStockItems }} Low Stock
            </span>
            <span class="badge bg-danger rounded-pill px-3 py-2">
                <i class="fas fa-times-circle me-1"></i>{{ $outOfStockItems }} Out of Stock
            </span>
        </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
        <a href="{{ route('inventory.export') }}" class="btn btn-success">
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
            <i class="fas fa-list me-2"></i>Product Inventory
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-barcode me-2"></i>Product Code</th>
                        <th><i class="fas fa-tag me-2"></i>Product Name</th>
                        <th><i class="fas fa-folder me-2"></i>Category</th>
                        <th><i class="fas fa-dollar-sign me-2"></i>Unit Price</th>
                        <th><i class="fas fa-warehouse me-2"></i>Stock</th>
                        <th><i class="fas fa-calculator me-2"></i>Total Value</th>
                        <th><i class="fas fa-truck me-2"></i>Supplier</th>
                        <th><i class="fas fa-cogs me-2"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    <tr class="{{ $item->stock_quantity < 10 ? 'table-warning' : ($item->stock_quantity == 0 ? 'table-danger' : '') }}">
                        <td>
                            <strong class="text-primary">{{ $item->product_code }}</strong>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            @if($item->description)
                                <small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $item->category }}</span>
                        </td>
                        <td>
                            <strong class="text-success">${{ number_format($item->unit_price, 2) }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-2">{{ $item->stock_quantity }}</span>
                                @if($item->stock_quantity == 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Empty
                                    </span>
                                @elseif($item->stock_quantity < 10)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Low
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Good
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong class="text-primary">${{ number_format($item->unit_price * $item->stock_quantity, 2) }}</strong>
                        </td>
                        <td>
                            @if($item->supplier)
                                <span class="text-muted">{{ $item->supplier }}</span>
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inventory.show', $item) }}" class="btn btn-outline-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline-primary" title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete Product"
                                            onclick="return confirm('Are you sure? This will delete the product permanently.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>No products found</h5>
                                <p>Start by adding your first product to the inventory.</p>
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
        {{ $inventory->links('pagination::bootstrap-4') }}
    </div>
    <div>
        <a href="{{ route('inventory.history') }}" class="btn btn-info">
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
                    <i class="fas fa-upload me-2"></i>Import Inventory CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-semibold">Choose CSV File</label>
                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            CSV should have columns: Product Code, Product Name, Description, Unit Price, Stock Quantity, Category, Supplier, Last Restocked
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
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete All Inventory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Are you sure you want to delete <strong>ALL {{ $totalProducts }} inventory items</strong>?</p>
                <div class="bg-light p-3 rounded mb-3">
                    <ul class="text-muted mb-0">
                        <li>All products will be permanently deleted</li>
                        <li>Total inventory value: ${{ number_format($totalValue, 2) }}</li>
                        <li>This may affect existing sales records</li>
                    </ul>
                </div>
                <p><strong>Type "DELETE ALL INVENTORY" to confirm:</strong></p>
                <input type="text" id="confirmDeleteInput" class="form-control" placeholder="Type here to confirm...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <form action="{{ route('inventory.destroy.all') }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                        <i class="fas fa-trash-alt me-2"></i>Delete All Inventory
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
            if (this.value === 'DELETE ALL INVENTORY') {
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