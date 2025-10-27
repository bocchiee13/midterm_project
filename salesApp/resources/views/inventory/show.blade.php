@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cube me-2 text-primary"></i>Product Details - {{ $inventory->product_code }}
                </h5>
                <div class="btn-group">
                    <a href="{{ route('inventory.edit', $inventory) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 mb-3 h-100">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">
                                <i class="fas fa-info-circle me-2"></i>Product Information
                            </h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="45%" class="text-muted">
                                        <i class="fas fa-barcode me-2"></i>Product Code:
                                    </th>
                                    <td><strong class="text-primary">{{ $inventory->product_code }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-tag me-2"></i>Product Name:
                                    </th>
                                    <td><strong>{{ $inventory->product_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-folder me-2"></i>Category:
                                    </th>
                                    <td>
                                        <span class="badge bg-primary">{{ $inventory->category }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-dollar-sign me-2"></i>Unit Price:
                                    </th>
                                    <td><strong class="text-success">${{ number_format($inventory->unit_price, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded p-4 mb-3 h-100">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">
                                <i class="fas fa-warehouse me-2"></i>Stock & Supply Information
                            </h6>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th width="45%" class="text-muted">
                                        <i class="fas fa-boxes me-2"></i>Stock Quantity:
                                    </th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <strong class="me-2">{{ $inventory->stock_quantity }}</strong>
                                            @if($inventory->stock_quantity < 10 && $inventory->stock_quantity > 0)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                                </span>
                                            @elseif($inventory->stock_quantity == 0)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Out of Stock
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>In Stock
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-truck me-2"></i>Supplier:
                                    </th>
                                    <td>{{ $inventory->supplier ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-calendar-alt me-2"></i>Last Restocked:
                                    </th>
                                    <td>
                                        @if($inventory->last_restocked)
                                            <span class="text-success">{{ $inventory->last_restocked->format('F d, Y') }}</span>
                                        @else
                                            <span class="text-muted">Never restocked</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">
                                        <i class="fas fa-plus me-2"></i>Created:
                                    </th>
                                    <td>{{ $inventory->created_at->format('F d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                @if($inventory->description)
                <div class="bg-light rounded p-4 mt-3">
                    <h6 class="text-muted text-uppercase fw-semibold mb-3">
                        <i class="fas fa-align-left me-2"></i>Description
                    </h6>
                    <p class="mb-0 text-dark">{{ $inventory->description }}</p>
                </div>
                @endif

                <!-- Stock Value Card -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card border-0 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white text-center">
                                <i class="fas fa-calculator fa-2x mb-2"></i>
                                <h6 class="text-uppercase fw-semibold">Total Stock Value</h6>
                                <h3 class="fw-bold mb-0">${{ number_format($inventory->unit_price * $inventory->stock_quantity, 2) }}</h3>
                                <small class="opacity-75">{{ $inventory->stock_quantity }} units × ${{ number_format($inventory->unit_price, 2) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection