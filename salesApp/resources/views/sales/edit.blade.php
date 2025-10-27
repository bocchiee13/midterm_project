@extends('layouts.app')

@section('title', 'Edit Sale')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2 text-primary"></i>Edit Sale - {{ $sale->transaction_id }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sales.update', $sale) }}" method="POST" id="saleForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="transaction_id" class="form-label fw-semibold">
                                <i class="fas fa-receipt me-2"></i>Transaction ID
                            </label>
                            <input type="text" class="form-control @error('transaction_id') is-invalid @enderror" 
                                   name="transaction_id" value="{{ old('transaction_id', $sale->transaction_id) }}" required
                                   placeholder="Enter unique transaction ID">
                            @error('transaction_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label fw-semibold">
                                <i class="fas fa-user me-2"></i>Customer Name
                            </label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}" required
                                   placeholder="Enter customer name">
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="inventory_id" class="form-label fw-semibold">
                                <i class="fas fa-box me-2"></i>Select Product
                            </label>
                            <select class="form-select @error('inventory_id') is-invalid @enderror" 
                                    name="inventory_id" id="inventory_id" required>
                                <option value="">Choose a product...</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}" 
                                            data-price="{{ $inventory->unit_price }}"
                                            data-stock="{{ $inventory->stock_quantity }}"
                                            data-name="{{ $inventory->product_name }}"
                                            {{ old('inventory_id', $sale->inventory_id) == $inventory->id ? 'selected' : '' }}>
                                        {{ $inventory->product_name }} - ${{ number_format($inventory->unit_price, 2) }} 
                                        (Stock: {{ $inventory->stock_quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('inventory_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="unit_price" class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-2"></i>Unit Price
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" 
                                       id="unit_price" name="unit_price" value="{{ old('unit_price', $sale->unit_price) }}" readonly>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Price will be filled automatically when you select a product
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label fw-semibold">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantity
                            </label>
                            <input type="number" min="1" class="form-control @error('quantity') is-invalid @enderror" 
                                   name="quantity" id="quantity" value="{{ old('quantity', $sale->quantity) }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="stock-info"></small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="total_amount" class="form-label fw-semibold">
                                <i class="fas fa-calculator me-2"></i>Total Amount
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control bg-light" 
                                       id="total_amount" value="{{ old('total_amount', $sale->total_amount) }}" readonly>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Calculated automatically
                            </small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="sale_date" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt me-2"></i>Sale Date
                            </label>
                            <input type="date" class="form-control @error('sale_date') is-invalid @enderror" 
                                   name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sales_person" class="form-label fw-semibold">
                                <i class="fas fa-user-tie me-2"></i>Sales Person
                            </label>
                            <input type="text" class="form-control @error('sales_person') is-invalid @enderror" 
                                   name="sales_person" value="{{ old('sales_person', $sale->sales_person) }}" required
                                   placeholder="Enter sales person name">
                            @error('sales_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">
                                <i class="fas fa-info-circle me-2"></i>Status
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending" {{ old('status', $sale->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $sale->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $sale->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Total Summary Card -->
                    <div class="card border-0 bg-gradient mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white text-center">
                            <i class="fas fa-calculator fa-2x mb-2"></i>
                            <h6 class="text-uppercase fw-semibold">Sale Total</h6>
                            <h3 class="fw-bold mb-0" id="display-total">${{ number_format($sale->total_amount, 2) }}</h3>
                            <small class="opacity-75" id="calculation-display">{{ $sale->quantity }} units × ${{ number_format($sale->unit_price, 2) }}</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Sales
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventorySelect = document.getElementById('inventory_id');
    const unitPriceInput = document.getElementById('unit_price');
    const quantityInput = document.getElementById('quantity');
    const totalAmountInput = document.getElementById('total_amount');
    const stockInfo = document.getElementById('stock-info');
    const displayTotal = document.getElementById('display-total');
    const calculationDisplay = document.getElementById('calculation-display');

    function updateProductDetails() {
        const selectedOption = inventorySelect.options[inventorySelect.selectedIndex];
        
        if (selectedOption.value) {
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const stock = parseInt(selectedOption.getAttribute('data-stock'));
            
            unitPriceInput.value = price.toFixed(2);
            stockInfo.innerHTML = `<i class="fas fa-warehouse me-1"></i>Available stock: <strong>${stock}</strong>`;
            quantityInput.max = stock;
            
            if (stock === 0) {
                stockInfo.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-1"></i><span class="text-danger">Out of stock!</span>';
                quantityInput.disabled = true;
            } else {
                quantityInput.disabled = false;
            }
            
            calculateTotal();
        } else {
            unitPriceInput.value = '';
            stockInfo.innerHTML = '';
            totalAmountInput.value = '';
            displayTotal.textContent = '$0.00';
            calculationDisplay.textContent = '0 units × $0.00';
            quantityInput.max = '';
            quantityInput.disabled = false;
        }
    }

    function calculateTotal() {
        const price = parseFloat(unitPriceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = price * quantity;
        
        totalAmountInput.value = total.toFixed(2);
        displayTotal.textContent = '$' + total.toFixed(2);
        calculationDisplay.textContent = `${quantity} units × $${price.toFixed(2)}`;
    }

    inventorySelect.addEventListener('change', updateProductDetails);
    quantityInput.addEventListener('input', calculateTotal);

    // Initialize if there's a selection
    if (inventorySelect.value) {
        updateProductDetails();
    }
});
</script>
@endsection