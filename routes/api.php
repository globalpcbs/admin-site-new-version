<?php

use App\Models\order_tb as Order;
use App\Models\porder_tb;
use App\Models\corder_tb;
use App\Models\vendor_tb;
use App\Models\packing_tb;
use App\Models\invoice_tb;
use App\Models\credit_tb;
use App\Models\stock_tb;
use App\Models\data_tb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Existing APIs for quotes (keep these)
Route::get('/partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = Order::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$query}%")
        ->distinct()
        ->orderBy('part_no', 'asc')
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = Order::query()
        ->select('cust_name')
        ->where('cust_name', 'like', "%{$query}%")
        ->distinct()
        ->orderBy('cust_name', 'asc')
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});
// These routes will be available at /api/purchase/...
Route::get('/purchase/partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = porder_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$query}%")
        ->distinct()
        ->orderBy('part_no', 'asc')
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/purchase/customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = porder_tb::query()
        ->select('customer')
        ->where('customer', 'like', "%{$query}%")
        ->distinct()
        ->orderBy('customer', 'asc')
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/purchase/vendor-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = vendor_tb::query()
        ->select('c_name', 'c_shortname', 'data_id')
        ->where('c_name', 'like', "%{$query}%")
        ->orWhere('c_shortname', 'like', "%{$query}%")
        ->orderBy('c_shortname', 'asc')
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});
// Add these routes for confirmation orders autocomplete
Route::get('/confirmation-partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = corder_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/confirmation-customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = corder_tb::query()
        ->select('customer')
        ->where('customer', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});
// Add these routes for packing slips autocomplete
Route::get('/packing-partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = packing_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/packing-customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = packing_tb::query()
        ->select('customer')
        ->where('customer', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});
// Add these routes for invoice autocomplete
Route::get('/invoice-partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = invoice_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/invoice-customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = invoice_tb::query()
        ->select('customer')
        ->where('customer', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});
// Add these routes for credit autocomplete
Route::get('/credit-partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = credit_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/credit-customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = credit_tb::query()
        ->select('customer')
        ->where('customer', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

// Add these routes for stock autocomplete
Route::get('/stock-partno-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $suggestions = stock_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});

Route::get('/stock-customer-suggestions', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    // Since stock_tb uses customer_id, we need to get from data_tb
    $suggestions = data_tb::query()
        ->select('c_name')
        ->where('c_name', 'like', '%' . $query . '%')
        ->distinct()
        ->limit(10)
        ->get()
        ->toArray();
    
    return response()->json($suggestions);
});