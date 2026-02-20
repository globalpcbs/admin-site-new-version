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

        <div class="card">
            <div class="card-header bg-primary text-white">
                 <h5>
                    <b>
                        <i class="fa fa-list"></i> Manage Quotes
                        <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
                    </b>
                </h5>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover table-striped">
                <thead>
                    <tr>
                        <th><i class="fa fa-hashtag"></i> ID</th>
                        <th><i class="fa fa-file-text-o"></i> Quote #</th>
                        <th><i class="fa fa-user"></i> Customer Name</th>
                        <th><i class="fa fa-cube"></i> Part No</th>
                        <th><i class="fa fa-refresh"></i> Rev</th>
                        <th><i class="fa fa-calendar"></i> Added Date</th>
                        <th><i class="fa fa-cogs"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotes as $quote)
                    <tr>
                        <td>{{ $quote->ord_id }}</td>
                        <td>{{ $quote->ord_id + 10000 }}</td>
                        <td>{{ $quote->cust_name }}</td>
                        <td class="position-relative">
                            @php
                            $alerts = \App\Models\alerts_tb::where('part_no', $quote->part_no)
                            ->where('rev', $quote->rev)
                            ->where('customer', $quote->cust_name)
                            ->get();
                            @endphp

                            @if($alerts->count() > 0)
                            <!-- Hoverable Part Number -->
                            <a href="javascript:void(0)" class="text-danger fw-bold"
                                onmouseover="document.getElementById('alert-box-{{ $quote->ord_id }}').style.display='block'"
                                onmouseout="document.getElementById('alert-box-{{ $quote->ord_id }}').style.display='none'">
                                {{ $quote->part_no }}
                            </a>

                            <!-- Custom Tooltip Box -->
                            <div id="alert-box-{{ $quote->ord_id }}" style="
                                display:none;
                                position:absolute;
                                top:-10px;
                                left:150px;
                                background:#fff8f8;
                                border:1px solid #c33;
                                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                                width:220px;
                                padding:10px;
                                z-index:1000;
                                border-radius:8px;
                            ">
                                <h6 style="color:#c33; font-weight:bold; margin-bottom:8px;">Alerts</h6>
                                @foreach($alerts as $index => $alert)
                                <div style="font-size:13px; color:#333;">
                                    {{ $index + 1 }}. {{ $alert->alert }}
                                </div>
                                @endforeach
                            </div>
                            @else
                            {{ $quote->part_no }}
                            @endif
                        </td>

                        <td>{{ $quote->rev }}</td>
                        <td>{{ \Carbon\Carbon::parse($quote->ord_date)->format('m/d/Y') }}</td>
                        <td>
                            <a href="{{ route('qoutes.edit',$quote->ord_id) }}" class="btn btn-primary btn-xs btn-sm">
                                <i class="fa fa-edit"></i> Edit 
                            </a>
                            <a href="https://files.pcbsglobal.website/download-pdf.php?id={{ $quote->ord_id }}&oper=download&name={{ ucfirst(Auth::user()->username) }}"
                                class="btn btn-success btn-xs btn-sm"> <i class="fa fa-download"></i>  PDF</a>
                            <a href="https://files.pcbsglobal.website/download-pdf.php?id={{ $quote->ord_id }}&oper=view&name={{ ucfirst(Auth::user()->username) }}"
                                class="btn btn-info btn-xs btn-sm" target="_blank"> <i class="fa fa-eye"></i>  PDF</a>
                            <a href="https://files.pcbsglobal.website/download-doc.php?id={{ $quote->ord_id }}"
                                class="btn btn-sm btn-xs btn-secondary"> <i class="fa fa-file"></i> DOC</a>
                            <button wire:click="deleteQuote({{ $quote->ord_id }})" 
                                wire:key="delete-{{ $quote->ord_id }}"
                                wire:confirm="Are you sure you want to delete this quote?"
                                class="btn btn-sm btn-xs btn-danger">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                            <button wire:click="duplicateQuote({{ $quote->ord_id }})" 
                                wire:key="duplicate-{{ $quote->ord_id }}"
                                class="btn btn-xs btn-sm btn-warning">
                                <i class="fa fa-copy"></i> Duplicate
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $quotes->links('pagination::bootstrap-5') }}
        </div>
    </div>

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
                    const selectedValue = customerSuggestions[selectedCustomerIndex].cust_name;
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

        // Fetch part number suggestions from server
        async function fetchPartNoSuggestions(query) {
            if (query.length < 2) {
                return [];
            }
            
            try {
                const response = await fetch(`/api/partno-suggestions?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching part number suggestions:', error);
                return [];
            }
        }
        
        // Fetch customer suggestions from server
        async function fetchCustomerSuggestions(query) {
            if (query.length < 2) {
                return [];
            }
            
            try {
                const response = await fetch(`/api/qoute/customer-suggestions?q=${encodeURIComponent(query)}`);
                const data = await response.json();
              //  console.log(data);
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
                         onclick="selectCustomer('${item.cust_name.replace(/'/g, "\\'")}')"
                         data-index="${index}"
                         onmouseover="highlightCustomerItem(${index})">
                         ${item.cust_name}
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