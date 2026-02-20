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

    <div class="mt-4">
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

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>
                    <b>
                        <i class="fa fa-list"></i> Manage Packing Slips
                        <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
                    </b>
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped align-middle btn-sm text-nowrap">
                    <thead class="table-light text-center">
                        <tr>
                            <th><i class="fa fa-id-badge"></i> Slip ID</th>
                            <th><i class="fa fa-list-ol"></i> Slip #</th>
                            <th><i class="fa fa-user"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part No</th>
                            <th><i class="fa fa-refresh"></i> Rev</th>
                            <th><i class="fa fa-calendar"></i> Packing Date</th>
                            <th>Action</th>
                            <th>Invoiced</th>
                            <th>Logged</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packingSlips as $slip)
                        <tr>
                            <td>{{ $slip->invoice_id ?? 'N/A' }}</td>
                            <td>{{ $slip->invoice_id+9987 ?? 'N/A' }}</td>
                            <td>{{ $slip->custo->c_name ?? 'Customer Not Found' }}</td>
                            <td>{{ $slip->part_no ?? 'N/A' }}</td>
                            <td>{{ $slip->rev ?? 'N/A' }}</td>
                            <td>
                                {{ $slip->podate ? \Carbon\Carbon::parse($slip->podate)->format('m/d/Y') : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ route('packing.edit',$slip->invoice_id) }}">
                                    <button type="button" class="btn btn-sm btn-xs btn-primary">
                                        <i class="fa fa-pencil"></i> Edit
                                    </button>
                                </a>
                                <a href="https://files.pcbsglobal.website/download-pdf3.php?id={{ $slip->invoice_id }}&oper=download">
                                    <button type="button" class="btn btn-sm btn-xs btn-success">
                                        <i class="fa fa-download"></i>  PDF
                                    </button>
                                </a>
                                <a href="https://files.pcbsglobal.website/download-pdf3.php?id={{ $slip->invoice_id }}&oper=view" target="_blank">
                                    <button type="button" class="btn btn-sm btn-xs btn-info">
                                        <i class="fa fa-eye"></i> View PDF
                                    </button>
                                </a>
                                <a href="https://files.pcbsglobal.website/download-doc3.php?id={{ $slip->invoice_id }}">
                                    <button type="button" class="btn btn-sm btn-xs btn-secondary">
                                        <i class="fa fa-download"></i> Doc
                                    </button>
                                </a>
                                <button type="button" class="btn btn-sm btn-xs btn-danger"
                                    wire:click="deletePackingSlip({{ $slip->invoice_id }})" wire:confirm="Are you sure You want to delete packing slip?" wire:key="delete-{{ $slip->invoice_id }}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                                <button type="button" class="btn btn-sm btn-xs btn-warning"
                                    wire:click="duplicate({{ $slip->invoice_id }})" wire:key="duplicate-{{ $slip->invoice_id }}">
                                    <i class="fa fa-copy"></i> Duplicate
                                </button>
                            </td>
                            <td class="text-center">
                                <input type="checkbox" wire:click="togglePending({{ $slip->invoice_id }})"
                                    {{ $slip->pending == 'Yes' ? 'checked' : '' }} onclick="if (!this.checked) {
                    const confirmed = confirm('Are you sure you want to unmark this as pending?');
                    if (!confirmed) event.preventDefault();
                }" class="form-check-input mt-0">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input mt-0"
                                    wire:change="isLogged({{ $slip->invoice_id }})">
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="14" class="text-center">No Packing Slips Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $packingSlips->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    
    @if ($confirmingDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this packing slip?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                    <button class="btn btn-danger" wire:click="deletePackingSlip({{ $deleteId }})">
                        <i class="fa fa-trash"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
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

        // Fetch part number suggestions from server for packing slips
        async function fetchPartNoSuggestions(query) {
            if (query.length < 2) {
                return [];
            }
            
            try {
                const response = await fetch(`/api/packing-partno-suggestions?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                console.log(data);
                return data;
            } catch (error) {
                console.error('Error fetching part number suggestions:', error);
                return [];
            }
        }
        
        // Fetch customer suggestions from server for packing slips
       async function fetchCustomerSuggestions(query) {
            if (query.length < 1) return []; // allow 1 char search

            try {
                const response = await fetch(`/api/customer-suggestions?q=${encodeURIComponent(query)}`);
                if (!response.ok) {
                    console.error('HTTP error:', response.status);
                    return [];
                }
                const data = await response.json();
               // console.log('Suggestions:', data);
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