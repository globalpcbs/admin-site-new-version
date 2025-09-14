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

class Edit extends Component
{
    // Replicate all properties from Add component
    public $basePrice = 2;
    public $manualMode = false;
    public $priceMatrix = [];
    public $quantities = [];
    public $days = [];
    public $manualPrices = [];
    public $day = 15;
    
    // All other form fields from Add component...
    public $alert_pno;
    public $cust_name;
    public $part_no;
    public $rev;
    // ... (include all other properties from Add component)
    
    public $orderId;
    public $order;
    public $customers;
    public $vendors;
    public $search = '';
    public $matches = [];
    public $inputKey;

     // Price calculation properties
    // Basic Information
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
    public $cancharge = 'no';
    public $ccharge;
    public $fob = 'Anaheim';
    public $fob_oth;
    public $vid;
    public $vid_oth;
    
    // Quantities and Prices
    public $qty1, $qty2, $qty3, $qty4, $qty5, $qty6, $qty7, $qty8, $qty9, $qty10;
    public $day1, $day2, $day3, $day4, $day5;
    public $price1 = null, $price2 = null, $price3 = null, $price4 = null;
    
    // PCB Specifications
    public $ipc_class = '3';
    public $no_layer = 'Double Sided';
    public $m_require = 'FR-4';
    public $thickness = '0.062';
    public $thickness_tole = '+/- 10%';
    public $inner_copper = '1';
    public $start_cu = '1';
    public $plated_cu = '.0014';
    public $fingers_gold = false;
    public $trace_min = '.006';
    public $space_min = '.006';
    public $con_impe_sing = false;
    public $con_impe_diff = false;
    public $tore_impe = '+/- 10%';
    public $hole_size;
    public $pad;
    public $blind = false;
    public $buried = false;
    public $hdi_design = false;
    public $resin_filled = false;
    public $cond_vias = false;
    
    // Finish Options
    public $finish = 'HASL';
    public $mask_size = 'Both';
    public $mask_type = 'Glossy';
    public $color = 'Green';
    public $ss_side = '1';
    public $ss_color = 'White';
    
    // Board Details
    public $board_size1;
    public $board_size2;
    public $array = false;
    public $b_per_array;
    public $array_size1;
    public $array_size2;
    public $route_tole = '+/-.005';
    public $array_design = false;
    public $design_array = false;
    public $array_type1 = false;
    public $array_type2 = false;
    public $array_require1 = false;
    public $array_require2 = false;
    public $array_require3 = false;
    public $bevel = false;
    public $counter_sink = false;
    public $cut_outs = false;
    public $slots = false;
    
    // Markings and QA
    public $logo = 'Factory';
    public $mark = false;
    public $date_code = 'WWYY';
    public $other_marking;
    public $micro_section = false;
    public $test_stamp = false;
    public $in_board = false;
    public $array_rail = false;
    public $xouts = false;
    public $xoutsnum;
    public $rosh_cert = false;
    
    // Special Instructions
    public $special_instadmin;
    public $is_spinsadmact = 'no';
    public $sp_reqs;
    
    // Other fields
    public $txtother1, $txtother2, $txtother3, $txtother4, $txtother5, $txtother6, $txtother7;
    public $txtother8, $txtother9, $txtother10, $txtother11, $txtother12, $txtother13, $txtother14;
    public $txtother15, $txtother16, $txtother17, $txtother19, $txtother28, $txtother51, $txtother52;
    public $txtother53, $txtother54, $txtother55, $txtother56;
    public $customer_id; // Add this to store the ID
          // for alerts 
    public bool $showAlertPopup = false;
    public $customers_main = [];
    public $customers_eng = [];
    public $alertMessages = [];
    public bool $showProfilePopup = false;
    public $profileMessages = [];

    // Alert management properties
    public $newAlert = '';
    public $editingAlertId = null;
    public $request_by;
    
    // button status ..
    public $button_status = 0;

    protected $listeners = [
        'simpleQuoteToggled' => 'handleSimpleQuoteToggle',
        'fobChanged' => 'handleFobChange',
        'vendorChanged' => 'handleVendorChange',
    ];

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrderData();
        $this->customers = Customer::all();
        $this->vendors = Vendor::all();
        $this->inputKey = uniqid();
    }

    public function showMiscField()
    {
        $this->showMisc1 = $this->selectedMisc == 'm1';
        $this->showMisc2 = $this->selectedMisc == 'm2';
        $this->showMisc3 = $this->selectedMisc == 'm3';
    }

    protected function loadOrderData()
{
    $this->order = Order::findOrFail($this->orderId);
    
    // Basic Information
    $this->cust_name = $this->order->cust_name;
    $customer = Customer::where('c_name', $this->order->cust_name)->first();
    if(!empty($customer)) {
        $this->customers_main = customermain::where('coustid',$customer->data_id)->get(); 
        $this->customers_eng  = customereng::where('coustid',$customer->data_id)->get(); 
    }
    $this->part_no = $this->order->part_no;
    $this->rev = $this->order->rev;
    $this->new_or_rep = $this->order->new_or_rep;
    $this->request_by = $this->order->req_by;
    $this->email = $this->order->email;
    $this->phone = $this->order->phone;
    $this->fax = $this->order->fax;
    $this->quote_by = $this->order->quote_by;
    $this->necharge = $this->order->necharge;
    $this->comments = $this->order->comments;
    $this->simplequote = $this->order->simplequote == '1';
    $this->reminders = Reminder::where('quoteid', $this->orderId)->exists();
    $this->day = $this->order->days ?? 15;

    // Misc Charges
    $this->descharge = $this->order->descharge;
    $this->desdesc = $this->order->desdesc;
    $this->descharge1 = $this->order->descharge1;
    $this->desdesc1 = $this->order->desdesc1;
    $this->descharge2 = $this->order->descharge2;
    $this->desdesc2 = $this->order->desdesc2;
        // Determine which MISC section to show
    if (!empty($this->order->desdesc2)) {
        $this->selectedMisc = 'm3';
        $this->showMisc3 = true;
    } elseif (!empty($this->order->desdesc1)) {
        $this->selectedMisc = 'm2';
        $this->showMisc2 = true;
    } elseif (!empty($this->order->desdesc)) {
        $this->selectedMisc = 'm1';
        $this->showMisc1 = true;
    }

    // Order Details
    $this->cancharge = $this->order->cancharge;
    $this->ccharge = $this->order->ccharge;
    $this->fob = $this->order->fob;
    $this->fob_oth = $this->order->fob_oth;
    $this->vid = $this->order->vid;
    $this->vid_oth = $this->order->vid_oth;

    // Quantities
    foreach (range(1, 10) as $i) {
        $this->quantities[$i] = $this->order->{'qty'.$i} ?? null;
    }

    // Days
    foreach (range(1, 5) as $i) {
        $this->days[$i] = $this->order->{'day'.$i} ?? null;
    }

    // Manual Prices
    $this->initializeManualPrices();
    foreach (range(1, 10) as $qtyIndex) {
        foreach (range(1, 5) as $dayIndex) {
            $priceField = 'pr'.$qtyIndex.$dayIndex;
            if (!empty($this->order->$priceField)) {
                $this->manualPrices[$qtyIndex][$dayIndex] = $this->order->$priceField;
                $this->manualMode = true;
            }
        }
    }

    // PCB Specifications
    $this->ipc_class = $this->order->ipc_class;
    $this->no_layer = $this->order->no_layer;
    $this->m_require = $this->order->m_require;
    $this->thickness = $this->order->thickness;
    $this->thickness_tole = $this->order->thickness_tole;
    $this->inner_copper = $this->order->inner_copper;
    $this->start_cu = $this->order->start_cu;
    $this->plated_cu = $this->order->plated_cu;
    $this->fingers_gold = $this->order->fingers_gold == 'yes';
    $this->trace_min = $this->order->trace_min;
    $this->space_min = $this->order->space_min;
    $this->con_impe_sing = $this->order->con_impe_sing == 'Yes';
    $this->con_impe_diff = $this->order->con_impe_diff == 'Differential';
    $this->tore_impe = $this->order->tore_impe;
    $this->hole_size = $this->order->hole_size;
    $this->pad = $this->order->pad;
    $this->blind = $this->order->blind == 'yes';
    $this->buried = $this->order->buried == 'yes';
    $this->hdi_design = $this->order->hdi_design == 'Yes';
    $this->resin_filled = $this->order->resin_filled == 'Yes';
    $this->cond_vias = $this->order->cond_vias == 'Yes';

    // Finish Options
    $this->finish = $this->order->finish;
    $this->mask_size = $this->order->mask_size;
    $this->mask_type = $this->order->mask_type;
    $this->color = $this->order->color;
    $this->ss_side = $this->order->ss_side;
    $this->ss_color = $this->order->ss_color;

    // Board Details
    $this->board_size1 = $this->order->board_size1;
    $this->board_size2 = $this->order->board_size2;
    $this->array = $this->order->array == 'YES';
    $this->b_per_array = $this->order->b_per_array;
    $this->array_size1 = $this->order->array_size1;
    $this->array_size2 = $this->order->array_size2;
    $this->route_tole = $this->order->route_tole;
    $this->array_design = $this->order->array_design == 'Yes';
    $this->design_array = $this->order->design_array == 'yes';
    $this->array_type1 = $this->order->array_type1 == 'Tab Route';
    $this->array_type2 = $this->order->array_type2 == 'V Score';
    $this->array_require1 = $this->order->array_require1 == 'Tooling Holes';
    $this->array_require2 = $this->order->array_require2 == 'Fiducials';
    $this->array_require3 = $this->order->array_require3 == 'Mousebites';
    $this->bevel = $this->order->bevel == 'yes';
    $this->counter_sink = $this->order->counter_sink == 'yes';
    $this->cut_outs = $this->order->cut_outs == 'Yes';
    $this->slots = $this->order->slots == 'Yes';

    // Markings and QA
    $this->logo = $this->order->logo;
    $this->mark = $this->order->mark == 'Yes';
    $this->date_code = $this->order->date_code;
    $this->other_marking = $this->order->other_marking;
    $this->micro_section = $this->order->micro_section == 'YES';
    $this->test_stamp = $this->order->test_stamp == 'Yes';
    $this->in_board = $this->order->in_board == 'In Board';
    $this->array_rail = $this->order->array_rail == 'In Array Rail';
    $this->xouts = $this->order->xouts == 'yes';
    $this->xoutsnum = $this->order->xouts1;
    $this->rosh_cert = $this->order->rosh_cert == 'Yes';

    // Special Instructions
    $this->special_instadmin = $this->order->special_instadmin;
    $this->is_spinsadmact = $this->order->is_spinsadmact;
    $this->sp_reqs = $this->order->sp_reqs;

    // Other fields
    $this->txtother1 = $this->order->txtother1;
    $this->txtother2 = $this->order->txtother2;
    $this->txtother3 = $this->order->txtother3;
    $this->txtother4 = $this->order->txtother4;
    $this->txtother5 = $this->order->txtother5;
    $this->txtother6 = $this->order->txtother6;
    $this->txtother7 = $this->order->txtother7;
    $this->txtother8 = $this->order->txtother8;
    $this->txtother9 = $this->order->txtother9;
    $this->txtother10 = $this->order->txtother10;
    $this->txtother11 = $this->order->txtother11;
    $this->txtother12 = $this->order->txtother12;
    $this->txtother13 = $this->order->txtother13;
    $this->txtother14 = $this->order->txtother14;
    $this->txtother15 = $this->order->txtother15;
    $this->txtother16 = $this->order->txtother16;
    $this->txtother17 = $this->order->txtother17;
    $this->txtother19 = $this->order->txtother19;
    $this->txtother28 = $this->order->txtother28;
    $this->txtother51 = $this->order->txtother51;
    $this->txtother52 = $this->order->txtother52;
    $this->txtother53 = $this->order->txtother53;
    $this->txtother54 = $this->order->txtother54;
    $this->txtother55 = $this->order->txtother55;
    $this->txtother56 = $this->order->txtother56;
}

    // Replicate all methods from Add component that are needed for the view
    public function getQuantities()
    {
        return array_filter($this->quantities, function($qty) {
            return !empty($qty);
        });
    }

    public function getLeadTimes()
    {
        return array_filter($this->days, function($day) {
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
    
    public function toggleManualPrice()
    {
        $this->manualMode = true;
    }

    public function updatedQuantities()
    {
        $this->initializeManualPrices();
    }

    public function updatedDays()
    {
        $this->initializeManualPrices();
    }

    protected function initializeManualPrices()
    {
        $this->manualPrices = [];
        for ($i = 1; $i <= 20; $i++) {
            for ($j = 1; $j <= 20; $j++) {
                $this->manualPrices[$i][$j] = null;
            }
        }
    }

    public function updateManualPrice($qIndex, $lIndex, $value)
    {
        $this->manualPrices[$qIndex][$lIndex] = $value;
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
        // dd($profiles->count());
            $hasAlerts = $alerts->count() > 0;
            $hasProfiles = $profiles->count() > 0;
             $this->button_status = 1;
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
            $qoute = Order::where('ord_id',$this->orderId)->first();
            $qoute->update($orderData);
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
            session()->flash('success', 'Quote Updated successfully!');
            
            // Optionally reset the form
            // $this->reset();
            return redirect(route('qoutes.manage'));
        } catch (\Exception $e) {
            session()->flash('warning', 'Error submitting quote: ' . $e->getMessage());
        }
    }

    // Replicate the search functionality from Add component
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
        public function changecustomer(){
        //dd($this->cust_name);
        $customer = Customer::where('c_name', $this->cust_name)->first();
        $this->customers_main = customermain::where('coustid',$customer->data_id)->get(); 
        $this->customers_eng  = customereng::where('coustid',$customer->data_id)->get(); 
    }

    public function render()
    {
        return view('livewire.qoutes.edit')->layout('layouts.app', ['title' => 'Edit Quote']);
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