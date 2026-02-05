<?php

namespace App\Livewire\Customers\Alerts;

use App\Models\alerts_tb;
use App\Models\data_tb as customer;
use App\Models\order_tb;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class AddPartNumberAlerts extends Component
{
    // Main properties
    public $aid = '';
    public $txtcust = '';
    public $pno = '';
    public $rev = '';
    public $alerts = [];
    public $newpno = 'no';
    
    public function mount()
    {
        $this->alerts = [['text' => '', 'viewable' => []]];
    }
    
    /**
     * Select an option from search results
     */
    public function selectOption($orderId, $customer = null, $partNo = null, $revision = null)
    {
        try {
            // If customer, partNo, rev are provided directly, use them
            if ($customer && $partNo) {
                $this->aid = $orderId;
                $this->txtcust = $customer;
                $this->pno = $partNo;
                $this->rev = $revision ?: '';
            } else {
                // Fallback: fetch from database
                $order = order_tb::find($orderId);
                
                if (!$order) {
                    session()->flash('error', 'Selected order not found.');
                    return;
                }
                
                $this->aid = $orderId;
                $this->txtcust = $order->cust_name ?? '';
                $this->pno = $order->part_no ?? '';
                $this->rev = $order->rev ?? '';
            }
            
            // Load existing alerts for this part
            $this->loadExistingAlerts();
            
            session()->flash('success', 'Selected: ' . $this->txtcust . '_' . $this->pno . ($this->rev ? '_' . $this->rev : ''));
            
        } catch (\Exception $e) {
            Log::error('Select option error: ' . $e->getMessage());
            session()->flash('error', 'Error selecting option.');
        }
    }
    
    /**
     * Load existing alerts for the selected part
     */
    public function loadExistingAlerts()
    {
        if (!$this->txtcust || !$this->pno) {
            $this->alerts = [['text' => '', 'viewable' => []]];
            return;
        }

        try {
            $existingAlerts = alerts_tb::where('customer', trim($this->txtcust))
                ->where('part_no', trim($this->pno))
                ->where('rev', trim($this->rev ?: ''))
                ->where('atype', 'p')
                ->get();

            if ($existingAlerts->count() > 0) {
                $this->alerts = [];
                foreach ($existingAlerts as $alert) {
                    $this->alerts[] = [
                        'text' => $alert->alert ?? '',
                        'viewable' => !empty($alert->viewable) ? explode('|', $alert->viewable) : [],
                    ];
                }
            } else {
                // If no existing alerts, ensure at least one empty alert row
                $this->alerts = [['text' => '', 'viewable' => []]];
            }
            
        } catch (\Exception $e) {
            Log::error('Load alerts error: ' . $e->getMessage());
            $this->alerts = [['text' => '', 'viewable' => []]];
        }
    }
    
    /**
     * Clear entire selection and reset form
     */
    public function clearSelection()
    {
        $this->aid = '';
        $this->txtcust = '';
        $this->pno = '';
        $this->rev = '';
        $this->alerts = [['text' => '', 'viewable' => []]];
        
        session()->flash('info', 'Selection cleared. You can search again.');
    }
    
      public function resetForm()
    {
        $this->txtcust = '';
        $this->pno = '';
        $this->rev = '';
        $this->alerts = [['text' => '', 'viewable' => []]];
    }
    
    /**
     * Add a new alert row
     */
    public function addAlert()
    {
        $this->alerts[] = ['text' => '', 'viewable' => []];
    }
    
    /**
     * Remove an alert row
     */
    public function removeAlert($index)
    {
        if (count($this->alerts) > 1) {
            unset($this->alerts[$index]);
            $this->alerts = array_values($this->alerts);
        }
    }
    
    /**
     * Save alerts
     */
    public function save()
    {
        $this->validate([
            'txtcust' => 'required',
            'pno' => 'required',
            'alerts.*.text' => 'required',
        ]);
        
        // Delete existing alerts for this combination
        alerts_tb::where('customer', $this->txtcust)
            ->where('part_no', $this->pno)
            ->where('rev', $this->rev)
            ->where('atype', 'p')
            ->delete();
        
        // Save new alerts
        foreach ($this->alerts as $alert) {
            if (!empty(trim($alert['text']))) {
                alerts_tb::create([
                    'customer' => $this->txtcust,
                    'part_no' => $this->pno,
                    'rev' => $this->rev,
                    'alert' => trim($alert['text']),
                    'viewable' => !empty($alert['viewable']) ? implode('|', $alert['viewable']) : '',
                    'atype' => 'p',
                    'created_at' => now(),
                ]);
            }
        }
        
        // Redirect without flash message
        return redirect()->route('customers.alerts.manage-part');
    }
    
    public function render()
    {
        return view('livewire.customers.alerts.add-part-number-alerts')
            ->layout('layouts.app', ['title' => 'Add Part Alerts']);
    }
}