<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\data_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\vendor_tb as Vendor;
use Illuminate\Support\Facades\DB;
use App\Models\order_tb as Order;
use App\Models\data_tb as customer;
use Carbon\Carbon;

class Vieworder extends Component
{
    use WithPagination;

    public $purchase;
    public $items;
    public $vendor;
    public $shipper;
    public $customer;
    public $totalAmount = 0;
    
    // Add formatted date properties
    public $formattedPodate;
    public $formattedDate1;
    public $formattedDate2;
    public $formattedOrdon;

    public function mount($id)
    {
        $this->purchase = porder_tb::with(['vendor', 'shipper', 'items'])
            ->findOrFail($id);
            
        $this->items = items_tb::where('pid', $id)->get();
        $this->vendor = vendor_tb::find($this->purchase->vid);
        $this->shipper = \App\Models\shipper_tb::find($this->purchase->sid);
        $this->customer = data_tb::where('c_name', $this->purchase->customer)->first();
        
        // Calculate total amount
        $this->totalAmount = $this->items->sum('tprice');
        
        // Format dates safely - use the simple method
        $this->formattedPodate = $this->formatCustomDate($this->purchase->podate);
        $this->formattedDate1 = $this->formatCustomDate($this->purchase->date1);
        $this->formattedDate2 = $this->formatCustomDate($this->purchase->date2);
        $this->formattedOrdon = $this->formatCustomDate($this->purchase->ordon);
    }

    /**
     * Simple and safe date formatting without Carbon::parse
     */
    private function formatCustomDate($dateString, $format = 'M d, Y')
    {
        if (empty($dateString) || $dateString == '0000-00-00' || $dateString == '00-00-0000') {
            return 'N/A';
        }

        // For "Saturday-04-04-2020" format specifically
        if (str_contains($dateString, '-')) {
            $parts = explode('-', $dateString);
            
            // If we have 4 parts like "Saturday-04-04-2020"
            if (count($parts) === 4) {
                $day = $parts[1];   // 04
                $month = $parts[2]; // 04
                $year = $parts[3];  // 2020
                
                // Validate the parts are numeric
                if (is_numeric($day) && is_numeric($month) && is_numeric($year)) {
                    try {
                        // Use createSafe to avoid exceptions
                        return Carbon::createSafe($year, $month, $day)->format($format);
                    } catch (\Exception $e) {
                        return $dateString;
                    }
                }
            }
            
            // If we have 3 parts like "04-04-2020"
            if (count($parts) === 3) {
                $first = $parts[0];
                $second = $parts[1];
                $year = $parts[2];
                
                if (is_numeric($first) && is_numeric($second) && is_numeric($year)) {
                    try {
                        // Try both day-month-year and month-day-year
                        if ($first <= 12) {
                            // Assume month-day-year
                            return Carbon::createSafe($year, $first, $second)->format($format);
                        } else {
                            // Assume day-month-year
                            return Carbon::createSafe($year, $second, $first)->format($format);
                        }
                    } catch (\Exception $e) {
                        return $dateString;
                    }
                }
            }
        }

        // For any other format, return as is to avoid Carbon::parse errors
        return $dateString;
    }

    /**
     * Alternative: Manual date formatting without Carbon
     */
    private function manualDateFormat($dateString)
    {
        if (empty($dateString)) {
            return 'N/A';
        }

        // Handle "Saturday-04-04-2020" format
        if (preg_match('/\b(\d{1,2})-(\d{1,2})-(\d{4})\b/', $dateString, $matches)) {
            $month = (int)$matches[1];
            $day = (int)$matches[2];
            $year = (int)$matches[3];
            
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];
            
            if (isset($months[$month])) {
                return $months[$month] . ' ' . $day . ', ' . $year;
            }
        }
        
        return $dateString;
    }

    public function printOrder()
    {
        $this->dispatch('print-order');
    }

    public function render()
    {
        return view('livewire.purchase-order.vieworder', [
            'purchase' => $this->purchase,
            'items' => $this->items,
            'vendor' => $this->vendor,
            'shipper' => $this->shipper,
            'customer' => $this->customer,
            'totalAmount' => $this->totalAmount,
            'formattedPodate' => $this->formattedPodate,
            'formattedDate1' => $this->formattedDate1,
            'formattedDate2' => $this->formattedDate2,
            'formattedOrdon' => $this->formattedOrdon,
        ])->layout('layouts.app');
    }
}