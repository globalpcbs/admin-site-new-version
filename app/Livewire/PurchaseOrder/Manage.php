<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\vendor_tb;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Manage extends Component
{
    use WithPagination;

    public $searchPart = '';
    public $searchCustomer = '';
    public $searchVendor = '';
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchVendorInput = '';

    public $matches = [];
    public $matches_partno = [];
    public $matches_vendor = [];
    
    protected $paginationTheme = 'bootstrap';
    
    public $alertMessage = '';
    public $alertType = '';
    public $isLoading = false;

    // Cache search results in memory
    protected $searchCache = [];
    protected $suggestionCache = [];

    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function updating($field)
    {
        if (in_array($field, ['searchPart', 'searchCustomer', 'searchVendor'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'searchPart', 'searchCustomer', 'searchVendor', 
            'searchPartNoInput', 'searchCustomerInput', 'searchVendorInput'
        ]);
        $this->matches = [];
        $this->matches_partno = [];
        $this->matches_vendor = [];
        $this->resetPage();
        
        // Clear memory caches
        $this->searchCache = [];
        $this->suggestionCache = [];
    }

    public function delete($id)
    {
        if (!confirm('Are you sure to delete?')) return;

        $this->isLoading = true;
        
        try {
            DB::transaction(function () use ($id) {
                items_tb::where('pid', $id)->delete();
                porder_tb::destroy($id);
            });

            // Clear memory caches
            $this->searchCache = [];
            $this->suggestionCache = [];
            
            $this->alertMessage = 'Purchase order deleted successfully.';
            $this->alertType = 'warning';
            
        } catch (\Exception $e) {
            $this->alertMessage = 'Error deleting purchase order.';
            $this->alertType = 'danger';
        } finally {
            $this->isLoading = false;
        }
    }

    public function duplicate($id)
    {
        $this->isLoading = true;
        
        DB::beginTransaction();
        try {
            $original = porder_tb::select([
                'poid', 'customer', 'part_no', 'rev', 'vendor_id', 
                'podate', 'note', 'supli_due', 'cus_due'
            ])->findOrFail($id);
            
            $newPo = $original->replicate();
            $newPo->note = null;
            $newPo->supli_due = null;
            $newPo->cus_due = null;
            $newPo->podate = Carbon::now()->format('m/d/Y');
            $newPo->save();

            // Batch insert for better performance
            $originalItems = items_tb::where('pid', $id)
                ->select(['item', 'qty', 'price', 'desc', 'unit'])
                ->get();
            
            $newItems = $originalItems->map(function ($item) use ($newPo) {
                return [
                    'pid' => $newPo->poid,
                    'item' => $item->item,
                    'qty' => $item->qty,
                    'price' => $item->price,
                    'desc' => $item->desc,
                    'unit' => $item->unit,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            if (!empty($newItems)) {
                items_tb::insert($newItems);
            }

            DB::commit();
            
            // Clear memory caches
            $this->searchCache = [];
            $this->suggestionCache = [];
            
            $this->alertMessage = 'Purchase order duplicated successfully.';
            $this->alertType = 'success';

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alertMessage = 'Duplication failed: ' . $e->getMessage();
            $this->alertType = 'danger';
        } finally {
            $this->isLoading = false;
        }
    }

    // FIXED: Updated vendor relationship query
    public function render()
    {
        $cacheKey = md5(serialize([$this->searchCustomer, $this->searchPart, $this->searchVendor]));
        
        if (!isset($this->searchCache[$cacheKey])) {
            $query = porder_tb::where('cancel', '!=', 1)
                             ->with(['vendor:data_id,c_shortname']) // FIXED: Use data_id instead of id
                             ->latest('poid');

            // Use exact starts-with matching for better performance
            if (!empty($this->searchCustomer)) {
                $query->where('customer', 'like', $this->searchCustomer . '%');
            }

            if (!empty($this->searchPart)) {
                $query->where('part_no', 'like', $this->searchPart . '%');
            }

            if (!empty($this->searchVendor)) {
                $query->whereHas('vendor', function ($q) {
                    $q->where('c_shortname', 'like', $this->searchVendor . '%')
                      ->orWhere('c_name', 'like', $this->searchVendor . '%');
                });
            }

            $this->searchCache[$cacheKey] = $query->paginate(30);
        }

        $orders = $this->searchCache[$cacheKey];

        return view('livewire.purchase-order.manage', compact('orders'))
               ->layout('layouts.app');
    }

    // Search Actions
    public function searchv()
    {
        $this->searchVendor = $this->searchVendorInput;
        $this->resetPage();
        $this->reset(['searchVendorInput', 'matches_vendor']);
    }

    public function searchq()
    {
        $this->searchPart = $this->searchPartNoInput;
        $this->resetPage();
        $this->reset(['searchPartNoInput', 'matches_partno']);
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
        $this->reset(['searchCustomerInput', 'matches']);
    }

    // Optimized Search Suggestions with Memory Caching
    public function onKeyUp(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }

        $cacheKey = "customer_" . md5($value);
        if (!isset($this->suggestionCache[$cacheKey])) {
            $this->suggestionCache[$cacheKey] = porder_tb::query()
                ->select('customer')
                ->where('customer', 'like', $value . '%')
                ->distinct()
                ->limit(6)
                ->get()
                ->pluck('customer')
                ->map(function ($customer) {
                    return ['customer' => $customer];
                })
                ->toArray();
        }

        $this->matches = $this->suggestionCache[$cacheKey];
    }

    public function usekeyupno(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }

        $cacheKey = "partno_" . md5($value);
        if (!isset($this->suggestionCache[$cacheKey])) {
            $this->suggestionCache[$cacheKey] = porder_tb::query()
                ->select('part_no')
                ->where('part_no', 'like', $value . '%')
                ->distinct()
                ->limit(6)
                ->get()
                ->pluck('part_no')
                ->map(function ($partNo) {
                    return ['part_no' => $partNo];
                })
                ->toArray();
        }

        $this->matches_partno = $this->suggestionCache[$cacheKey];
    }

    public function usekeyupvendor(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_vendor = [];
            return;
        }

        $cacheKey = "vendor_" . md5($value);
        if (!isset($this->suggestionCache[$cacheKey])) {
            $this->suggestionCache[$cacheKey] = vendor_tb::query()
                ->select('data_id', 'c_name', 'c_shortname')
                ->where('c_name', 'like', $value . '%')
                ->orWhere('c_shortname', 'like', $value . '%')
                ->limit(6)
                ->get()
                ->toArray();
        }

        $this->matches_vendor = $this->suggestionCache[$cacheKey];
    }

    public function useMatch($i)
    {
        if (isset($this->matches[$i])) {
            $this->searchCustomerInput = $this->matches[$i]['customer'];
            $this->matches = [];
        }
    }

    public function useMatchpn($i)
    {
        if (isset($this->matches_partno[$i])) {
            $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
            $this->matches_partno = [];
        }
    }

    public function useMatchve($i)
    {
        if (isset($this->matches_vendor[$i])) {
            $this->searchVendorInput = $this->matches_vendor[$i]['c_shortname'] ?? $this->matches_vendor[$i]['c_name'];
            $this->matches_vendor = [];
        }
    }
}