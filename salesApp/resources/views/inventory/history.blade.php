@extends('layouts.app')

@section('title', 'Inventory History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">
            <i class="fas fa-history text-primary me-3"></i>Inventory History
        </h1>
        <p class="text-muted">Complete record of all inventory items and their sales performance</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Inventory
        </a>
        <a href="{{ route('inventory.export') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>Export All
        </a>
    </div>
</div>

<!-- Modern Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-cube text-primary fa-2x mb-2"></i>
            <h5>Total Products</h5>
            <h3>{{ $stats['total_products'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-dollar-sign text-success fa-2x mb-2"></i>
            <h5>Total Value</h5>
            <h3>${{ number_format($stats['total_value'], 0) }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
            <h5>Low Stock</h5>
            <h3>{{ $stats['low_stock_items'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>
            <h5>Out of Stock</h5>
            <h3>{{ $stats['out_of_stock'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-shopping-cart text-info fa-2x mb-2"></i>
            <h5>Sales Made</h5>
            <h3>{{ $stats['total_sales_transactions'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-chart-line text-dark fa-2x mb-2"></i>
            <h5>Revenue Gen</h5>
            <h3>${{ number_format($stats['total_revenue'], 0) }}</h3>
        </div>
    </div>
</div>

<!-- Inventory Performance Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-chart-bar me-2"></i>Inventory Performance
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-barcode me-1"></i>Code</th>
                        <th><i class="fas fa-tag me-1"></i>Product Name</th>
                        <th><i class="fas fa-folder me-1"></i>Category</th>
                        <th><i class="fas fa-warehouse me-1"></i>Stock</th>
                        <th><i class="fas fa-dollar-sign me-1"></i>Unit Price</th>
                        <th><i class="fas fa-calculator me-1"></i>Stock Value</th>
                        <th><i class="fas fa-shopping-cart me-1"></i>Sales</th>
                        <th><i class="fas fa-sort-numeric-up me-1"></i>Qty Sold</th>
                        <th><i class="fas fa-chart-line me-1"></i>Revenue</th>
                        <th><i class="fas fa-calendar me-1"></i>Last Restocked</th>
                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory as $item)
                    <tr class="{{ $item->stock_quantity < 10 ? 'table-warning' : ($item->stock_quantity == 0 ? 'table-danger' : '') }}">
                        <td><strong class="text-primary">{{ $item->product_code }}</strong></td>
                        <td>
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            @if($item->supplier)
                                <small class="text-muted">
                                    <i class="fas fa-truck me-1"></i>{{ $item->supplier }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $item->category }}</span>
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
                        <td><strong class="text-success">${{ number_format($item->unit_price, 2) }}</strong></td>
                        <td><strong class="text-primary">${{ number_format($item->unit_price * $item->stock_quantity, 2) }}</strong></td>
                        <td>
                            @if($item->total_sales > 0)
                                <span class="badge bg-primary">{{ $item->total_sales }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            @if($item->total_quantity_sold > 0)
                                <span class="fw-bold">{{ $item->total_quantity_sold }}</span>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>
                        <td>
                            @if($item->total_revenue > 0)
                                <strong class="text-success">${{ number_format($item->total_revenue, 2) }}</strong>
                            @else
                                <span class="text-muted">$0.00</span>
                            @endif
                        </td>
                        <td>
                            @if($item->last_restocked)
                                <span class="text-success">{{ $item->last_restocked->format('M d, Y') }}</span>
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inventory.show', $item) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-outline-primary btn-sm" title="Edit Product">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>No inventory items found</h5>
                                <p>Start by adding products to your inventory.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $inventory->links('pagination::bootstrap-4') }}
</div>
@endsection