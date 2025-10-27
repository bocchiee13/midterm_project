@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Product - {{ $inventory->product_code }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('inventory.update', $inventory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_code" class="form-label fw-semibold">
                                <i class="fas fa-barcode me-2"></i>Product Code
                            </label>
                            <input type="text" class="form-control @error('product_code') is-invalid @enderror" 
                                   name="product_code" value="{{ old('product_code', $inventory->product_code) }}" required 
                                   placeholder="Enter unique product code">
                            @error('product_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label fw-semibold">
                                <i class="fas fa-tag me-2"></i>Product Name
                            </label>
                            <input type="text" class="form-control @error('product_name') is-invalid @enderror" 
                                   name="product_name" value="{{ old('product_name', $inventory->product_name) }}" required
                                   placeholder="Enter product name">
                            @error('product_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            <i class="fas fa-align-left me-2"></i>Description
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" rows="3" 
                                  placeholder="Enter product description...">{{ old('description', $inventory->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="unit_price" class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-2"></i>Unit Price
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control @error('unit_price') is-invalid @enderror" 
                                       name="unit_price" value="{{ old('unit_price', $inventory->unit_price) }}" required
                                       placeholder="0.00">
                            </div>
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="stock_quantity" class="form-label fw-semibold">
                                <i class="fas fa-warehouse me-2"></i>Stock Quantity
                            </label>
                            <input type="number" min="0" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                   name="stock_quantity" value="{{ old('stock_quantity', $inventory->stock_quantity) }}" required
                                   placeholder="0">
                            @error('stock_quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label fw-semibold">
                                <i class="fas fa-folder me-2"></i>Category
                            </label>
                            <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                   name="category" value="{{ old('category', $inventory->category) }}" required
                                   placeholder="Enter category">
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier" class="form-label fw-semibold">
                                <i class="fas fa-truck me-2"></i>Supplier
                            </label>
                            <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                                   name="supplier" value="{{ old('supplier', $inventory->supplier) }}"
                                   placeholder="Enter supplier name (optional)">
                            @error('supplier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_restocked" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt me-2"></i>Last Restocked
                            </label>
                            <input type="date" class="form-control @error('last_restocked') is-invalid @enderror" 
                                   name="last_restocked" value="{{ old('last_restocked', $inventory->last_restocked?->format('Y-m-d')) }}">
                            @error('last_restocked')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Inventory
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection