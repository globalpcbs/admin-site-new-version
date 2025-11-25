<div>
    <!-- Success Alert from Session -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" id="successAlert">
            <i class="fa fa-check-square"></i> {{ session('success') }}
        </div>
        
        <script>
            setTimeout(() => {
                const alert = document.getElementById('successAlert');
                alert.classList.remove('show');
                setTimeout(() => alert.style.display = 'none', 150);
            }, 3000);
        </script>
    @endif

    <!-- Livewire Alert Component -->
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

    <!-- Search Filters Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <!-- Search by Part Number -->
                <div class="col-lg-4">
                    <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        <input type="text" class="form-control"
                            placeholder="Enter part number" 
                            wire:model="searchPartNoInput"
                            wire:keydown.enter="searchq" 
                            wire:keyup="usekeyupno($event.target.value)" />
                        <button class="btn btn-primary" type="button" wire:click="searchq">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <!-- Part Number Dropdown Suggestions -->
                    @if(!empty($matches_partno) && count($matches_partno) > 0)
                        <ul class="list-group position-absolute shadow-sm mt-1"
                            style="z-index:1050; max-height:220px; overflow-y:auto; width: calc(100% - 30px);">
                            @foreach($matches_partno as $i => $m)
                                <li class="list-group-item list-group-item-action cursor-pointer"
                                    wire:click="useMatchpn({{ $i }})">
                                    {{ $m['part_no'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Search by Customer Name -->
                <div class="col-lg-4">
                    <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" 
                            wire:model="searchCustomerInput"
                            placeholder="Enter customer name" 
                            wire:keydown.enter="searchbyCustomer" 
                            wire:keyup="onKeyUp($event.target.value)">
                        <button class="btn btn-primary" type="button" wire:click="searchbyCustomer">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <!-- Customer Name Dropdown Suggestions -->
                    @if(!empty($matches) && count($matches) > 0)
                        <ul class="list-group position-absolute shadow-sm mt-1"
                            style="z-index:1050; max-height:220px; overflow-y:auto; width: calc(100% - 30px);">
                            @foreach($matches as $i => $m)
                                <li class="list-group-item list-group-item-action cursor-pointer"
                                    wire:click="useMatch({{ $i }})">
                                    {{ $m['customer'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Reset Button -->
                <div class="col-lg-4 text-end">
                    <button class="btn btn-secondary" wire:click="resetFilters">
                        <i class="fa fa-times-circle"></i> Reset Filters
                    </button>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if(!empty($partSearchInput) || !empty($customerSearchInput))
                <div class="row mt-3">
                    <div class="col-12">
                        <small class="text-muted">
                            <strong>Active Filters:</strong>
                            @if(!empty($partSearchInput))
                                <span class="badge bg-primary">Part No: {{ $partSearchInput }}</span>
                            @endif
                            @if(!empty($customerSearchInput))
                                <span class="badge bg-info">Customer: {{ $customerSearchInput }}</span>
                            @endif
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fa fa-list"></i> Confirmation Orders 
                <span class="badge bg-secondary">{{ $orders->total() }}</span>
            </div>
            <div>
                <i class="fa fa-spinner fa-spin text-danger" wire:loading></i>
                <small class="text-muted" wire:loading>Loading...</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped table-hover m-0">
                    <thead class="table-light">
                        <tr>
                            <th>PO ID</th>
                            <th>Order Conf#</th>
                            <th>Customer</th>
                            <th>Part No</th>
                            <th>Rev</th>
                            <th>CO Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr wire:key="order-{{ $order->poid }}">
                            <td class="fw-bold">{{ $order->poid }}</td>
                            <td>{{ $order->conf_no ?? 'N/A' }}</td>
                            <td>{{ $order->customer }}</td>
                            <td>
                                <span class="badge bg-dark">{{ $order->part_no }}</span>
                            </td>
                            <td>
                                @if($order->rev)
                                    <span class="badge bg-warning text-dark">Rev {{ $order->rev }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $order->podate }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Download PDF -->
                                    <a href="https://files.pcbsglobal.website/download-pdf4.php?id={{ $order->poid }}&oper=download" 
                                       class="btn btn-outline-secondary" 
                                       title="Download PDF">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    
                                    <!-- View PDF -->
                                    <a href="https://files.pcbsglobal.website/download-pdf4.php?id={{ $order->poid }}&oper=view" 
                                       target="_blank" 
                                       class="btn btn-outline-success"
                                       title="View PDF">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    
                                    <!-- Download Word -->
                                    <a href="https://files.pcbsglobal.website/download-doc4.php?id={{ $order->poid }}"
                                       class="btn btn-outline-danger"
                                       title="Download Word Document">
                                        <i class="fa fa-file-word"></i>
                                    </a>
                                    
                                    <!-- Edit -->
                                    <a href="{{ route('confirmation.edit', $order->poid )}}"
                                       class="btn btn-outline-primary"
                                       title="Edit Order">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    
                                    <!-- Delete -->
                                    <button class="btn btn-outline-danger" 
                                            wire:click="delete({{ $order->poid }})"
                                            wire:confirm="Are you sure you want to delete this order?"
                                            wire:loading.attr="disabled"
                                            title="Delete Order">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    
                                    <!-- Duplicate -->
                                    <button class="btn btn-outline-info" 
                                            wire:click="duplicate({{ $order->poid }})"
                                            wire:loading.attr="disabled"
                                            title="Duplicate Order">
                                        <i class="fa fa-clone"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa fa-inbox fa-2x mb-2"></i><br>
                                No confirmation orders found.
                                @if(!empty($partSearchInput) || !empty($customerSearchInput))
                                    <div class="mt-2">
                                        <small>Try adjusting your search filters</small>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer with Pagination Info -->
        @if($orders->hasPages() || $orders->total() > 0)
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">
                    Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} entries
                </small>
            </div>
            <div>
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div wire:loading.class="d-block" class="d-none">
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.1); z-index: 9999;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

/* Ensure dropdowns appear above other content */
.position-absolute {
    position: absolute !important;
}

/* Loading states */
[wire\:loading] {
    opacity: 0.7;
    pointer-events: none;
}
</style>

<script>
// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    // Close part number dropdown if click is outside
    if (!e.target.closest('.col-lg-4:first-child')) {
        Livewire.dispatch('close-partno-dropdown');
    }
    
    // Close customer dropdown if click is outside  
    if (!e.target.closest('.col-lg-4:nth-child(2)')) {
        Livewire.dispatch('close-customer-dropdown');
    }
});

// Add keyboard navigation for dropdowns
document.addEventListener('keydown', function(e) {
    const activeElement = document.activeElement;
    
    // Navigate dropdown with arrow keys when input is focused
    if (activeElement.type === 'text' && (e.key === 'ArrowDown' || e.key === 'ArrowUp')) {
        e.preventDefault();
        const dropdownItems = activeElement.closest('.col-lg-4').querySelectorAll('.list-group-item');
        if (dropdownItems.length > 0) {
            // Simple keyboard navigation implementation
            const currentIndex = Array.from(dropdownItems).findIndex(item => item === document.activeElement);
            let nextIndex = 0;
            
            if (e.key === 'ArrowDown') {
                nextIndex = currentIndex < dropdownItems.length - 1 ? currentIndex + 1 : 0;
            } else if (e.key === 'ArrowUp') {
                nextIndex = currentIndex > 0 ? currentIndex - 1 : dropdownItems.length - 1;
            }
            
            if (dropdownItems[nextIndex]) {
                dropdownItems[nextIndex].focus();
            }
        }
    }
    
    // Close dropdown on Escape key
    if (e.key === 'Escape') {
        Livewire.dispatch('close-all-dropdowns');
    }
});
</script>