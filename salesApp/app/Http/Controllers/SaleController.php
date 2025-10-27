<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SaleController extends Controller
{
    // Show all sales
    public function index()
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            $sales = Sale::with('inventory')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $sales = Sale::orderBy('created_at', 'desc')->paginate(10);
        }
        
        $totalSales = Sale::count();
        $totalRevenue = Sale::where('status', 'completed')->sum('total_amount');
        $pendingSales = Sale::where('status', 'pending')->count();
        
        return view('sales.index', compact('sales', 'totalSales', 'totalRevenue', 'pendingSales'));
    }

    // Show form to create a sale
    public function create()
    {
        $inventories = Inventory::where('stock_quantity', '>', 0)->orderBy('product_name')->get();
        return view('sales.create', compact('inventories'));
    }

    // Save new sale
    public function store(Request $request)
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        $rules = [
            'transaction_id' => 'required|unique:sales,transaction_id',
            'customer_name' => 'required|max:150',
            'quantity' => 'required|integer|min:1',
            'sale_date' => 'required|date',
            'sales_person' => 'required|max:100',
            'status' => 'required|in:pending,completed,cancelled'
        ];
        
        if ($hasInventoryIdColumn) {
            $rules['inventory_id'] = 'required|exists:inventory,id';
        } else {
            $rules['product_name'] = 'required|max:150';
            $rules['unit_price'] = 'required|numeric|min:0';
        }
        
        $validated = $request->validate($rules);

        if ($hasInventoryIdColumn) {
            // Get inventory item
            $inventory = Inventory::findOrFail($validated['inventory_id']);
            
            // Check stock availability
            if (!$inventory->isInStock($validated['quantity'])) {
                return back()->withErrors(['quantity' => 'Insufficient stock. Available: ' . $inventory->stock_quantity])
                            ->withInput();
            }

            // Add product details from inventory
            $validated['product_name'] = $inventory->product_name;
            $validated['unit_price'] = $inventory->unit_price;

            // Create sale
            $sale = Sale::create($validated);

            // Reduce inventory stock (only if status is completed)
            if ($validated['status'] === 'completed') {
                $inventory->reduceStock($validated['quantity']);
            }
        } else {
            // Create sale without inventory relationship
            $sale = Sale::create($validated);
        }

        return redirect()->route('sales.index')->with('success', 'Sale created successfully!');
    }

    // Show single sale
    public function show(Sale $sale)
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            $sale->load('inventory');
        }
        
        return view('sales.show', compact('sale'));
    }

    // Show form to edit sale
    public function edit(Sale $sale)
    {
        $inventories = Inventory::orderBy('product_name')->get();
        
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        if ($hasInventoryIdColumn) {
            $sale->load('inventory');
        }
        
        return view('sales.edit', compact('sale', 'inventories'));
    }

    // Update sale
    public function update(Request $request, Sale $sale)
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        $rules = [
            'transaction_id' => 'required|unique:sales,transaction_id,' . $sale->id,
            'customer_name' => 'required|max:150',
            'quantity' => 'required|integer|min:1',
            'sale_date' => 'required|date',
            'sales_person' => 'required|max:100',
            'status' => 'required|in:pending,completed,cancelled'
        ];
        
        if ($hasInventoryIdColumn) {
            $rules['inventory_id'] = 'required|exists:inventory,id';
        } else {
            $rules['product_name'] = 'required|max:150';
            $rules['unit_price'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if ($hasInventoryIdColumn) {
            // Get inventory item
            $inventory = Inventory::findOrFail($validated['inventory_id']);
            
            // Handle stock changes
            $oldQuantity = $sale->quantity;
            $newQuantity = $validated['quantity'];
            $oldStatus = $sale->status;
            $newStatus = $validated['status'];

            // If inventory item changed or quantity changed, handle stock accordingly
            if ($sale->inventory_id !== $validated['inventory_id'] || $oldQuantity !== $newQuantity || $oldStatus !== $newStatus) {
                
                // Return old stock if sale was completed
                if ($oldStatus === 'completed' && $sale->inventory) {
                    $sale->inventory->increment('stock_quantity', $oldQuantity);
                }
                
                // Deduct new stock if new status is completed
                if ($newStatus === 'completed') {
                    if (!$inventory->isInStock($newQuantity)) {
                        return back()->withErrors(['quantity' => 'Insufficient stock. Available: ' . $inventory->stock_quantity])
                                    ->withInput();
                    }
                    $inventory->reduceStock($newQuantity);
                }
            }

            // Add product details from inventory
            $validated['product_name'] = $inventory->product_name;
            $validated['unit_price'] = $inventory->unit_price;
        }

        $sale->update($validated);
        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    // Delete sale
    public function destroy(Sale $sale)
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        // Return stock if sale was completed and has inventory relationship
        if ($hasInventoryIdColumn && $sale->status === 'completed' && $sale->inventory) {
            $sale->inventory->increment('stock_quantity', $sale->quantity);
        }
        
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully and stock returned!');
    }

    // Delete all sales
    public function destroyAll()
    {
        try {
            DB::beginTransaction();
            
            $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
            
            if ($hasInventoryIdColumn) {
                // Return stock for all completed sales
                $completedSales = Sale::where('status', 'completed')->with('inventory')->get();
                foreach ($completedSales as $sale) {
                    if ($sale->inventory) {
                        $sale->inventory->increment('stock_quantity', $sale->quantity);
                    }
                }
            }
            
            // Delete all sales
            $deletedCount = Sale::count();
            Sale::truncate();
            
            DB::commit();
            
            return redirect()->route('sales.index')->with('success', "Successfully deleted all {$deletedCount} sales and returned stock to inventory!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales.index')->with('error', 'Failed to delete all sales. Please try again.');
        }
    }

    // Transaction History
    public function history()
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            $sales = Sale::with('inventory')->orderBy('created_at', 'desc')->paginate(20);
        } else {
            $sales = Sale::orderBy('created_at', 'desc')->paginate(20);
        }
        
        $stats = [
            'total_transactions' => Sale::count(),
            'completed_sales' => Sale::where('status', 'completed')->count(),
            'pending_sales' => Sale::where('status', 'pending')->count(),
            'cancelled_sales' => Sale::where('status', 'cancelled')->count(),
            'total_revenue' => Sale::where('status', 'completed')->sum('total_amount'),
            'average_sale' => Sale::where('status', 'completed')->avg('total_amount'),
        ];
        
        return view('sales.history', compact('sales', 'stats'));
    }

    // Get inventory details via AJAX
    public function getInventoryDetails($id)
    {
        $inventory = Inventory::find($id);
        if ($inventory) {
            return response()->json([
                'product_name' => $inventory->product_name,
                'unit_price' => $inventory->unit_price,
                'stock_quantity' => $inventory->stock_quantity,
                'category' => $inventory->category
            ]);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }

    // Export to CSV
    public function export()
    {
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            $sales = Sale::with('inventory')->get();
        } else {
            $sales = Sale::all();
        }
        
        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Transaction ID', 'Customer Name', 'Product Name', 'Unit Price', 'Quantity', 'Total Amount', 'Sale Date', 'Sales Person', 'Status']);
        
        foreach ($sales as $sale) {
            $csv->insertOne([
                $sale->id,
                $sale->transaction_id,
                $sale->customer_name,
                $sale->product_name,
                $sale->unit_price,
                $sale->quantity,
                $sale->total_amount,
                $sale->sale_date->format('Y-m-d'),
                $sale->sales_person,
                $sale->status
            ]);
        }

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales_export.csv"',
        ]);
    }

    // Import from CSV
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $imported = 0;

        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');

        foreach ($records as $record) {
            try {
                $saleData = [
                    'transaction_id' => $record['Transaction ID'],
                    'customer_name' => $record['Customer Name'],
                    'product_name' => $record['Product Name'],
                    'unit_price' => $record['Unit Price'],
                    'quantity' => $record['Quantity'],
                    'sale_date' => $record['Sale Date'],
                    'sales_person' => $record['Sales Person'],
                    'status' => $record['Status'] ?? 'completed'
                ];

                if ($hasInventoryIdColumn) {
                    // Try to find inventory by product name
                    $inventory = Inventory::where('product_name', $record['Product Name'])->first();
                    $saleData['inventory_id'] = $inventory ? $inventory->id : null;
                }
                
                Sale::create($saleData);
                $imported++;
            } catch (\Exception $e) {
                continue; // Skip invalid rows
            }
        }

        return redirect()->route('sales.index')->with('success', "Successfully imported {$imported} sales records!");
    }
}