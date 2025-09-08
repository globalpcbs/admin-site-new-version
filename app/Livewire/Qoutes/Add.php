<?php

namespace App\Livewire\Qoutes;

use Livewire\Component;
use App\Models\order_tb as Order;
use App\Models\data_tb as Customer;
use App\Models\vendor_tb as Vendor;
use App\Models\reminder_tb as Reminder;
// for alerts ..
use App\Models\alerts_tb    as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;
use App\Models\maincont_tb as customermain;
use App\Models\enggcont_tb as customereng;

class Add extends Component
{
    // Form fields - grouped by sections for better organization
    
    // Price calculation properties
    public $basePrice = 2;
    public $manualMode = false;
    public $priceMatrix = [];
    public $quantities  = []; // Initialize 20 quantity fields
    public $days = []; // Initialize 20 day fields
    public $manualPrices = [];
    public $day =15;
    // Basic Information
    public $alert_pno;
    public $cust_name;
    public $part_no;
    public $rev;
    public $new_or_rep = 'New Part';
    public $email;
    public $phone;
    public $fax;
    public $quote_by;
    public $necharge;
    public $comments;
    public $simplequote = false;
    public $reminders = false;
   // public $days = 15;
    
    // Misc Charges
    public $selectedMisc;
    public $showMisc1 = false;
    public $showMisc2 = false;
    public $showMisc3 = false;
    public $descharge;
    public $desdesc;
    public $descharge1;
    public $desdesc1;
    public $descharge2;
    public $desdesc2;
    
    // Order Details
    public $cancharge;
    public $ccharge;
    public $fob;
    public $fob_oth;
    public $vid;
    public $vid_oth;
    
    // Quantities and Prices
    public $qty1, $qty2, $qty3, $qty4, $qty5, $qty6, $qty7, $qty8, $qty9, $qty10;
    public $day1, $day2, $day3, $day4, $day5;
    public $price1 = null, $price2 = null, $price3 = null, $price4 = null;
    
    // PCB Specifications
    public $ipc_class;
    public $no_layer;
    public $m_require;
    public $thickness;
    public $thickness_tole;
    public $inner_copper;
    public $start_cu;
    public $plated_cu;
    public $fingers_gold;
    public $trace_min;
    public $space_min;
    public $con_impe_sing;
    public $con_impe_diff;
    public $tore_impe;
    public $hole_size;
    public $pad;
    public $blind;
    public $buried;
    public $hdi_design;
    public $resin_filled;
    public $cond_vias;
    
    // Finish Options
    public $finish;
    public $mask_size;
    public $mask_type;
    public $color;
    public $ss_side;
    public $ss_color;
    
    // Board Details
    public $board_size1;
    public $board_size2;
    public $array;
    public $b_per_array;
    public $array_size1;
    public $array_size2;
    public $route_tole;
    public $array_design;
    public $design_array;
    public $array_type1;
    public $array_type2;
    public $array_require1;
    public $array_require2;
    public $array_require3;
    public $bevel;
    public $counter_sink;
    public $cut_outs;
    public $slots;
    
    // Markings and QA
    public $logo;
    public $mark;
    public $date_code;
    public $other_marking;
    public $micro_section;
    public $test_stamp;
    public $in_board;
    public $array_rail;
    public $xouts;
    public $xoutsnum;
    public $rosh_cert;
    
    // Special Instructions
    public $special_instadmin;
    public $is_spinsadmact;
    public $sp_reqs;
    
    // Other fields
    public $txtother1, $txtother2, $txtother3, $txtother4, $txtother5, $txtother6, $txtother7;
    public $txtother8, $txtother9, $txtother10, $txtother11, $txtother12, $txtother13, $txtother14;
    public $txtother15, $txtother16, $txtother17, $txtother19, $txtother28, $txtother51, $txtother52;
    public $txtother53, $txtother54, $txtother55, $txtother56;
    public $request_by;
    
    
    // Data for dropdowns
    public $customers;
    public $vendors;
    public $customers_main = [];
    public $customers_eng = [];
    // for search ...
    public $search = '';
    public $matches = [];
    public $inputKey;
    public $customer_id; // Add this to store the ID
          // for alerts 
    public bool $showAlertPopup = false;
    public $alertMessages = [];
    public bool $showProfilePopup = false;
    public $profileMessages = [];

    // Alert management properties
    public $newAlert = '';
    public $editingAlertId;
    // button status ..
    public $button_status = 0;
    protected $listeners = [
        'simpleQuoteToggled' => 'handleSimpleQuoteToggle',
        'fobChanged' => 'handleFobChange',
        'vendorChanged' => 'handleVendorChange',
    ];

    // Add to your component class
    public function getQuantities()
    {
        return array_filter([
            $this->qty1, $this->qty2, $this->qty3, $this->qty4, $this->qty5,
            $this->qty6, $this->qty7, $this->qty8, $this->qty9, $this->qty10
        ], function($qty) {
            return !empty($qty);
        });
    }

    public function getLeadTimes()
    {
        return array_filter([
            $this->day1, $this->day2, $this->day3, $this->day4, $this->day5
        ], function($day) {
            return !empty($day);
        });
    }

        public function handleSimpleQuoteToggle($isChecked)
        {
            $this->simplequote = $isChecked;
        }

        public function handleFobChange($value)
        {
            $this->fob = $value;
        }

        public function handleVendorChange($value)
        {
            $this->vid = $value;
        }
        // Add these methods to your Livewire component


    public function mount()
    {
        $this->customers = Customer::all();
        $this->vendors = Vendor::all();
        $this->inputKey = uniqid(); // force unique key
        // Initialize all manual price inputs (pr11 through pr105)
        // $this->priceInputs = [
        //     // Row 1 (qty1)
        //     'pr11' => null, 'pr12' => null, 'pr13' => null, 'pr14' => null, 'pr15' => null,
            
        //     // Row 2 (qty2)
        //     'pr21' => null, 'pr22' => null, 'pr23' => null, 'pr24' => null, 'pr25' => null,
            
        //     // Row 3 (qty3)
        //     'pr31' => null, 'pr32' => null, 'pr33' => null, 'pr34' => null, 'pr35' => null,
            
        //     // Row 4 (qty4)
        //     'pr41' => null, 'pr42' => null, 'pr43' => null, 'pr44' => null, 'pr45' => null,
            
        //     // Row 5 (qty5)
        //     'pr51' => null, 'pr52' => null, 'pr53' => null, 'pr54' => null, 'pr55' => null,
            
        //     // Row 6 (qty6)
        //     'pr61' => null, 'pr62' => null, 'pr63' => null, 'pr64' => null, 'pr65' => null,
            
        //     // Row 7 (qty7)
        //     'pr71' => null, 'pr72' => null, 'pr73' => null, 'pr74' => null, 'pr75' => null,
            
        //     // Row 8 (qty8)
        //     'pr81' => null, 'pr82' => null, 'pr83' => null, 'pr84' => null, 'pr85' => null,
            
        //     // Row 9 (qty9)
        //     'pr91' => null, 'pr92' => null, 'pr93' => null, 'pr94' => null, 'pr95' => null,
            
        //     // Row 10 (qty10)
        //     'pr101' => null, 'pr102' => null, 'pr103' => null, 'pr104' => null, 'pr105' => null,
        // ];
           // Initialize manual prices array
         // Initialize quantities with 20 null values
    $this->quantities = array_fill(1, 10, null);
    
    // Initialize days with 20 null values
    $this->days = array_fill(1, 5, null);
        $this->initializeManualPrices();
    }

protected function initializeManualPrices()
{
    $this->manualPrices = [];
    
    // Create empty price matrix structure (20x20)
    for ($i = 1; $i <= 20; $i++) {
        for ($j = 1; $j <= 20; $j++) {
            $this->manualPrices[$i][$j] = null;
        }
    }
}
    public function showMiscField()
    {
        $this->showMisc1 = $this->selectedMisc === 'm1';
        $this->showMisc2 = $this->selectedMisc === 'm2';
        $this->showMisc3 = $this->selectedMisc === 'm3';
    }
    
public function toggleManualPrice()
{
    $this->manualMode = true;
    
    // // Initialize empty structure instead of copying calculated prices
    // $quantities = $this->getQuantities();
    // $leadTimes = $this->getLeadTimes();
    
    // foreach ($quantities as $qIndex => $qty) {
    //     foreach ($leadTimes as $lIndex => $day) {
    //         if (!isset($this->manualPrices[$qIndex][$lIndex])) {
    //             $this->manualPrices[$qIndex][$lIndex] = ''; // Initialize empty
    //         }
    //     }
    // }
}
public function updatedQuantities()
{
    // Rebuild price matrix when quantities change
    $this->initializeManualPrices();
}

public function updatedDays()
{
    // Rebuild price matrix when days change
    $this->initializeManualPrices();
}

// protected function initializeManualPrices()
// {
//     $this->manualPrices = $this->priceMatrix;
// }

public function updateManualPrice($qIndex, $lIndex, $value)
{
    $this->manualPrices[$qIndex][$lIndex] = $value;
}
    public function updatedPriceInputs($value, $key)
{
    // Format price inputs as needed
    $this->priceInputs[$key] = is_numeric($value) ? '$' . number_format($value, 2) : $value;
}  
public function updateCustomerId($selectedName)
{
    $customer = Customer::where('c_name', $selectedName)->first();
    $this->customer_id = $customer ? $customer->data_id : null;
    dd($this->customer_id);
}
    public function save(){
        $this->validate([
            'cust_name' => 'required',
            'part_no' => 'required',
            // Add more validation rules as needed
        ]);
        $customer = Customer::where('c_name', $this->cust_name)->first();
        $this->customer_id = $customer ? $customer->data_id : null;
       // dd($this->customer_id);
          $alerts = Alert::where('customer', $this->cust_name)
                ->where('part_no', $this->part_no)
                ->where('rev', $this->rev)
                ->where('atype', 'p')
                ->orderBy('id', 'desc')
                ->get()
                ->filter(function ($alert) {
                    return in_array('quo', explode('|', $alert->viewable));
                });
                // for profile alert ..
            // Check for profile alerts
            $profiles = Profile::where('custid',$this->customer_id)->with('details')
                ->get();
            $this->button_status = 1;
        // dd($profiles->count());
            $hasAlerts = $alerts->count() > 0;
            $hasProfiles = $profiles->count() > 0;

            if ($hasAlerts) {
                $this->showAlertPopup = true;
                $this->alertMessages = $alerts;
            }

            if ($hasProfiles) {
                $this->showProfilePopup = true;
                $this->profileMessages = $profiles;
            }

            // If no alerts at all, save immediately
            if (!$hasAlerts && !$hasProfiles) {
                $this->saveproccess();
            }
      //  $this->saveproccess();
    }
     public function closeAlertPopup(): void
        {
            $this->showAlertPopup = false;
            // dd($this->showAlertPopup);
            $this->checkIfShouldSave();
        }

        public function closeProfilePopup(): void
        {
            $this->showProfilePopup = false;
            //  dd($this->showProfilePopup);
            $this->checkIfShouldSave();
        }

        protected function checkIfShouldSave(): void
        {
            // Only save if both popups are closed
            if (!$this->showAlertPopup && !$this->showProfilePopup) {
            // dd("main save function");
                $this->saveproccess();
            }
        }
        public array $alertTypes = [];
        public function addAlert(): void
        {
            $this->validate([
                'newAlert' => 'required|string|max:255',
                'alertTypes' => 'required|array|min:1'
            ]);

            // Debug before save
            logger()->debug('Pre-Save Data', [
                'alert' => $this->newAlert,
                'types' => $this->alertTypes,
                'imploded' => collect($this->alertTypes)->implode('|')
            ]);

            try {
            // dd($this->alertTypes);
                $alert = Alert::create([
                    'customer' => $this->cust_name ?? '',
                    'part_no' => $this->part_no ?? '',
                    'rev' => $this->rev ?? '',
                    'alert' => trim($this->newAlert),
                    'viewable' => collect($this->alertTypes)->implode('|'),
                    'atype' => 'p',
                ]);

                // Debug after save
                logger()->debug('Created Alert', $alert->toArray());

                $this->reset(['newAlert', 'alertTypes']);
                $this->loadAlerts();
                session()->flash('success', 'Alert added successfully.');
                
            } catch (\Exception $e) {
                logger()->error('Alert Creation Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                session()->flash('error', 'Failed to add alert. Check logs for details.');
            }
        }


        public function loadAlerts()
        {
        $alerts = Alert::where('customer', $this->cust_name)
            ->where('part_no', $this->part_no)
            ->where('rev', $this->rev)
            ->where('atype', 'p')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($alert) {
                return in_array('quo', explode('|', $alert->viewable));
            });

            if($alerts->count() > 0) {
                //$this->showAlertPopup = true;
                $this->alertMessages = $alerts;
            }
        }

        public function editAlert($id)
        {
            $alert = Alert::findOrFail($id);
            
            $this->editingAlertId = $id;
            $this->newAlert = $alert->alert;
            
            // Clear the array first
            $this->alertTypes = [];
            
            // Small delay to ensure Livewire processes the change
            usleep(1000);
            
            // Set the new values
            $this->alertTypes = collect(explode('|', $alert->viewable))
                ->map(fn($item) => trim($item))
                ->filter()
                ->values()
                ->toArray();
            
            // Force Livewire to update the view
            $this->js('window.dispatchEvent(new CustomEvent("alert-types-updated"))');
        }


        public function updateAlert()
        {
            $this->validate(['newAlert' => 'required|string|max:255']);
            //dd($this->newAlert);
            $viewable = collect($this->alertTypes)->filter()->implode('|');

            Alert::where('id', $this->editingAlertId)->update([
                'alert' => trim($this->newAlert),
                'viewable' => $viewable,
            ]);

            $this->reset(['newAlert', 'alertTypes', 'editingAlertId']);
            $this->loadAlerts();
        }


        public function deleteAlert($id)
        {
            Alert::where('id', $id)->delete();
            $this->loadAlerts();
        }

        public function cancelEdit()
        {
            $this->resetAlertInputs();
        }

        public function resetAlertInputs()
        {
            $this->reset(['newAlert','alertTypes']);
        }
    public function saveproccess()
    {
       // dd($this->desdesc2);
        // Validate the form data
        // Prepare price data from manual inputs
        $priceData = [];
        foreach ($this->manualPrices as $qIndex => $leadTimes) {
            foreach ($leadTimes as $lIndex => $price) {
                if ($price !== '') {
                    $priceData["price_q{$qIndex}_d{$lIndex}"] = $price;
                }
            }
        }

        // Prepare the data for insertion
        $orderData = [
            'cust_name' => $this->cust_name,
            'part_no' => $this->part_no,
            'rev' => $this->rev,
            'req_by' => $this->request_by, // Assuming same as customer name
            'email' => $this->email,
            'phone' => $this->phone,
            'fax' => $this->fax,
            'quote_by' => $this->quote_by,
            'necharge' => $this->necharge,
            'descharge' => $this->descharge,
            'descharge1' => $this->descharge1,
            'descharge2' => $this->descharge2,
             'desdesc' => $this->desdesc,
            'desdesc1' => $this->desdesc1,
            'desdesc2' => $this->desdesc2,
            'special_instadmin' => $this->special_instadmin,
            'is_spinsadmact' => $this->is_spinsadmact,
            'ipc_class' => $this->ipc_class,
            'no_layer' => $this->no_layer,
            'm_require' => $this->m_require,
            'thickness' => $this->thickness,
            'thickness_tole' => $this->thickness_tole,
            'inner_copper' => $this->inner_copper,
            'start_cu' => $this->start_cu,
            'plated_cu' => $this->plated_cu,
            'fingers_gold' => $this->fingers_gold ? 'yes' : 'no',
            'trace_min' => $this->trace_min,
            'space_min' => $this->space_min,
            'con_impe_sing' => $this->con_impe_sing ? 'Yes' : 'No',
            'con_impe_diff' => $this->con_impe_diff ? 'Differential' : '',
            'tore_impe' => $this->tore_impe,
            'hole_size' => $this->hole_size,
            'pad' => $this->pad,
            'blind' => $this->blind ? 'yes' : 'no',
            'buried' => $this->buried ? 'yes' : 'no',
            'hdi_design' => $this->hdi_design ? 'Yes' : 'No',
            'finish' => $this->finish,
            'mask_size' => $this->mask_size,
            'mask_type' => $this->mask_type,
            'color' => $this->color,
            'ss_side' => $this->ss_side,
            'ss_color' => $this->ss_color,
            'board_size1' => $this->board_size1,
            'board_size2' => $this->board_size2,
            'array' => $this->array ? 'YES' : 'NO',
            'b_per_array' => $this->b_per_array,
            'array_size1' => $this->array_size1,
            'array_size2' => $this->array_size2,
            'route_tole' => $this->route_tole,
            'array_design' => $this->array_design ? 'Yes' : 'No',
            'design_array' => $this->design_array ? 'yes' : 'no',
            'array_type1' => $this->array_type1 ? 'Tab Route' : '',
            'array_type2' => $this->array_type2 ? 'V Score' : '',
            'array_require1' => $this->array_require1 ? 'Tooling Holes' : '',
            'array_require2' => $this->array_require2 ? 'Fiducials' : '',
            'array_require3' => $this->array_require3 ? 'Mousebites' : '',
            'bevel' => $this->bevel ? 'yes' : 'no',
            'counter_sink' => $this->counter_sink ? 'yes' : 'no',
            'cut_outs' => $this->cut_outs ? 'Yes' : 'No',
            'slots' => $this->slots ? 'Yes' : 'No',
            'logo' => $this->logo,
            'mark' => $this->mark ? 'Yes' : 'No',
            'date_code' => $this->date_code,
            'other_marking' => $this->other_marking,
            'micro_section' => $this->micro_section ? 'YES' : 'NO',
            'test_stamp' => $this->test_stamp ? 'Yes' : 'No',
            'in_board' => $this->in_board ? 'In Board' : '',
            'array_rail' => $this->array_rail ? 'In Array Rail' : '',
            'xouts' => $this->xouts ? 'yes' : 'no',
            'xouts1' => $this->xoutsnum,
            'rosh_cert' => $this->rosh_cert ? 'Yes' : 'No',
            'ord_date' => now()->format('m/d/Y'),
            'cancharge' => $this->cancharge,
            'ccharge' => $this->ccharge,
            'fob' => $this->fob,
            'price1' => $this->price1,
            'price2' => $this->price2,
            'price3' => $this->price3,
            'price4' => $this->price4,
            'new_or_rep' => $this->new_or_rep,
            'cond_vias' => $this->cond_vias ? 'Yes' : 'No',
            'resin_filled' => $this->resin_filled ? 'Yes' : 'No',
            'sp_reqs' => $this->sp_reqs,
            'comments' => $this->comments,
            'simplequote' => $this->simplequote ? '1' : '0',
            'fob_oth' => $this->fob_oth,
            'vid' => $this->vid,
            'vid_oth' => $this->vid_oth,
            // Add all other fields from your form
        ];
      //  dd($orderData);
         // Add quantities to order data
    foreach ($this->quantities as $index => $qty) {
        $orderData['qty'.$index] = $qty;
    }
    
    // Add days to order data
    foreach ($this->days as $index => $day) {
        $orderData['day'.$index] = $day;
    }
    
    // Add manual prices to order data
    foreach ($this->manualPrices as $qtyIndex => $dayPrices) {
        foreach ($dayPrices as $dayIndex => $price) {
            if (!empty($price)) {
                $orderData['pr'.$qtyIndex.$dayIndex] = $price;
            }
        }
    }
        try {
            // Create the order
            $qoute = Order::create($orderData);
            //dd($qoute->ord_id);
            // Normalize checkbox value like legacy PHP
             $enabled = $this->reminders ? 'yes' : 'no';

            // Check if reminder exists
            $reminder = Reminder::where('quoteid', $qoute->ord_id)->first();

            if ($reminder) {
                $reminder->update([
                    'enabled' => $enabled,
                    'days' => $this->day,
                ]);
            } else {
                if ($enabled === 'yes') {
                    Reminder::create([
                        'quoteid' => $qoute->ord_id,
                        'enabled' => $enabled,
                        'days' => $this->day,
                        'lastreminder' => now(),
                    ]);
                }
            }
            // Show success message
            session()->flash('success', 'Quote submitted successfully!');
            
            // Optionally reset the form
            // $this->reset();
            return redirect(route('qoutes.manage'));
        } catch (\Exception $e) {
            session()->flash('warning', 'Error submitting quote: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        $customers = Customer::all();
        return view('livewire.qoutes.add',compact('customers'))->layout('layouts.app', ['title' => 'Add Quotes']);
    }
        public function onKeyUp(string $value): void
    {
        $this->search = $value;

        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }

        $this->matches = Order::select('part_no', 'rev', 'cust_name')
            ->where('part_no', 'like', "%{$value}%")
            ->orWhere('cust_name', 'like', "%{$value}%")
            ->distinct()
            ->get()
            ->map(fn ($row) => [
                'label' => "{$row->part_no}_{$row->rev}_{$row->cust_name}",
                'part' => $row->part_no,
                'rev' => $row->rev,
                'cust' => $row->cust_name,
            ])->toArray();
    }

    public function selectLookup(string $part, string $rev, string $cust): void
    {
        $this->search = "{$cust}_{$part}_{$rev}";
        $this->matches = [];

        $order = Order::where('part_no', $part)
            ->where('rev', $rev)
            ->where('cust_name', $cust)
            ->first();
        $customer = Customer::where('c_name', $order->cust_name)->first();
        $this->customers_main = customermain::where('coustid',$customer->data_id)->get(); 
        $this->customers_eng  = customereng::where('coustid',$customer->data_id)->get(); 
        if ($order) {
            $this->cust_name = $order->cust_name;
            $this->rev = $order->rev;
            $this->part_no = $order->part_no;
        }
        $this->inputKey = uniqid();
    }

    public function useMatch(int $i): void
    {
        if (!isset($this->matches[$i])) return;
        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }
    public function requestby(){
       //bbbbbbbbbbbbbbbb  , dd($this->request_by);
       // $this->customers_main = customermain::where('coustid',$customer->data_id)->get(); 
        $main = customermain::where('name',$this->request_by)->first();
        $eng = customereng::where('name',$this->request_by)->first();
        if(!empty($main)){
            $this->email = $main->email;
            $this->phone = $main->phone;
        } else if(!empty($eng)){
            $this->email = $eng->email;
            $this->phone = $eng->phone;
        }
        $this->inputKey = uniqid();
      //  dd($this->email);
    }
}