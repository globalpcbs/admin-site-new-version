<div>
    <div>
        <!-- Success Alert from Session -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" id="successAlert">
                <i class="fa fa-check-square"></i>  {{ session('success') }}
            </div>
            
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('successAlert');
                    alert.classList.remove('show');
                    setTimeout(() => alert.style.display = 'none', 150);
                }, 3000);
            </script>
        @endif

        <!-- Livewire Alert -->
        @if($alertMessage)
            <div class="container mt-2">
                <div class="alert alert-{{ $alertType }}" 
                    x-data="{ show: true }" 
                    x-show="show"
                    x-init="setTimeout(() => { show = false; $wire.dispatch('alert-hidden') }, 3000)">
                    <i class="fa fa-{{ $alertType == 'success' ? 'check' : 'times' }}-circle"></i> 
                    {{ $alertMessage }}
                </div>
            </div>
        @endif
        
        <!-- Search Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">

                    <!-- Search by Part Number -->
                    <div class="col-lg-4 position-relative" 
                        x-data="partNoSearch()" 
                        x-init="init()">
                        <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                            <input type="text" 
                                class="form-control" 
                                x-model="inputValue"
                                x-ref="searchInput"
                                placeholder="Enter part number" 
                                x-on:keyup.debounce.300ms="fetchSuggestions($event.target.value)"
                                x-on:focus="showDropdown = true"
                                x-on:keydown.enter="performSearch()"
                                autocomplete="off" />
                            <!-- Clear Button -->
                            <button class="btn btn-outline-secondary" type="button" 
                                x-on:click="clearInput()" 
                                x-show="inputValue.length > 0"
                                title="Clear">
                                <i class="fa fa-times"></i>
                            </button>
                            <button class="btn btn-primary" type="button" x-on:click="performSearch()">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        
                        <div x-show="showDropdown && suggestions.length > 0" 
                             x-cloak
                             class="autocomplete-dropdown">
                            <ul class="list-group">
                                <template x-for="(suggestion, index) in suggestions" :key="index">
                                    <li class="list-group-item list-group-item-action autocomplete-item"
                                        x-text="suggestion.part_no"
                                        x-on:click="selectSuggestion(suggestion)"
                                        x-on:mouseenter="highlightedIndex = index"
                                        x-bind:class="{ 'bg-primary text-white': highlightedIndex === index }"
                                        style="cursor: pointer;">
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Search by Customer Name -->
                    <div class="col-lg-4 position-relative" 
                        x-data="customerSearch()" 
                        x-init="init()">
                        <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" 
                                class="form-control" 
                                x-model="inputValue"
                                x-ref="searchInput"
                                placeholder="Enter customer name" 
                                x-on:keyup.debounce.300ms="fetchSuggestions($event.target.value)"
                                x-on:focus="showDropdown = true"
                                x-on:keydown.enter="performSearch()"
                                autocomplete="off" />
                            <!-- Clear Button -->
                            <button class="btn btn-outline-secondary" type="button" 
                                x-on:click="clearInput()" 
                                x-show="inputValue.length > 0"
                                title="Clear">
                                <i class="fa fa-times"></i>
                            </button>
                            <button class="btn btn-primary" type="button" x-on:click="performSearch()">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        
                        <div x-show="showDropdown && suggestions.length > 0" 
                             x-cloak
                             class="autocomplete-dropdown">
                            <ul class="list-group">
                                <template x-for="(suggestion, index) in suggestions" :key="index">
                                    <li class="list-group-item list-group-item-action autocomplete-item"
                                        x-text="suggestion.customer"
                                        x-on:click="selectSuggestion(suggestion)"
                                        x-on:mouseenter="highlightedIndex = index"
                                        x-bind:class="{ 'bg-primary text-white': highlightedIndex === index }"
                                        style="cursor: pointer;">
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Search by Vendor -->
                    <div class="col-md-4 position-relative" 
                        x-data="vendorSearch()" 
                        x-init="init()">
                        <label class="form-label fw-bold">
                            <i class="fa fa-search"></i> Search By Vendor Name
                        </label>
                        <div class="input-group">
                            <input type="text" 
                                class="form-control"
                                x-model="inputValue"
                                x-ref="searchInput"
                                placeholder="Enter Vendor Name"
                                x-on:keyup.debounce.300ms="fetchSuggestions($event.target.value)"
                                x-on:focus="showDropdown = true"
                                x-on:keydown.enter="performSearch()"
                                autocomplete="off" />
                            <!-- Clear Button -->
                            <button class="btn btn-outline-secondary" type="button" 
                                x-on:click="clearInput()" 
                                x-show="inputValue.length > 0"
                                title="Clear">
                                <i class="fa fa-times"></i>
                            </button>
                            <button class="btn btn-primary" x-on:click="performSearch()">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                        
                        <div x-show="showDropdown && suggestions.length > 0" 
                             x-cloak
                             class="autocomplete-dropdown">
                            <ul class="list-group">
                                <template x-for="(suggestion, index) in suggestions" :key="index">
                                    <li class="list-group-item list-group-item-action autocomplete-item"
                                        x-text="suggestion.c_shortname || suggestion.c_name"
                                        x-on:click="selectSuggestion(suggestion)"
                                        x-on:mouseenter="highlightedIndex = index"
                                        x-bind:class="{ 'bg-primary text-white': highlightedIndex === index }"
                                        style="cursor: pointer;">
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-md-12 mt-2 text-end">
                        <button class="btn btn-secondary" wire:click="resetFilters">
                            <i class="fa fa-times-circle"></i> Reset Filters
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Orders Table Card -->
        <div class="card">
            <div class="card-header">
                <b><i class="fa fa-list"></i> Manage Purchase Orders</b>
                <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
            </div>

            <div>
                <table class="table table-bordered table-sm table-striped">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-id-badge"></i> ID</th>
                            <th><i class="fa fa-hashtag"></i> PO </th>
                            <th><i class="fa fa-user-circle"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part No</th>
                            <th><i class="fa fa-refresh"></i> Rev</th>
                            <th><i class="fa fa-calendar"></i> PO Date</th>
                            <th><i class="fa fa-industry"></i> Vendor</th>
                            <th><i class="fa fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->poid }}</td>
                            <td>{{ $order->poid + 9933 }}</td>
                            <td>{{ $order->customer }}</td>
                            <td style="position: relative;">
                                @php
                                    $alerts = \App\Models\alerts_tb::where('part_no', $order->part_no)
                                        ->where('rev', $order->rev)
                                        ->where('customer', $order->customer)
                                        ->get();
                                @endphp

                                @if($alerts->count() > 0)
                                    <a href="javascript:void(0);" class="ttip_trigger text-danger">
                                        {{ $order->part_no }}
                                    </a>

                                    <div class="ttip_overlay bg-light p-3 border shadow"
                                        style="position: absolute; top: 100%; left: 0; width: 300px; display: none; z-index: 9999;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-2 text-danger">Alerts</h6>
                                            <a href="javascript:void(0);" class="ttip_close text-danger">×</a>
                                        </div>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($alerts as $index => $alert)
                                                <li>{{ $alert->alert }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    {{ $order->part_no }}
                                @endif
                            </td>

                            <td>{{ $order->rev }}</td>
                            <td>{{ $order->podate }}</td>
                            <td>{{ $order->vendor->c_shortname ?? 'N/A' }}</td>
                            <td>
                                <!-- <a href="{{ route('purchase.orders.view',$order->poid) }}" class="btn btn-info btn-xs">
                                    <i class="fa fa-eye"></i> View
                                </a> -->
                                <a href="{{ route('purchase.orders.edit',$order->poid) }}"
                                    class="btn btn-sm btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>

                                <a href="https://files.pcbsglobal.website/download-pdf1.php?id={{ $order->poid }}&oper=download"
                                    class="btn btn-xs btn-sm btn-success">
                                    <i class="fa fa-download"></i> PDF
                                </a>

                                <a href="https://files.pcbsglobal.website/download-pdf1.php?id={{ $order->poid }}&oper=view" target="_blank"
                                    class="btn btn-sm btn-xs btn-info">
                                    <i class="fa fa-eye"></i> View PDF
                                </a>

                                <a href="https://files.pcbsglobal.website/download-doc1.php?id={{ $order->poid }}"
                                    class="btn btn-sm btn-xs btn-secondary">
                                    <i class="fa fa-file-text"></i> DOC
                                </a>

                                <button wire:click="delete({{ $order->poid }})" wire:key="delete-{{ $order->poid }}" class="btn btn-xs btn-sm btn-danger" wire:confirm="Are you sure, you want to delete this purchase order?">
                                    <i class="fa fa-trash"></i> Delete
                                </button>

                                <button wire:click="duplicate({{ $order->poid }})" wire:key="duplicate-{{ $order->poid }}" class="btn btn-xs btn-sm btn-warning">
                                    <i class="fa fa-copy"></i> Duplicate
                                </button>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No Purchase Orders Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
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
                font-size: 0.875rem;
            }
            
            .autocomplete-item {
                padding: 6px 10px;
                cursor: pointer;
                border-bottom: 1px solid #f0f0f0;
                font-size: 0.875rem;
                line-height: 1.3;
            }
            
            .autocomplete-item:hover {
                background-color: #007bff;
                color: white;
            }
            
            .autocomplete-item:last-child {
                border-bottom: none;
            }
            
            [x-cloak] {
                display: none !important;
            }
            
            .btn-outline-secondary {
                border-color: #dee2e6;
            }
        </style>

        <script>
            // Global variables to store Alpine components
            let partNoSearchComponent = null;
            let customerSearchComponent = null;
            let vendorSearchComponent = null;

            // Part Number Search Component
            function partNoSearch() {
                return {
                    inputValue: '',
                    suggestions: [],
                    showDropdown: false,
                    highlightedIndex: -1,
                    
                    init() {
                        partNoSearchComponent = this;
                        document.addEventListener('click', (e) => {
                            if (!this.$el.contains(e.target)) {
                                this.showDropdown = false;
                            }
                        });
                        
                        // Listen for Livewire reset event
                        Livewire.on('resetFiltersCompleted', () => {
                            this.clearInput();
                        });
                    },
                    
                    async fetchSuggestions(query) {
                        if (query.length < 2) {
                            this.suggestions = [];
                            this.showDropdown = false;
                            return;
                        }
                        
                        try {
                            const response = await fetch(`/api/purchase/partno-suggestions?q=${encodeURIComponent(query)}`);
                            const data = await response.json();
                            this.suggestions = data;
                            this.showDropdown = data.length > 0;
                        } catch (error) {
                            console.error('Error fetching part number suggestions:', error);
                            this.suggestions = [];
                        }
                    },
                    
                    selectSuggestion(suggestion) {
                        this.inputValue = suggestion.part_no;
                        this.showDropdown = false;
                        this.suggestions = [];
                        this.performSearch();
                    },
                    
                    performSearch() {
                        if (this.inputValue.trim()) {
                            @this.set('searchPart', this.inputValue.trim());
                            this.showDropdown = false;
                            // Clear input immediately
                            this.inputValue = '';
                            // Remove focus from input after search
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.blur();
                            }
                        }
                    },
                    
                    clearInput() {
                        this.inputValue = '';
                        this.suggestions = [];
                        this.showDropdown = false;
                        this.highlightedIndex = -1;
                        // Also clear Livewire property if needed
                        @this.set('searchPart', '');
                        // Remove focus from input
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.blur();
                        }
                    }
                }
            }

            // Customer Search Component
            function customerSearch() {
                return {
                    inputValue: '',
                    suggestions: [],
                    showDropdown: false,
                    highlightedIndex: -1,
                    
                    init() {
                        customerSearchComponent = this;
                        document.addEventListener('click', (e) => {
                            if (!this.$el.contains(e.target)) {
                                this.showDropdown = false;
                            }
                        });
                        
                        // Listen for Livewire reset event
                        Livewire.on('resetFiltersCompleted', () => {
                            this.clearInput();
                        });
                    },
                    
                    async fetchSuggestions(query) {
                        if (query.length < 2) {
                            this.suggestions = [];
                            this.showDropdown = false;
                            return;
                        }
                        
                        try {
                            const response = await fetch(`/api/purchase/customer-suggestions?q=${encodeURIComponent(query)}`);
                            const data = await response.json();
                            this.suggestions = data;
                            this.showDropdown = data.length > 0;
                        } catch (error) {
                            console.error('Error fetching customer suggestions:', error);
                            this.suggestions = [];
                        }
                    },
                    
                    selectSuggestion(suggestion) {
                        this.inputValue = suggestion.customer;
                        this.showDropdown = false;
                        this.suggestions = [];
                        this.performSearch();
                    },
                    
                    performSearch() {
                        if (this.inputValue.trim()) {
                            @this.set('searchCustomer', this.inputValue.trim());
                            this.showDropdown = false;
                            // Clear input immediately
                            this.inputValue = '';
                            // Remove focus from input after search
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.blur();
                            }
                        }
                    },
                    
                    clearInput() {
                        this.inputValue = '';
                        this.suggestions = [];
                        this.showDropdown = false;
                        this.highlightedIndex = -1;
                        // Also clear Livewire property if needed
                        @this.set('searchCustomer', '');
                        // Remove focus from input
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.blur();
                        }
                    }
                }
            }

            // Vendor Search Component
            function vendorSearch() {
                return {
                    inputValue: '',
                    suggestions: [],
                    showDropdown: false,
                    highlightedIndex: -1,
                    
                    init() {
                        vendorSearchComponent = this;
                        document.addEventListener('click', (e) => {
                            if (!this.$el.contains(e.target)) {
                                this.showDropdown = false;
                            }
                        });
                        
                        // Listen for Livewire reset event
                        Livewire.on('resetFiltersCompleted', () => {
                            this.clearInput();
                        });
                    },
                    
                    async fetchSuggestions(query) {
                        if (query.length < 2) {
                            this.suggestions = [];
                            this.showDropdown = false;
                            return;
                        }
                        
                        try {
                            const response = await fetch(`/api/purchase/vendor-suggestions?q=${encodeURIComponent(query)}`);
                            const data = await response.json();
                            this.suggestions = data;
                            this.showDropdown = data.length > 0;
                        } catch (error) {
                            console.error('Error fetching vendor suggestions:', error);
                            this.suggestions = [];
                        }
                    },
                    
                    selectSuggestion(suggestion) {
                        this.inputValue = suggestion.c_shortname || suggestion.c_name;
                        this.showDropdown = false;
                        this.suggestions = [];
                        this.performSearch();
                    },
                    
                    performSearch() {
                        if (this.inputValue.trim()) {
                            @this.set('searchVendor', this.inputValue.trim());
                            this.showDropdown = false;
                            // Clear input immediately
                            this.inputValue = '';
                            // Remove focus from input after search
                            if (this.$refs.searchInput) {
                                this.$refs.searchInput.blur();
                            }
                        }
                    },
                    
                    clearInput() {
                        this.inputValue = '';
                        this.suggestions = [];
                        this.showDropdown = false;
                        this.highlightedIndex = -1;
                        // Also clear Livewire property if needed
                        @this.set('searchVendor', '');
                        // Remove focus from input
                        if (this.$refs.searchInput) {
                            this.$refs.searchInput.blur();
                        }
                    }
                }
            }

            // Initialize Livewire listener
            document.addEventListener('livewire:init', () => {
                // This event is dispatched from the Livewire component when reset is clicked
                Livewire.on('resetFiltersCompleted', () => {
                    // Reset all Alpine.js components
                    if (partNoSearchComponent) partNoSearchComponent.clearInput();
                    if (customerSearchComponent) customerSearchComponent.clearInput();
                    if (vendorSearchComponent) vendorSearchComponent.clearInput();
                });
            });

            // Tooltip functionality
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.ttip_trigger').forEach(function (trigger) {
                    const tooltip = trigger.nextElementSibling;
                    const closeBtn = tooltip.querySelector('.ttip_close');

                    trigger.addEventListener('click', function (e) {
                        e.stopPropagation();
                        document.querySelectorAll('.ttip_overlay').forEach(t => t.style.display = 'none');
                        tooltip.style.display = 'block';
                    });

                    closeBtn.addEventListener('click', function () {
                        tooltip.style.display = 'none';
                    });

                    document.addEventListener('click', function (e) {
                        if (!tooltip.contains(e.target) && e.target !== trigger) {
                            tooltip.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    </div>
</div>