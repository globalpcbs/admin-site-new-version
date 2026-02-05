<div>
    <div class="container-fluid px-4 py-4">
        @include('includes.flash')

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 0.5rem;">
            <div class="card-header bg-white py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Reports</h6>
                <div wire:loading>
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="card-body bg-light" style="border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
                <form wire:submit.prevent="search">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="small fw-bold text-muted text-uppercase mb-1">From Date</label>
                            <div class="input-group input-group-sm bg-white rounded shadow-sm">
                                <span class="input-group-text bg-transparent border-0"><i class="far fa-calendar-alt text-gray-400"></i></span>
                                <input type="date" class="form-control border-0 bg-transparent" wire:model.live="from" id="from" wire:key="from-{{ $refreshKey }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                             <label class="small fw-bold text-muted text-uppercase mb-1">To Date</label>
                            <div class="input-group input-group-sm bg-white rounded shadow-sm">
                                <span class="input-group-text bg-transparent border-0"><i class="far fa-calendar-alt text-gray-400"></i></span>
                                <input type="date" class="form-control border-0 bg-transparent" wire:model.live="to" id="to" wire:key="to-{{ $refreshKey }}">
                            </div>
                        </div>
                        
                        <div class="col-md-2 position-relative">
                            <label class="small fw-bold text-muted text-uppercase mb-1">Part Number</label>
                             <div class="input-group input-group-sm bg-white rounded shadow-sm">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-cube text-gray-400"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent" 
                                       wire:model.live="partNumber"
                                       id="partNumber"
                                       wire:key="partNumber-{{ $refreshKey }}"
                                       placeholder="Search..." autocomplete="off">
                            </div>
                            @if(count($partNumberSuggestions) > 0)
                                <div class="autocomplete-dropdown shadow-sm border-0 mt-1">
                                    @foreach($partNumberSuggestions as $suggestion)
                                        <div class="autocomplete-item py-2 px-3 border-bottom" wire:click="$set('partNumber', '{{ $suggestion }}')">{{ $suggestion }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="col-md-3 position-relative">
                            <label class="small fw-bold text-muted text-uppercase mb-1">Customer</label>
                            <div class="input-group input-group-sm bg-white rounded shadow-sm">
                                <span class="input-group-text bg-transparent border-0"><i class="far fa-user text-gray-400"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent" 
                                       wire:model.live="customerName"
                                       id="customerName"
                                       wire:key="customerName-{{ $refreshKey }}"
                                       placeholder="Search Customer..." autocomplete="off">
                            </div>
                            @if(count($customerNameSuggestions) > 0)
                                <div class="autocomplete-dropdown shadow-sm border-0 mt-1">
                                    @foreach($customerNameSuggestions as $suggestion)
                                        <div class="autocomplete-item py-2 px-3 border-bottom" wire:click="$set('customerName', '{{ $suggestion }}')">{{ $suggestion }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="col-md-3 position-relative">
                            <label class="small fw-bold text-muted text-uppercase mb-1">Vendor</label>
                             <div class="input-group input-group-sm bg-white rounded shadow-sm">
                                <span class="input-group-text bg-transparent border-0"><i class="fas fa-industry text-gray-400"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent" 
                                       wire:model.live="vendorName"
                                       id="vendorName"
                                       wire:key="vendorName-{{ $refreshKey }}"
                                       placeholder="Search Vendor..." autocomplete="off">
                            </div>
                            @if(count($vendorNameSuggestions) > 0)
                                <div class="autocomplete-dropdown shadow-sm border-0 mt-1">
                                    @foreach($vendorNameSuggestions as $suggestion)
                                        <div class="autocomplete-item py-2 px-3 border-bottom" wire:click="$set('vendorName', '{{ $suggestion }}')">{{ $suggestion }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="button" wire:click="resetFilters" class="btn btn-sm btn-light text-secondary me-2 border shadow-sm"><i class="fas fa-undo me-1"></i> Reset</button>
                        <button type="submit" class="btn btn-sm btn-primary shadow-sm px-4"><i class="fas fa-search me-1"></i> Search Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Card -->
        <div class="card shadow-sm border-0" style="border-radius: 0.5rem;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-table me-2 text-primary"></i>Status Report Data</h6>
                 @if($orders->count() > 0)
                    <span class="badge bg-light text-secondary border fw-normal">Showing {{ $orders->firstItem() }}-{{ $orders->lastItem() }} of {{ $orders->total() }}</span>
                @endif
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm table-bordered table-stripped mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="py-3 ps-3 border-bottom-0">POID</th>
                                <th class="py-3 border-bottom-0">Customer</th>
                                <th class="py-3 border-bottom-0">Part No.</th>
                                <th class="py-3 text-center border-bottom-0">Rev</th>
                                <th class="py-3 border-bottom-0">Due Date</th>
                                <th class="py-3 border-bottom-0">Cust PO</th>
                                <th class="py-3 border-bottom-0">Our PO</th>
                                <th class="py-3 border-bottom-0">Vendor</th>
                                <th class="py-3 text-center border-bottom-0">WT</th>
                                <th class="py-3 border-bottom-0">Inv #</th>
                                <th class="py-3 border-bottom-0">Inv Date</th>
                                <th class="py-3 text-center border-bottom-0">Credit</th>
                                <th class="py-3 border-bottom-0">Sup Due</th>
                                <th class="py-3 border-bottom-0">Cus Due</th>
                                <th class="py-3 text-end pe-3 border-bottom-0">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr wire:key="row-{{ $order->poid }}">
                                <td class="ps-3 align-middle fw-bold text-primary">{{ $order->poid }}</td>
                                
                                <td class="align-middle position-relative">
                                    @php $customer = \App\Models\data_tb::where('c_name', $order->customer)->first(); @endphp
                                    @if($customer)
                                        <span class="ttip_trigger fw-medium text-dark" style="cursor: pointer; border-bottom: 1px dotted #ccc;">
                                            {{ Str::limit($customer->c_shortname, 15) }}
                                        </span>
                                        <div class="ttip_overlay shadow-lg rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start border-bottom pb-2 mb-2">
                                                <h6 class="mb-0 fw-bold text-primary">{{ $customer->c_name }}</h6>
                                                <button type="button" class="btn-close btn-xs ttip_close" aria-label="Close"></button>
                                            </div>
                                            <div class="small text-muted">
                                                @if($customer->c_address) <div><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $customer->c_address }}</div> @endif
                                                @if($customer->c_phone) <div><i class="fas fa-phone me-2 text-primary"></i>{{ $customer->c_phone }}</div> @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="align-middle fw-bold">
                                    <a href="javascript:void(0);" wire:click="openModal({{ $order->poid }})" class="text-dark text-decoration-none hover-primary">
                                        {{ $order->part_no }}
                                    </a>
                                </td>
                                <td class="align-middle text-center">{{ $order->rev }}</td>
                                
                                <td class="align-middle position-relative">
                                     <span class="ttip_trigger text-nowrap" style="cursor: help;">
                                        {{ $order->dweek }}
                                        @if($order->note) <i class="fas fa-info-circle text-warning ms-1"></i> @endif
                                    </span>
                                    <div class="ttip_overlay shadow-lg rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-1">
                                            <span class="fw-bold text-dark small text-uppercase">Order Notes</span>
                                            <button type="button" class="btn-close btn-xs ttip_close"></button>
                                        </div>
                                        <p class="mb-0 small text-secondary bg-light p-2 rounded">{{ $order->note ?: 'No notes available.' }}</p>
                                    </div>
                                </td>

                                <td class="align-middle">{{ $order->po }}</td>
                                <td class="align-middle">
                                    <a href="{{ route('purchase.orders.edit',$order->poid) }}" target="_blank" class="badge bg-white text-primary border fw-normal text-decoration-none">
                                        {{ $order->poid+9933 }}
                                    </a>
                                </td>
                                <td class="align-middle text-truncate" style="max-width: 120px;">{{ $order->vc }}</td>
                                <td class="align-middle text-center">
                                     <input class="form-check-input" type="checkbox" 
                                           wire:click="toggleOrder('{{ $order->poid }}', $event.target.checked)"
                                           @checked($order->allow === 'true')>
                                </td>

                                <td class="align-middle">
                                    @if($order->invoice_id)
                                        <a href="{{ route('invoice.edit',['id' => $order->invoice_id]) }}" target="_blank" class="text-dark text-decoration-none fw-bold">{{ $order->invoice_id + 9976 }}</a>
                                    @else - @endif
                                </td>
                                <td class="align-middle">{{ $order->invoicedon ? date('m/d/Y', strtotime($order->invoicedon)) : '-' }}</td>

                                <td class="align-middle text-center">
                                    @php $credit = \App\Models\credit_tb::where('inv_id', $order->invoice_id + 9976)->first(); @endphp
                                    @if ($credit)
                                         <div class="btn-group btn-group-sm">
                                            <a href="{{ route('credit.pdf',$credit->credit_id) }}" target="_blank" class="btn btn-light btn-xs border text-secondary"><i class="far fa-file-pdf"></i></a>
                                            <a href="{{ route('credit.edit',$credit->credit_id) }}" target="_blank" class="btn btn-light btn-xs border text-secondary"><i class="fas fa-edit"></i></a>
                                        </div>
                                    @else - @endif
                                </td>

                                <td class="align-middle">{{ $order->supli_due }}</td>
                                <td class="align-middle">{{ $order->cus_due }}</td>

                                <td class="align-middle text-end pe-3">
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2 small" wire:click="openNoteModal({{ $order->poid }})">
                                        <i class="far fa-sticky-note me-1"></i> Note
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="text-center py-5 text-muted bg-light">
                                    <div class="py-3">
                                        <i class="fas fa-search fa-2x mb-2 text-gray-300"></i>
                                        <p class="mb-0">No records found matching your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
             @if($orders->count() > 0)
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                     <div class="small text-muted">Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }}</div>
                    <div>{{ $orders->links() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold">Update Dates</h6>
                    <button type="button" class="btn-close btn-close-white btn-sm" wire:click="closeModal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-2">
                         <label class="form-label small fw-bold text-secondary mb-1">Customer Due</label>
                         <input type="date" class="form-control form-control-sm" wire:model.defer="cus_due">
                    </div>
                    <div class="mb-3">
                         <label class="form-label small fw-bold text-secondary mb-1">Supplier Due</label>
                         <input type="date" class="form-control form-control-sm" wire:model.defer="sup_due">
                    </div>
                    <div class="text-end">
                        <button class="btn btn-white btn-sm border me-1" wire:click="closeModal">Cancel</button>
                        <button class="btn btn-primary btn-sm" wire:click="updateDueDates">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showNoteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning py-2">
                    <h6 class="modal-title fw-bold text-dark">Edit Note</h6>
                    <button type="button" class="btn-close btn-sm" wire:click="$set('showNoteModal', false)"></button>
                </div>
                <div class="modal-body p-3">
                    <textarea class="form-control form-control-sm mb-3" rows="4" wire:model.defer="note" placeholder="Enter note..."></textarea>
                    <div class="text-end">
                        <button class="btn btn-warning btn-sm text-dark w-100 shadow-sm" wire:click="saveNote">Save Note</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .hover-primary:hover { color: #4e73df !important; }
        .ttip_overlay {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 280px;
            z-index: 1050;
            background: #fff;
            margin-top: 0.5rem;
            text-align: left;
        }
        
        .autocomplete-dropdown {
            background-color: #fff;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            width: 100%;
            z-index: 1000;
        }
        .autocomplete-item {
            cursor: pointer;
            font-size: 0.85rem;
        }
        .autocomplete-item:hover {
            background-color: #f8f9fc;
            color: #4e73df;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
             document.addEventListener('click', function(e) {
                // Tooltip Logic
                const trigger = e.target.closest('.ttip_trigger');
                if (trigger) {
                    // Close all others
                    document.querySelectorAll('.ttip_overlay').forEach(el => el.style.display = 'none');
                    const overlay = trigger.nextElementSibling;
                    if(overlay && overlay.classList.contains('ttip_overlay')) {
                         overlay.style.display = 'block';
                    }
                } else {
                    if(!e.target.closest('.ttip_overlay')) {
                        document.querySelectorAll('.ttip_overlay').forEach(el => el.style.display = 'none');
                    }
                    if(e.target.closest('.ttip_close')) {
                         const overlay = e.target.closest('.ttip_overlay');
                         if(overlay) overlay.style.display = 'none';
                    }
                }
                
                // Autocomplete Close Logic
                 if (!e.target.closest('.autocomplete-dropdown') && !e.target.closest('.position-relative')) {
                    Livewire.dispatch('clear-suggestions');
                }
            });
            
             // Escape key to close everything
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    Livewire.dispatch('clear-suggestions');
                    document.querySelectorAll('.ttip_overlay').forEach(el => el.style.display = 'none');
                }
            });
            
             Livewire.on('clear-inputs', () => {
                setTimeout(() => {
                    const ids = ['from', 'to', 'partNumber', 'customerName', 'vendorName'];
                    ids.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) {
                            el.value = '';
                            el.dispatchEvent(new Event('input')); 
                        }
                    });
                }, 50);
            });
            
             Livewire.on('clear-suggestions', () => {});
        });
    </script>
</div>