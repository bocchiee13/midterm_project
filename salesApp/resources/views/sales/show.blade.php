@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2 text-primary"></i>Sale Details - {{ $sale->transaction_id }}
                </h5>
                <div class="btn-group">
                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 mb-3 h-100">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">
                                <i class="fas fa-info-circle me-2"></i>Transaction Information
                            </h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="45%" class="text-muted">
                                        <i class="fas fa-receipt me-2"></i>Transaction ID:
                                    </th>
                                    <td><strong class="text-primary">{{ $sale->transaction_id }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-user me-2"></i>Customer Name:
                                    </th>
                                    <td><strong>{{ $sale->customer_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-box me-2"></i>Product Name:
                                    </th>
                                    <td><strong>{{ $sale->product_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-dollar-sign me-2"></i>Unit Price:
                                    </th>
                                    <td><strong class="text-success">${{ number_format($sale->unit_price, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 mb-3 h-100">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">
                                <i class="fas fa-chart-line me-2"></i>Sale Details
                            </h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="45%" class="text-muted">
                                        <i class="fas fa-sort-numeric-up me-2"></i>Quantity:
                                    </th>
                                    <td>
                                        <span class="badge bg-primary px-3 py-2">{{ $sale->quantity }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-calculator me-2"></i>Total Amount:
                                    </th>
                                    <td><strong class="text-success fs-5">${{ number_format($sale->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-calendar-alt me-2"></i>Sale Date:
                                    </th>
                                    <td><strong>{{ $sale->sale_date->format('F d, Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-user-tie me-2"></i>Sales Person:
                                    </th>
                                    <td><strong>{{ $sale->sales_person }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="bg-light rounded p-4 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold mb-2">
                                <i class="fas fa-info-circle me-2"></i>Transaction Status
                            </h6>
                            <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'pending' ? 'warning' : 'danger') }} px-3 py-2 fs-6">
                                <i class="fas fa-{{ $sale->status === 'completed' ? 'check-circle' : ($sale->status === 'pending' ? 'clock' : 'times-circle') }} me-2"></i>
                                {{ ucfirst($sale->status) }}
                            </span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Created on</small>
                            <div class="fw-semibold">{{ $sale->created_at->format('M d, Y \a\t h:i A') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Calculation Summary Card -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white text-center py-4">
                                <i class="fas fa-calculator fa-3x mb-3 opacity-75"></i>
                                <h6 class="text-uppercase fw-semibold mb-1">Sale Summary</h6>
                                <h2 class="fw-bold mb-2">${{ number_format($sale->total_amount, 2) }}</h2>
                                <p class="mb-0 opacity-75 fs-5">{{ $sale->quantity }} units × ${{ number_format($sale->unit_price, 2) }} each</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($sale->inventory)
                <!-- Product Information Card -->
                <div class="bg-light rounded p-4 mt-3">
                    <h6 class="text-muted text-uppercase fw-semibold mb-3">
                        <i class="fas fa-cube me-2"></i>Product Information
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%" class="text-muted">Product Code:</th>
                                    <td><strong class="text-primary">{{ $sale->inventory->product_code }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Category:</th>
                                    <td><span class="badge bg-secondary">{{ $sale->inventory->category }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="40%" class="text-muted">Current Stock:</th>
                                    <td>
                                        <strong>{{ $sale->inventory->stock_quantity }}</strong>
                                        @if($sale->inventory->stock_quantity < 10)
                                            <span class="badge bg-warning text-dark ms-2">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Supplier:</th>
                                    <td>{{ $sale->inventory->supplier ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection