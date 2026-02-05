<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\data_tb;
use App\Models\porder_tb;
use Illuminate\Support\Facades\DB;

class StatusReportApiController extends Controller
{
    public function getCustomer($name)
    {
        try {
            $customer = data_tb::where('c_name', $name)->first();
            
            if (!$customer) {
                return response()->json([
                    'error' => 'Customer not found'
                ], 404);
            }
            
            return response()->json([
                'c_name' => $customer->c_name,
                'c_shortname' => $customer->c_shortname,
                'c_address' => $customer->c_address,
                'c_address2' => $customer->c_address2,
                'c_address3' => $customer->c_address3,
                'c_phone' => $customer->c_phone,
                'c_fax' => $customer->c_fax,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch customer details',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function quickUpdate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:porder_tb,poid',
            'field' => 'required|in:cus_due,supli_due',
            'value' => 'nullable|date'
        ]);
        
        try {
            $order = porder_tb::findOrFail($request->order_id);
            $order->{$request->field} = $request->value;
            $order->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Due date updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update due date: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function toggleWT(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:porder_tb,poid',
            'allow' => 'required|boolean'
        ]);
        
        try {
            $order = porder_tb::findOrFail($request->order_id);
            $order->allow = $request->allow ? 'true' : 'false';
            $order->save();
            
            return response()->json([
                'success' => true,
                'message' => 'WT status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update WT status: ' . $e->getMessage()
            ], 500);
        }
    }
}