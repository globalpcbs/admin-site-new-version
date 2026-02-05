<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\porder_tb;
use App\Models\data_tb;
use App\Models\credit_tb;

class OrderController extends Controller
{
    public function getCustomer($name)
    {
        $customer = data_tb::where('c_name', $name)->first();
        return response()->json($customer);
    }
    
    public function getCredit($invoiceId)
    {
        $credit = credit_tb::where('inv_id', $invoiceId)->first();
        return response()->json($credit);
    }
    
    public function quickUpdate(Request $request, $id)
    {
        $request->validate([
            'field' => 'required|in:cus_due,supli_due',
            'value' => 'nullable|date'
        ]);
        
        $order = porder_tb::findOrFail($id);
        $order->{$request->field} = $request->value;
        $order->save();
        
        return response()->json(['success' => true]);
    }
    
    public function toggleWT(Request $request, $id)
    {
        $order = porder_tb::findOrFail($id);
        $order->allow = $request->allow ? 'true' : 'false';
        $order->save();
        
        return response()->json(['success' => true]);
    }
}