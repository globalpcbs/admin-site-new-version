<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PorderTb;
use App\Models\DataTb;
use App\Models\CreditTb;

class StatusReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('porder_tb as p')
                ->select(
                    'p.*',
                    'i.invoice_id',
                    'i.podate as invoicedon',
                    'v.c_shortname as vc',
                    DB::raw("UNIX_TIMESTAMP(STR_TO_DATE(p.dweek,'%m-%d-%Y')) as dw")
                )
                ->leftJoin('invoice_tb as i', function($join) {
                    $join->on('p.part_no', '=', 'i.part_no')
                         ->on('p.rev', '=', 'i.rev')
                         ->on('p.po', '=', 'i.po');
                })
                ->leftJoin('vendor_tb as v', 'v.data_id', '=', 'p.vid');

            // Apply filters
            if ($request->filled('from') && $request->filled('to')) {
                $query->whereBetween(DB::raw("STR_TO_DATE(p.dweek, '%m-%d-%Y')"), [
                    $request->from, $request->to
                ]);
            }

            if ($request->filled('part_number')) {
                $query->where('p.part_no', 'like', '%' . $request->part_number . '%');
            }

            if ($request->filled('customer_name')) {
                $query->where('p.customer', 'like', '%' . $request->customer_name . '%');
            }

            if ($request->filled('vendor_name')) {
                $query->where('v.c_shortname', 'like', '%' . $request->vendor_name . '%');
            }

            $orders = $query->orderBy('p.poid', 'asc')->limit(200)->get();

            // Load related data
            $orders->each(function ($order) {
                $order->customer_details = DataTb::where('c_name', $order->customer)->first();
                if ($order->invoice_id) {
                    $order->credit_details = CreditTb::where('inv_id', $order->invoice_id + 9976)->first();
                }
            });

            return response()->json([
                'success' => true,
                'orders' => $orders,
                'total' => $orders->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateDueDates(Request $request, $id)
    {
        $request->validate([
            'cus_due' => 'nullable|date',
            'sup_due' => 'nullable|date'
        ]);

        try {
            $order = PorderTb::findOrFail($id);
            $order->cus_due = $request->cus_due;
            $order->supli_due = $request->sup_due;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Due dates updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating due dates: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:1000'
        ]);

        try {
            $order = PorderTb::findOrFail($id);
            $order->note = $request->note;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating note: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleAllow(Request $request, $id)
    {
        try {
            $order = PorderTb::findOrFail($id);
            $order->allow = $request->allow ? 'true' : 'false';
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'WT Status updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}