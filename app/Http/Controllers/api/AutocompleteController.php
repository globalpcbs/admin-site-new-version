<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutocompleteController extends Controller
{
    public function partNumbers(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $results = DB::table('porder_tb')
            ->select('part_no')
            ->where('part_no', 'like', $query . '%')
            ->distinct()
            ->orderBy('part_no')
            ->limit(10)
            ->get()
            ->pluck('part_no');
            
        return response()->json($results);
    }
    
    public function customers(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $results = DB::table('porder_tb')
            ->select('customer')
            ->where('customer', 'like', '%' . $query . '%')
            ->distinct()
            ->orderBy('customer')
            ->limit(10)
            ->get()
            ->pluck('customer');
            
        return response()->json($results);
    }
    
    public function vendors(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $results = DB::table('vendor_tb')
            ->select('c_shortname')
            ->where('c_shortname', 'like', '%' . $query . '%')
            ->distinct()
            ->orderBy('c_shortname')
            ->limit(10)
            ->get()
            ->pluck('c_shortname');
            
        return response()->json($results);
    }
}