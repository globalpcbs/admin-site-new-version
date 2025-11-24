<div>
    <!-- Alert System -->
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
            <div class="row g-3 align-items-end" 
                 wire:loading.class="opacity-50"
                 x-data="{
                    init() {
                        // Client-side debouncing
                        let customerTimer, partTimer, vendorTimer;
                        
                        // Customer search debounce
                        Livewire.on('customer-search', (value) => {
                            clearTimeout(customerTimer);
                            customerTimer = setTimeout(() => {
                                @this.onKeyUp(value);
                            }, 350);
                        });
                        
                        // Part number search debounce  
                        Livewire.on('part-search', (value) => {
                            clearTimeout(partTimer);
                            partTimer = setTimeout(() => {
                                @this.usekeyupno(value);
                            }, 350);
                        });
                        
                        // Vendor search debounce
                        Livewire.on('vendor-search', (value) => {
                            clearTimeout(vendorTimer);
                            vendorTimer = setTimeout(() => {
                                @this.usekeyupvendor(value);
                            }, 350);
                        });
                    }
                 }">

                <!-- Search by Part Number -->
                <div class="col-lg-4" x-data="{ open: false }" @click.away="open = false">
                    <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        <input type="text" class="form-control" 
                            wire:model="searchPartNoInput"
                            x-on:input="open = true; $dispatch('part-search', $event.target.value)"
                            wire:keydown.enter="searchq" 
                            placeholder="Enter part number">
                        <button class="btn btn-primary" type="button" wire:click="searchq" 
                                wire:loading.attr="disabled" :disabled="$wire.isLoading">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div x-show="open && $wire.matches_partno.length > 0" x-cloak>
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            <template x-for="(match, index) in $wire.matches_partno" :key="index">
                                <li class="list-group-item list-group-item-action"
                                    x-text="match.part_no"
                                    @click="$wire.useMatchpn(index); open = false;">
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Search by Customer Name -->
                <div class="col-lg-4" x-data="{ open: false }" @click.away="open = false">
                    <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" 
                            wire:model="searchCustomerInput"
                            x-on:input="open = true; $dispatch('customer-search', $event.target.value)"
                            wire:keydown.enter="searchbyCustomer" 
                            placeholder="Enter customer name">
                        <button class="btn btn-primary" type="button" wire:click="searchbyCustomer" 
                                wire:loading.attr="disabled" :disabled="$wire.isLoading">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div x-show="open && $wire.matches.length > 0" x-cloak>
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            <template x-for="(match, index) in $wire.matches" :key="index">
                                <li class="list-group-item list-group-item-action"
                                    x-text="match.customer"
                                    @click="$wire.useMatch(index); open = false;">
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Search by Vendor -->
                <div class="col-md-4" x-data="{ open: false }" @click.away="open = false">
                    <label class="form-label fw-bold">
                        <i class="fa fa-search"></i> Search By Vendor Name
                    </label>
                    <div class="input-group">
                        <input type="text" wire:model="searchVendorInput" 
                            x-on:input="open = true; $dispatch('vendor-search', $event.target.value)"
                            wire:keydown.enter="searchv" 
                            placeholder="Enter Vendor Name"
                            class="form-control">
                        <button class="btn btn-primary" wire:click="searchv" 
                                wire:loading.attr="disabled" :disabled="$wire.isLoading">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                    <div x-show="open && $wire.matches_vendor.length > 0" x-cloak>
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            <template x-for="(vendor, index) in $wire.matches_vendor" :key="vendor.data_id">
                                <li class="list-group-item list-group-item-action"
                                    x-text="vendor.c_shortname || vendor.c_name"
                                    @click="$wire.useMatchve(index); open = false;">
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Reset Button -->
                <div class="col-md-12 mt-2 text-end">
                    <button class="btn btn-secondary" wire:click="resetFilters" 
                            wire:loading.attr="disabled" :disabled="$wire.isLoading">
                        <i class="fa fa-times-circle"></i> Reset Filters
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading.flex class="justify-content-center my-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span class="ms-2">Processing...</span>
    </div>

    <!-- Orders Table -->
    <div class="card" wire:loading.remove>
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fa fa-list"></i> Manage Purchase Orders
            </div>
            <div>
                <span class="badge bg-primary" x-text="$wire.orders?.data?.length || 0"></span> orders
                <i class="fa fa-spinner fa-spin ms-2" wire:loading></i>
            </div>
        </div>

        <div>
            @php
                // Preload alerts for all displayed orders to avoid N+1 queries
                $orderIds = $orders->pluck('poid');
                $alertsMap = \App\Models\alerts_tb::whereIn('poid', $orderIds)
                    ->get()
                    ->groupBy('poid');
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped table-hover">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="80"><i class="fa fa-id-badge"></i> ID</th>
                            <th width="100"><i class="fa fa-hashtag"></i> PO</th>
                            <th><i class="fa fa-user-circle"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part No</th>
                            <th width="80"><i class="fa fa-refresh"></i> Rev</th>
                            <th width="120"><i class="fa fa-calendar"></i> PO Date</th>
                            <th><i class="fa fa-industry"></i> Vendor</th>
                            <th width="300"><i class="fa fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr wire:key="order-{{ $order->poid }}">
                            <td class="fw-bold">{{ $order->poid }}</td>
                            <td class="fw-bold text-primary">{{ $order->poid + 9933 }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($order->customer, 25) }}</td>
                            <td style="position: relative;">
                                @php
                                    $alerts = $alertsMap[$order->poid] ?? collect();
                                @endphp

                                @if($alerts->count() > 0)
                                    <a href="javascript:void(0);" class="ttip_trigger text-danger" 
                                       title="{{ $alerts->count() }} alert(s)">
                                        {{ \Illuminate\Support\Str::limit($order->part_no, 20) }}
                                        <span class="badge bg-danger ms-1">{{ $alerts->count() }}</span>
                                    </a>
                                @else
                                    {{ \Illuminate\Support\Str::limit($order->part_no, 20) }}
                                @endif
                            </td>
                            <td>{{ $order->rev }}</td>
                            <td>{{ $order->podate }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($order->vendor->c_shortname ?? 'N/A', 20) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <a href="{{ route('purchase.orders.edit',$order->poid) }}"
                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a href="https://files.pcbsglobal.website/download-pdf1.php?id={{ $order->poid }}&oper=download"
                                        class="btn btn-sm btn-outline-success" title="Download PDF">
                                        <i class="fa fa-download"></i>
                                    </a>

                                    <a href="https://files.pcbsglobal.website/download-pdf1.php?id={{ $order->poid }}&oper=view" target="_blank"
                                        class="btn btn-sm btn-outline-info" title="View PDF">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <a href="https://files.pcbsglobal.website/download-doc1.php?id={{ $order->poid }}"
                                        class="btn btn-sm btn-outline-secondary" title="Download DOC">
                                        <i class="fa fa-file-text"></i>
                                    </a>

                                    <button wire:click="duplicate({{ $order->poid }})" 
                                        class="btn btn-sm btn-outline-warning" 
                                        wire:loading.attr="disabled" 
                                        title="Duplicate">
                                        <i class="fa fa-copy"></i>
                                    </button>

                                    <button wire:click="delete({{ $order->poid }})" 
                                        class="btn btn-sm btn-outline-danger"
                                        wire:loading.attr="disabled" 
                                        title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                No Purchase Orders Found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
            <div class="pagination-wrapper px-3 py-2 border-top">
                {{ $orders->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Tooltip Overlay (Hidden by default) -->
    <div id="tooltipOverlay" class="ttip_overlay bg-light p-3 border shadow"
         style="position: fixed; display: none; z-index: 9999; max-width: 350px;">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0 text-danger">Alerts</h6>
            <button type="button" class="btn-close" onclick="hideTooltip()"></button>
        </div>
        <div id="tooltipContent"></div>
    </div>

    <!-- JavaScript -->
    <script>
        // Enhanced tooltip functionality
        let currentTooltipTrigger = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Event delegation for tooltips
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('ttip_trigger')) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const orderId = e.target.closest('tr').querySelector('td:first-child').textContent.trim();
                    showAlertsTooltip(e.target, orderId);
                }
            });

            // Hide tooltip when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.classList.contains('ttip_trigger') && 
                    !e.target.closest('.ttip_overlay')) {
                    hideTooltip();
                }
            });

            // Hide tooltip on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    hideTooltip();
                }
            });
        });

        function showAlertsTooltip(trigger, orderId) {
            // Hide any existing tooltip
            hideTooltip();
            
            // Show loading state
            const tooltip = document.getElementById('tooltipOverlay');
            const content = document.getElementById('tooltipContent');
            content.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading alerts...</div>';
            
            // Position tooltip near the trigger
            const rect = trigger.getBoundingClientRect();
            tooltip.style.left = (rect.left + window.scrollX) + 'px';
            tooltip.style.top = (rect.bottom + window.scrollY + 5) + 'px';
            tooltip.style.display = 'block';
            
            currentTooltipTrigger = trigger;

            // Fetch alerts via AJAX (optional - you can preload them)
            setTimeout(() => {
                // Simulate loading - in real implementation, fetch from server
                content.innerHTML = '<div class="alert alert-warning">Alerts would load here via AJAX</div>';
            }, 300);
        }

        function hideTooltip() {
            const tooltip = document.getElementById('tooltipOverlay');
            tooltip.style.display = 'none';
            currentTooltipTrigger = null;
        }

        // Global confirmation
        function confirm(message) {
            return window.confirm(message);
        }

        // Livewire event listeners for better UX
        document.addEventListener('livewire:init', () => {
            Livewire.on('action-completed', () => {
                // Hide loading states, show notifications, etc.
                hideTooltip();
            });
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .table thead.sticky-top th {
            background: #f8f9fa;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .opacity-50 {
            opacity: 0.5;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .ttip_trigger {
            cursor: pointer;
            text-decoration: none;
            border-bottom: 1px dashed #dc3545;
        }
        
        .ttip_trigger:hover {
            text-decoration: none;
            opacity: 0.8;
        }
        
        .pagination-wrapper .pagination {
            margin-bottom: 0;
        }
    </style>
</div>