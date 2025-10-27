<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sale;
use Illuminate\Http\Request;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::orderBy('created_at', 'desc')->paginate(10);
        $totalProducts = Inventory::count();
        $totalValue = Inventory::sum(DB::raw('unit_price * stock_quantity'));
        $lowStockItems = Inventory::where('stock_quantity', '<', 10)->count();
        $outOfStockItems = Inventory::where('stock_quantity', 0)->count();
        
        return view('inventory.index', compact('inventory', 'totalProducts', 'totalValue', 'lowStockItems', 'outOfStockItems'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|unique:inventory,product_code',
            'product_name' => 'required|max:150',
            'description' => 'nullable',
            'unit_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|max:100',
            'supplier' => 'nullable|max:150',
            'last_restocked' => 'nullable|date'
        ]);

        Inventory::create($validated);
        return redirect()->route('inventory.index')->with('success', 'Product added successfully!');
    }

    public function show(Inventory $inventory)
    {
        // Check if inventory_id column exists in sales table
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            // Get sales history for this product
            $salesHistory = Sale::where('inventory_id', $inventory->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'sales_page');
                
            $totalSold = Sale::where('inventory_id', $inventory->id)
                ->where('status', 'completed')
                ->sum('quantity');
                
            $revenue = Sale::where('inventory_id', $inventory->id)
                ->where('status', 'completed')
                ->sum('total_amount');
        } else {
            // Fallback: match by product name
            $salesHistory = Sale::where('product_name', $inventory->product_name)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'sales_page');
                
            $totalSold = Sale::where('product_name', $inventory->product_name)
                ->where('status', 'completed')
                ->sum('quantity');
                
            $revenue = Sale::where('product_name', $inventory->product_name)
                ->where('status', 'completed')
                ->sum('total_amount');
        }
        
        return view('inventory.show', compact('inventory', 'salesHistory', 'totalSold', 'revenue'));
    }

    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_code' => 'required|unique:inventory,product_code,' . $inventory->id,
            'product_name' => 'required|max:150',
            'description' => 'nullable',
            'unit_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'category' => 'required|max:100',
            'supplier' => 'nullable|max:150',
            'last_restocked' => 'nullable|date'
        ]);

        $inventory->update($validated);
        return redirect()->route('inventory.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Inventory $inventory)
    {
        // Check if inventory_id column exists
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            $salesCount = Sale::where('inventory_id', $inventory->id)->count();
        } else {
            $salesCount = Sale::where('product_name', $inventory->product_name)->count();
        }
        
        if ($salesCount > 0) {
            return redirect()->route('inventory.index')
                ->with('error', "Cannot delete product. It has {$salesCount} associated sales records. Please delete those sales first.");
        }
        
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Product deleted successfully!');
    }

    public function destroyAll()
    {
        try {
            DB::beginTransaction();
            
            // Check if there are any sales linked to inventory
            $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
            
            if ($hasInventoryIdColumn) {
                $salesCount = Sale::whereNotNull('inventory_id')->count();
            } else {
                $salesCount = Sale::count(); // If no inventory_id column, check all sales
            }
            
            if ($salesCount > 0) {
                return redirect()->route('inventory.index')
                    ->with('error', "Cannot delete all products. There are {$salesCount} sales records that may be linked to inventory items. Please review and delete sales first if needed.");
            }
            
            // Delete all inventory
            $deletedCount = Inventory::count();
            Inventory::truncate();
            
            DB::commit();
            
            return redirect()->route('inventory.index')->with('success', "Successfully deleted all {$deletedCount} inventory items!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('inventory.index')->with('error', 'Failed to delete all inventory items. Please try again.');
        }
    }

    public function history()
    {
        // Check if inventory_id column exists in sales table
        $hasInventoryIdColumn = Schema::hasColumn('sales', 'inventory_id');
        
        if ($hasInventoryIdColumn) {
            // Get inventory with sales data using inventory_id relationship
            $inventory = Inventory::withCount(['sales as total_sales'])
                ->withSum(['sales as total_revenue' => function($query) {
                    $query->where('status', 'completed');
                }], 'total_amount')
                ->withSum(['sales as total_quantity_sold' => function($query) {
                    $query->where('status', 'completed');
                }], 'quantity')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Fallback: Get inventory without sales relationships
            $inventory = Inventory::select(['*'])
                ->selectRaw('(SELECT COUNT(*) FROM sales WHERE sales.product_name = inventory.product_name) as total_sales')
                ->selectRaw('(SELECT SUM(total_amount) FROM sales WHERE sales.product_name = inventory.product_name AND status = "completed") as total_revenue')
                ->selectRaw('(SELECT SUM(quantity) FROM sales WHERE sales.product_name = inventory.product_name AND status = "completed") as total_quantity_sold')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        // Overall stats
        $stats = [
            'total_products' => Inventory::count(),
            'total_value' => Inventory::sum(DB::raw('unit_price * stock_quantity')),
            'low_stock_items' => Inventory::where('stock_quantity', '<', 10)->count(),
            'out_of_stock' => Inventory::where('stock_quantity', 0)->count(),
            'total_sales_transactions' => Sale::count(),
            'total_revenue' => Sale::where('status', 'completed')->sum('total_amount'),
        ];

        return view('inventory.history', compact('inventory', 'stats'));
    }

    public function export()
    {
        $inventory = Inventory::all();
        
        $csv = Writer::createFromString('');
        $csv->insertOne(['ID', 'Product Code', 'Product Name', 'Description', 'Unit Price', 'Stock Quantity', 'Category', 'Supplier', 'Last Restocked']);
        
        foreach ($inventory as $item) {
            $csv->insertOne([
                $item->id,
                $item->product_code,
                $item->product_name,
                $item->description,
                $item->unit_price,
                $item->stock_quantity,
                $item->category,
                $item->supplier,
                $item->last_restocked?->format('Y-m-d') ?? ''
            ]);
        }

        return response($csv->toString(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_export.csv"',
        ]);
    }

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

        foreach ($records as $record) {
            try {
                Inventory::create([
                    'product_code' => $record['Product Code'],
                    'product_name' => $record['Product Name'],
                    'description' => $record['Description'] ?? null,
                    'unit_price' => $record['Unit Price'],
                    'stock_quantity' => $record['Stock Quantity'],
                    'category' => $record['Category'],
                    'supplier' => $record['Supplier'] ?? null,
                    'last_restocked' => !empty($record['Last Restocked']) ? $record['Last Restocked'] : null
                ]);
                $imported++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return redirect()->route('inventory.index')->with('success', "Successfully imported {$imported} inventory items!");
    }
}