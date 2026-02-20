<div>
    <div>
        <style>
            .ttip_overlay {
                display: none;
                position: absolute;
                z-index: 1000;
                top: -50px;
                left: -290px;
                width: 270px;
                padding: 10px;
                background: #fff;
                border: 1px solid #336699;
                box-shadow: 2px 2px 8px rgba(0,0,0,0.2);
            }

            td:hover .ttip_overlay {
                display: block;
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
                z-index: 1060;
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
                                       wire:model.live="searchPartNoInput"
                                       wire:keydown.enter="searchq"
                                       placeholder="Enter part number"
                                       autocomplete="off"
                                       onkeyup="showPartNoSuggestions(this.value)"
                                       onfocus="showPartNoSuggestions(this.value)"
                                       onkeydown="handlePartNoKeydown(event)" />
                                <button class="btn btn-primary" type="button" wire:click="searchq" id="partNoSearchBtn">
                                    <i class="fa fa-search" wire:loading.remove></i>
                                    <span wire:loading><i class="fa fa-spinner fa-spin"></i></span>
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
                                       wire:model.live="searchCustomerInput"
                                       wire:keydown.enter="searchbyCustomer"
                                       placeholder="Enter customer name"
                                       autocomplete="off"
                                       onkeyup="showCustomerSuggestions(this.value)"
                                       onfocus="showCustomerSuggestions(this.value)"
                                       onkeydown="handleCustomerKeydown(event)" />
                                <button class="btn btn-primary" type="button" wire:click="searchbyCustomer" id="customerSearchBtn">
                                    <i class="fa fa-search" wire:loading.remove></i>
                                    <span wire:loading><i class="fa fa-spinner fa-spin"></i></span>
                                </button>
                            </div>
                            <div id="customerSuggestions" class="autocomplete-dropdown"></div>
                        </div>

                        <div class="col-lg-2">
                            <br />
                            <button class="btn btn-info mt-2" wire:click="filterclose">
                                <i class="fa fa-rotate-right"></i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5>
                        <i class="fa fa-list"></i> Manage Stock
                        <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
                    </h5>
                </div>
                <div>
                    <table class="table table-bordered table-sm font-xs table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Stk#</th>
                                <th>Customer</th>
                                <th>Part No</th>
                                <th>Rev</th>
                                <th>Supplier</th>
                                <th>Date Add.</th>
                                <th>D/C</th>
                                <th>Finish</th>
                                <th>Mfg Date</th>
                                <th class="hdrow">Panel</th>
                                <th class="hdrow">Shelf Life</th>
                                <th class="hdrow">Expiration Date</th>
                                <th width="40">Stock Qty</th>
                                <th width="40">Remaining Qty</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $s)
                                @php
                                    $rowbg = '';
                                    $expirationDate = null;
                                    $expirationDateFormatted = '';

                                    if (!empty($s->manuf_dt) && !empty($s->shelflife)) {
                                        $manufactureDate = DateTime::createFromFormat('m-d-Y', $s->manuf_dt);
                                        if ($manufactureDate) {
                                            $expirationDate = clone $manufactureDate;
                                            $expirationDate->add(new DateInterval('P' . $s->shelflife . 'M'));
                                            $expirationDateFormatted = $expirationDate->format('m-d-Y');
                                            $today = new DateTime('today');
                                            if ($today >= $expirationDate) {
                                                $rowbg = 'table-danger';
                                            }
                                        }
                                    }

                                    $undeliveredAllocations = $s->allocations->where('delivered_on', '00-00-0000');
                                    $allocatedQty = $undeliveredAllocations->sum('qut');
                                    $stockQty = $s->qty;
                                    $remainingQty = $stockQty - $allocatedQty;
                                @endphp

                                <tr class="{{ $rowbg }}">
                                    <td>{{ $s->stkid }}</td>
                                    <td>{{ $s->customer }}</td>
                                    <td class="ctr" style="position: relative;">
                                        @php $comment = $s->comments; @endphp
                                        <span @if($comment) style="color:red; font-weight:bold;" @endif>
                                            {{ $s->part_no }}
                                        </span>
                                        @if ($comment)
                                            <div class="ttip_overlay">
                                                <h6 class="fw-bold">Comment</h6>
                                                {!! nl2br(e($comment)) !!}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $s->rev }}</td>
                                    <td>{{ $s->vendor->c_name ?? '-' }}</td>
                                    <td>{{ substr($s->dtadded, -10) }} </td>
                                    <td>{{ $s->dc }}</td>
                                    <td>{{ $s->finish }}</td>
                                    <td>{{ $s->manuf_dt }}</td>
                                    <td class='ctr'>{{ 1 == $s->docsready ? "Yes" : "No" }}</td>
                                    <td class='ctr'>{{ $s->shelflife.($s->shelflife==1 ? ' Month' : ' Months') }}</td>
                                    <td class='ctr'>{{ $expirationDateFormatted }}</td>
                                    <td>{{ $stockQty }}</td>
                                    <td style="position: relative;">
                                        @php
                                            $hasUndeliveredAllocations = $allocatedQty > 0;
                                            $isFullyAllocated = $remainingQty == 0 && $allocatedQty > 0;
                                        @endphp
                                        <span @if($hasUndeliveredAllocations || $isFullyAllocated) style="color:red; font-weight:bold;" @endif>
                                            {{ $remainingQty }}
                                        </span>
                                        @if($hasUndeliveredAllocations)
                                            <div class="ttip_overlay" id="aldiv_{{ $s->stkid }}">
                                                <label>Stock Allocation (Undelivered)</label>
                                                <table class="al_tb table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>PO#</th>
                                                            <th>Qty</th>
                                                            <th>Due Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($undeliveredAllocations as $allocation)
                                                            <tr>
                                                                <td>{{ $allocation->customer }}</td>
                                                                <td>{{ $allocation->pono }}</td>
                                                                <td>{{ $allocation->qut }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($allocation->due_date)->format('m-d-Y') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr style="background-color: #f8f9fa;">
                                                            <td colspan="2"><strong>Total Allocated:</strong></td>
                                                            <td><strong>{{ $allocatedQty }}</strong></td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('misc.edit.stock',$s->stkid) }}" class="btn btn-sm btn-xs btn-info">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-xs btn-danger" wire:click="delete({{ $s->stkid }})" wire:confirm="Are you sure you want to delete stock?" wire:key="delete-{{ $s->stkid }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button class="btn btn-sm btn-xs btn-secondary" wire:click="duplicate({{ $s->stkid }})" wire:key="duplicate-{{ $s->stkid }}">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($stocks->isEmpty())
                                <tr>
                                    <td colspan="15" class="text-center">No stock found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    {{ $stocks->links() }}
                </div>
            </div>
        </div>

        <script>
            // Global variables
            let partNoSuggestions = [];
            let customerSuggestions = [];
            let selectedPartNoIndex = -1;
            let selectedCustomerIndex = -1;

            // Handle Part Number input keydown events (arrow keys, escape, tab)
            function handlePartNoKeydown(event) {
                const dropdown = document.getElementById('partNoSuggestions');
                const items = dropdown.getElementsByClassName('autocomplete-item');

                if (event.key === 'ArrowDown') {
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
                    dropdown.style.display = 'none';
                    selectedPartNoIndex = -1;
                } else if (event.key === 'Tab') {
                    dropdown.style.display = 'none';
                    selectedPartNoIndex = -1;
                }
            }

            // Handle Customer input keydown events
            function handleCustomerKeydown(event) {
                const dropdown = document.getElementById('customerSuggestions');
                const items = dropdown.getElementsByClassName('autocomplete-item');

                if (event.key === 'ArrowDown') {
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
                    dropdown.style.display = 'none';
                    selectedCustomerIndex = -1;
                } else if (event.key === 'Tab') {
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
                if (query.length < 2) return [];
                try {
                    const response = await fetch(`/api/stock-partno-suggestions?q=${encodeURIComponent(query)}`);
                    return await response.json();
                } catch (error) {
                    console.error('Error fetching part number suggestions:', error);
                    return [];
                }
            }

            // Fetch customer suggestions from server
            async function fetchCustomerSuggestions(query) {
                if (query.length < 2) return [];
                try {
                    const response = await fetch(`/api/stock-customer-suggestions?q=${encodeURIComponent(query)}`);
                    return await response.json();
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
                input.blur();
                document.getElementById('partNoSuggestions').style.display = 'none';
                selectedPartNoIndex = -1;

                // Update Livewire property and trigger search
                @this.set('searchPartNoInput', partNo);
                @this.searchq();
            }

            // Select customer from dropdown
            function selectCustomer(customerName) {
                const input = document.getElementById('customerInput');
                input.value = customerName;
                input.blur();
                document.getElementById('customerSuggestions').style.display = 'none';
                selectedCustomerIndex = -1;

                // Update Livewire property and trigger search
                @this.set('searchCustomerInput', customerName);
                @this.searchbyCustomer();
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