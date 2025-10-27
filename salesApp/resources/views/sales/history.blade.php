@extends('layouts.app')

@section('title', 'Sales Transaction History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">
            <i class="fas fa-history text-primary me-3"></i>Sales Transaction History
        </h1>
        <p class="text-muted">Complete record of all sales transactions and performance metrics</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Sales
        </a>
        <a href="{{ route('sales.export') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>Export All
        </a>
    </div>
</div>

<!-- Modern Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-shopping-cart text-primary fa-2x mb-2"></i>
            <h5>Total Transactions</h5>
            <h3>{{ $stats['total_transactions'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
            <h5>Completed</h5>
            <h3>{{ $stats['completed_sales'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-clock text-warning fa-2x mb-2"></i>
            <h5>Pending</h5>
            <h3>{{ $stats['pending_sales'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>
            <h5>Cancelled</h5>
            <h3>{{ $stats['cancelled_sales'] }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-dollar-sign text-info fa-2x mb-2"></i>
            <h5>Total Revenue</h5>
            <h3>${{ number_format($stats['total_revenue'], 0) }}</h3>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <i class="fas fa-chart-line text-dark fa-2x mb-2"></i>
            <h5>Avg Sale</h5>
            <h3>${{ number_format($stats['average_sale'], 0) }}</h3>
        </div>
    </div>
</div>

<!-- Transaction History Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list-alt me-2"></i>All Transactions
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar me-1"></i>Date</th>
                        <th><i class="fas fa-receipt me-1"></i>Transaction ID</th>
                        <th><i class="fas fa-user me-1"></i>Customer</th>
                        <th><i class="fas fa-box me-1"></i>Product</th>
                        <th><i class="fas fa-sort-numeric-up me-1"></i>Qty</th>
                        <th><i class="fas fa-dollar-sign me-1"></i>Unit Price</th>
                        <th><i class="fas fa-calculator me-1"></i>Total</th>
                        <th><i class="fas fa-user-tie me-1"></i>Sales Person</th>
                        <th><i class="fas fa-info-circle me-1"></i>Status</th>
                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr class="{{ $sale->status === 'cancelled' ? 'table-secondary' : '' }}">
                        <td>
                            <div class="fw-semibold">{{ $sale->created_at->format('M d, Y') }}</div>
                            <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <strong class="text-primary">{{ $sale->transaction_id }}</strong>
                            <br><small class="text-muted">{{ $sale->sale_date->format('M d, Y') }}</small>
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
                            <span class="badge bg-light text-dark border">{{ $sale->quantity }}</span>
                        </td>
                        <td>
                            <strong class="text-success">${{ number_format($sale->unit_price, 2) }}</strong>
                        </td>
                        <td>
                            <strong class="text-primary">${{ number_format($sale->total_amount, 2) }}</strong>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $sale->sales_person }}</div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }}">
                                <i class="fas fa-{{ $sale->status === 'completed' ? 'check' : ($sale->status === 'pending' ? 'clock' : 'times') }} me-1"></i>
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-primary btn-sm" title="Edit Sale">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <h5>No transactions found</h5>
                                <p>Start by adding sales transactions to your system.</p>
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
    {{ $sales->links('pagination::bootstrap-4') }}
</div>
@endsection