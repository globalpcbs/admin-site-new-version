<div>
    <div>
            @if (session()->has('success'))
        <div 
            class="alert alert-success shadow"
            style="
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            "
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 3000)"
        >
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
      @if($alertMessage)
        <div 
            class="alert alert-{{ $alertType }} shadow"
            style="
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            "
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => { show = false; $wire.dispatch('alert-hidden') }, 3000)"
        >
            <i class="fa fa-{{ $alertType == 'success' ? 'check' : 'times' }}-circle"></i> 
            {{ $alertMessage }}
        </div>
    @endif

    <style>
        .table td, 
        .table th {
            white-space: nowrap;
            vertical-align: middle;
        }

        .table {
            width: auto;
            table-layout: auto;
        }
        
        .autocomplete-dropdown {
            position: absolute;
            width: 100%;
            max-height: 220px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1050;
            display: none;
            font-size: 0.875rem;
        }
        
        .autocomplete-item {
            padding: 6px 10px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.875rem;
            line-height: 1.3;
        }
        
        .autocomplete-item:hover,
        .autocomplete-item.selected {
            background-color: #007bff;
            color: white;
        }
        
        .autocomplete-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header fw-bold">Search By</div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search by Part Number -->
                    <div class="col-lg-5 position-relative">
                        <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="partNoInput"
                                   wire:model="searchPartNoInput"
                                   placeholder="Enter part number" 
                                   autocomplete="off"
                                   onkeyup="showPartNoSuggestions(this.value)"
                                   onfocus="showPartNoSuggestions(this.value)"
                                   onkeydown="handlePartNoKeydown(event)" />
                            <button class="btn btn-primary" type="button" onclick="performPartNoSearch()" id="partNoSearchBtn">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div id="partNoSuggestions" class="autocomplete-dropdown"></div>
                    </div>

                    <!-- Search by Customer Name -->
                    <div class="col-lg-5 position-relative">
                        <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="customerInput"
                                   wire:model="searchCustomerInput"
                                   placeholder="Enter customer name" 
                                   autocomplete="off"
                                   onkeyup="showCustomerSuggestions(this.value)"
                                   onfocus="showCustomerSuggestions(this.value)"
                                   onkeydown="handleCustomerKeydown(event)" />
                            <button class="btn btn-primary" type="button" onclick="performCustomerSearch()" id="customerSearchBtn">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div id="customerSuggestions" class="autocomplete-dropdown"></div>
                    </div>
                    
                    <div class="col-lg-2">
                        <br />
                        <button class="btn btn-info mt-2" wire:click="filterclose" onclick="resetInputFields()">
                            <i class="fa fa-rotate-right"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="overflow: hidden;">
            <div class="card-header bg-primary text-white">
                <h5>
                    <b>
                        <i class="fa fa-list"></i> Manage Invoice
                        <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
                    </b>
                </h5>
            </div>
            
            <!-- Invoice Table -->
            <table class="table table-bordered table-hover table-striped align-middle table-sm">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-key"></i> ID</th>
                            <th><i class="fa fa-hashtag"></i> Inv</th>
                            <th><i class="fa fa-user"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part No</th>
                            <th><i class="fa fa-retweet"></i> Rev</th>
                            <th><i class="fa fa-calendar"></i> Inv Date</th>
                            <th><i class="fa fa-exclamation-circle"></i> Past <br /> Due</th>
                            <th><i class="fa fa-check-square-o"></i> Paid</th>
                            <th><i class="fa fa-envelope-o"></i> Stop <br /> Mails</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_id }}</td>
                            <td>{{ $invoice->invoice_id + 9976 }}</td>
                            <td>{{ optional($invoice->customerRel)->c_shortname ?? $invoice->customer }}</td>
                            <td>{{ $invoice->part_no }}</td>
                            <td>{{ $invoice->rev }}</td>
                            <td>{{ $invoice->podate }}</td>
                            <td>
                                <input type="checkbox" 
                                    @if ($invoice->pending == 1) checked @endif
                                    wire:click="togglePending({{ $invoice->invoice_id }})"
                                    onclick="return confirm('Do you want to unmark this invoice as pending?')">
                            </td>

                            <td>
                                <input type="checkbox" 
                                    @if ($invoice->ispaid == '1') checked @endif
                                    wire:click="togglePaid({{ $invoice->invoice_id }})"
                                    onclick="return confirm('Do you want to unmark the invoice as unpaid?')"
                                    title="Type: {{ $invoice->paytype }}, Detail: {{ $invoice->paydetail }}, Date: {{ $invoice->paydate }}, Note: {{ $invoice->paynote }}">
                                
                                @if ($invoice->ispaid == '1')
                                <div class="custom-tooltip shadow">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold text-nowrap">Type:</td>
                                            <td>{{ ucwords($invoice->paytype) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Detail:</td>
                                            <td>{{ $invoice->paydetail }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Date:</td>
                                            <td>{{ $invoice->paydate }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Note:</td>
                                            <td>{{ $invoice->paynote }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @endif
                            </td>

                            <td>
                                <input type="checkbox" 
                                    @if ($invoice->mailstop == '1') checked @endif
                                    wire:click="toggleMailStop({{ $invoice->invoice_id }})"
                                    onclick="return confirm('Do you want to unmark Mail Stop for this invoice?')">
                            </td>

                            <td>
                                 <a href="{{ route('invoice.edit',$invoice->invoice_id) }}">
                                    <button class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</button>
                                </a>
                                <a href="https://files.pcbsglobal.website/download-pdf2.php?id={{ $invoice->invoice_id }}&oper=download"
                                    class="btn btn-sm btn-success btn-xs"><i class="fa fa-download"></i> PDF</a>
                                <a href="https://files.pcbsglobal.website/download-pdf2.php?id={{ $invoice->invoice_id }}&oper=view"
                                    class="btn btn-sm btn-info btn-xs" target="_blank"><i class="fa fa-eye"></i> PDF</a>
                                                                   
                                <button class="btn btn-sm btn-danger btn-xs"
                                    wire:click="delete({{ $invoice->invoice_id }})" wire:confirm="Are you sure You want to delete invoice?" wire:key="delete-{{ $invoice->invoice_id }}">
                                    <i class="fa fa-trash"></i> Del
                                </button>
                                <button class="btn btn-sm btn-warning btn-xs"
                                    wire:click="duplicate({{ $invoice->invoice_id }})" wire:key="duplicate-{{ $invoice->invoice_id }}">
                                    <i class="fa fa-copy"></i> Duplicate
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15">No matching invoices found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $invoices->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @if ($showPaymentModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-credit-card"></i> Enter Payment Details</h5>
                    <button type="button" class="btn-close" wire:click="$set('showPaymentModal', false)"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Payment Type</label><br>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" wire:model="paytype" value="check"> Check
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" wire:model="paytype" value="wire"> Wire
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" wire:model="paytype" value="transfer"> Transfer
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Detail</label>
                        <input type="text" class="form-control" wire:model.defer="paydetail">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" wire:model.defer="paydate" placeholder="mm/dd/yyyy">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Note</label>
                        <input type="text" class="form-control" wire:model.defer="paynote">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('showPaymentModal', false)">Cancel</button>
                    <button class="btn btn-primary" wire:click="savePayment">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        // Global variables
        let partNoSuggestions = [];
        let customerSuggestions = [];
        let selectedPartNoIndex = -1;
        let selectedCustomerIndex = -1;

        // Reset inputs when filter close is clicked
        function resetInputFields() {
            document.getElementById('partNoInput').value = '';
            document.getElementById('customerInput').value = '';
            
            // Also update Livewire properties to ensure consistency
            @this.set('searchPartNoInput', '');
            @this.set('searchCustomerInput', '');
        }

        // Perform part number search and clear input
        function performPartNoSearch() {
            const input = document.getElementById('partNoInput');
            // Trigger Livewire search
            @this.searchq();
            // Clear input immediately for UI feedback
            input.value = '';
            input.blur();
            // Hide suggestions dropdown
            document.getElementById('partNoSuggestions').style.display = 'none';
            selectedPartNoIndex = -1;
        }

        // Perform customer search and clear input
        function performCustomerSearch() {
            const input = document.getElementById('customerInput');
            // Trigger Livewire search
            @this.searchbyCustomer();
            // Clear input immediately for UI feedback
            input.value = '';
            input.blur();
            // Hide suggestions dropdown
            document.getElementById('customerSuggestions').style.display = 'none';
            selectedCustomerIndex = -1;
        }

        // Handle Part Number input keydown events
        function handlePartNoKeydown(event) {
            const dropdown = document.getElementById('partNoSuggestions');
            const items = dropdown.getElementsByClassName('autocomplete-item');
            const input = document.getElementById('partNoInput');
            
            if (event.key === 'Enter') {
                event.preventDefault();
                if (selectedPartNoIndex >= 0 && items[selectedPartNoIndex]) {
                    // Select from dropdown
                    const selectedValue = partNoSuggestions[selectedPartNoIndex].part_no;
                    selectPartNo(selectedValue);
                } else {
                    // Perform search with current input value
                    performPartNoSearch();
                }
                dropdown.style.display = 'none';
                selectedPartNoIndex = -1;
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (dropdown.style.display === 'block' && items.length > 0) {
                    selectedPartNoIndex = (selectedPartNoIndex + 1) % items.length;
                    updateSelection(items, selectedPartNoIndex);
                }
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (dropdown.style.display === 'block' && items.length > 0) {
                    selectedPartNoIndex = (selectedPartNoIndex - 1 + items.length) % items.length;
                    updateSelection(items, selectedPartNoIndex);
                }
            } else if (event.key === 'Escape') {
                input.blur();
                dropdown.style.display = 'none';
                selectedPartNoIndex = -1;
            } else if (event.key === 'Tab') {
                // Hide dropdown when tabbing away
                dropdown.style.display = 'none';
                selectedPartNoIndex = -1;
            }
        }

        // Handle Customer input keydown events
        function handleCustomerKeydown(event) {
            const dropdown = document.getElementById('customerSuggestions');
            const items = dropdown.getElementsByClassName('autocomplete-item');
            const input = document.getElementById('customerInput');
            
            if (event.key === 'Enter') {
                event.preventDefault();
                if (selectedCustomerIndex >= 0 && items[selectedCustomerIndex]) {
                    // Select from dropdown
                    const selectedValue = customerSuggestions[selectedCustomerIndex].customer;
                    selectCustomer(selectedValue);
                } else {
                    // Perform search with current input value
                    performCustomerSearch();
                }
                dropdown.style.display = 'none';
                selectedCustomerIndex = -1;
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                if (dropdown.style.display === 'block' && items.length > 0) {
                    selectedCustomerIndex = (selectedCustomerIndex + 1) % items.length;
                    updateSelection(items, selectedCustomerIndex);
                }
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                if (dropdown.style.display === 'block' && items.length > 0) {
                    selectedCustomerIndex = (selectedCustomerIndex - 1 + items.length) % items.length;
                    updateSelection(items, selectedCustomerIndex);
                }
            } else if (event.key === 'Escape') {
                input.blur();
                dropdown.style.display = 'none';
                selectedCustomerIndex = -1;
            } else if (event.key === 'Tab') {
                // Hide dropdown when tabbing away
                dropdown.style.display = 'none';
                selectedCustomerIndex = -1;
            }
        }

        // Update selection in dropdown
        function updateSelection(items, selectedIndex) {
            for (let i = 0; i < items.length; i++) {
                if (i === selectedIndex) {
                    items[i].classList.add('selected');
                    items[i].scrollIntoView({ block: 'nearest' });
                } else {
                    items[i].classList.remove('selected');
                }
            }
        }

        // Fetch part number suggestions from server for invoices
        async function fetchPartNoSuggestions(query) {
            if (query.length < 2) {
                return [];
            }
            
            try {
                const response = await fetch(`/api/invoice-partno-suggestions?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching part number suggestions:', error);
                return [];
            }
        }
        
        // Fetch customer suggestions from server for invoices
        async function fetchCustomerSuggestions(query) {
            if (query.length < 2) {
                return [];
            }
            
            try {
                const response = await fetch(`/api/invoice-customer-suggestions?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching customer suggestions:', error);
                return [];
            }
        }
        
        // Show part number suggestions
        async function showPartNoSuggestions(query) {
            const dropdown = document.getElementById('partNoSuggestions');
            selectedPartNoIndex = -1;
            
            if (query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }
            
            partNoSuggestions = await fetchPartNoSuggestions(query);
            
            if (partNoSuggestions.length > 0) {
                dropdown.innerHTML = partNoSuggestions.map((item, index) => 
                    `<div class="autocomplete-item" 
                         onclick="selectPartNo('${item.part_no.replace(/'/g, "\\'")}')" 
                         data-index="${index}"
                         onmouseover="highlightPartNoItem(${index})">
                         ${item.part_no}
                    </div>`
                ).join('');
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }
        
        // Show customer suggestions
        async function showCustomerSuggestions(query) {
            const dropdown = document.getElementById('customerSuggestions');
            selectedCustomerIndex = -1;
            
            if (query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }
            
            customerSuggestions = await fetchCustomerSuggestions(query);
            
            if (customerSuggestions.length > 0) {
                dropdown.innerHTML = customerSuggestions.map((item, index) => 
                    `<div class="autocomplete-item" 
                         onclick="selectCustomer('${item.customer.replace(/'/g, "\\'")}')"
                         data-index="${index}"
                         onmouseover="highlightCustomerItem(${index})">
                         ${item.customer}
                    </div>`
                ).join('');
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }
        
        // Highlight part number item on mouseover
        function highlightPartNoItem(index) {
            selectedPartNoIndex = index;
            const items = document.getElementById('partNoSuggestions').getElementsByClassName('autocomplete-item');
            updateSelection(items, selectedPartNoIndex);
        }
        
        // Highlight customer item on mouseover
        function highlightCustomerItem(index) {
            selectedCustomerIndex = index;
            const items = document.getElementById('customerSuggestions').getElementsByClassName('autocomplete-item');
            updateSelection(items, selectedCustomerIndex);
        }
        
        // Select part number from dropdown
        function selectPartNo(partNo) {
            const input = document.getElementById('partNoInput');
            input.value = partNo;
            @this.set('searchPartNoInput', partNo);
            document.getElementById('partNoSuggestions').style.display = 'none';
            selectedPartNoIndex = -1;
            
            // Trigger search and clear input
            setTimeout(() => {
                @this.searchq();
                // Clear input after search is triggered
                setTimeout(() => {
                    input.value = '';
                }, 50);
            }, 100);
        }
        
        // Select customer from dropdown
        function selectCustomer(customerName) {
            const input = document.getElementById('customerInput');
            input.value = customerName;
            @this.set('searchCustomerInput', customerName);
            document.getElementById('customerSuggestions').style.display = 'none';
            selectedCustomerIndex = -1;
            
            // Trigger search and clear input
            setTimeout(() => {
                @this.searchbyCustomer();
                // Clear input after search is triggered
                setTimeout(() => {
                    input.value = '';
                }, 50);
            }, 100);
        }
        
        // Hide dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.position-relative')) {
                document.getElementById('partNoSuggestions').style.display = 'none';
                document.getElementById('customerSuggestions').style.display = 'none';
                selectedPartNoIndex = -1;
                selectedCustomerIndex = -1;
            }
        });

        // Initialize - hide dropdowns on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('partNoSuggestions').style.display = 'none';
            document.getElementById('customerSuggestions').style.display = 'none';
        });
    </script>
</div>
</div>