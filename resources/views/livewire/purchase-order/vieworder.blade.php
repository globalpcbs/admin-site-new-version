<div>
    <!-- Header Section -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        PURCHASE ORDER #{{ $purchase->poid ?? 'N/A' }}
                    </h4>
                    <small class="opacity-75">Created on: {{ $formattedPodate }}</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-sm" wire:click="printOrder">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                    <a href="{{ route('purchase.orders.manage') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Company & Vendor Info -->
        <div class="col-md-8">
            <!-- Company & Vendor Information -->
            <div class="row">
                <!-- From Company -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-building me-2 text-primary"></i>From</h6>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold text-primary">Your Company Name</h6>
                            <p class="mb-1 small">123 Business Street</p>
                            <p class="mb-1 small">City, State 12345</p>
                            <p class="mb-0 small">Phone: (555) 123-4567</p>
                        </div>
                    </div>
                </div>

                <!-- Vendor Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-industry me-2 text-success"></i>Vendor</h6>
                        </div>
                        <div class="card-body">
                            @if($vendor)
                                <h6 class="fw-bold text-success">{{ $vendor->c_name }}</h6>
                                <p class="mb-1 small"><strong>ID:</strong> {{ $vendor->data_id }}</p>
                                <p class="mb-1 small"><strong>Contact:</strong> {{ $vendor->contact ?? 'N/A' }}</p>
                                <p class="mb-0 small"><strong>Phone:</strong> {{ $vendor->phone ?? 'N/A' }}</p>
                            @else
                                <p class="text-muted mb-0">Vendor information not available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Order Details -->
            <div class="row">
                <!-- Shipping Information -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-truck me-2 text-info"></i>Shipping Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row small">
                                <div class="col-6">
                                    <strong>Shipper:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $shipper->c_name ?? 'N/A' }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>Ship Via:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $purchase->svia }}
                                    @if($purchase->svia_oth)
                                        ({{ $purchase->svia_oth }})
                                    @endif
                                </div>
                                
                                <div class="col-6">
                                    <strong>Terms:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $purchase->sterms }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>City/State:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $purchase->city }}, {{ $purchase->state }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="col-md-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-warning"></i>Order Info</h6>
                        </div>
                        <div class="card-body">
                            <div class="row small">
                                <div class="col-6">
                                    <strong>Customer:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $purchase->customer }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>Part #:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $purchase->part_no }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>Revision:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $purchase->rev }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>Ordered On:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $formattedOrdon }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>Required Date:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $formattedDate1 }}
                                </div>
                                
                                <div class="col-6">
                                    <strong>ROHS:</strong>
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-{{ $purchase->rohs == 'yes' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($purchase->rohs) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-list-alt me-2 text-danger"></i>Order Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th class="ps-3">Item</th>
                                    <th>Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end pe-3">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td class="ps-3 fw-bold">{{ $item->item }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $item->dpval }}</strong>
                                                @if($item->itemdesc)
                                                    <br><small class="text-muted">{{ $item->itemdesc }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">{{ number_format($item->qty2) }}</td>
                                        <td class="text-end">${{ number_format($item->uprice, 2) }}</td>
                                        <td class="text-end pe-3 fw-bold">${{ number_format($item->tprice, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <br>No items found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold ps-3">Grand Total:</td>
                                    <td class="text-end fw-bold fs-5 text-success pe-3">
                                        ${{ number_format($totalAmount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Status & Additional Info -->
        <div class="col-md-4">
            <!-- Order Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-clipboard-check me-2 text-info"></i>Order Status</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($purchase->iscancel == 'yes')
                            <span class="badge bg-danger fs-6 py-2 px-3">
                                <i class="fas fa-ban me-1"></i>CANCELLED
                            </span>
                        @else
                            <span class="badge bg-success fs-6 py-2 px-3">
                                <i class="fas fa-check-circle me-1"></i>ACTIVE
                            </span>
                        @endif
                    </div>
                    
                    <div class="timeline">
                        <div class="timeline-item {{ $purchase->podate ? 'completed' : '' }}">
                            <i class="fas fa-file-alt timeline-icon"></i>
                            <div class="timeline-content">
                                <small class="fw-bold">Order Created</small>
                                <br>
                                <small class="text-muted">{{ $formattedPodate }}</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item {{ $purchase->date1 ? 'completed' : '' }}">
                            <i class="fas fa-calendar-alt timeline-icon"></i>
                            <div class="timeline-content">
                                <small class="fw-bold">Required Date</small>
                                <br>
                                <small class="text-muted">{{ $formattedDate1 }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2 text-warning"></i>Additional Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Requisitioner:</strong>
                        <p class="mb-1 small">{{ $purchase->namereq }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Requestor:</strong>
                        <p class="mb-1 small">{{ $purchase->namereq1 }}</p>
                    </div>
                    
                    @if($purchase->cpo)
                    <div class="mb-3">
                        <strong>Customer PO#:</strong>
                        <p class="mb-1 small">{{ $purchase->cpo }}</p>
                    </div>
                    @endif
                    
                    @if($purchase->comments)
                    <div>
                        <strong>Comments:</strong>
                        <p class="mb-0 small text-muted">{{ $purchase->comments }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2 text-primary"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('purchase.orders.edit',$purchase->poid) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Order
                        </a>
                        @if($purchase->iscancel == 'no')
                            <button class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times me-1"></i> Cancel Order
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>