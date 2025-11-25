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
            <div class="row g-3 align-items-end" wire:loading.class="opacity-50">

                <!-- Search by Part Number -->
                <div class="col-lg-4">
                    <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        <input type="text" class="form-control" 
                            wire:model="searchPartNoInput"
                            wire:keydown.enter="searchq" 
                            placeholder="Enter part number">
                        <button class="btn btn-primary" type="button" wire:click="searchq" wire:loading.attr="disabled">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    @if($matches_partno)
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            @foreach($matches_partno as $i => $m)
                                <li wire:key="match-partno-{{ $i }}" class="list-group-item list-group-item-action"
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
                            wire:keydown.enter="searchbyCustomer" 
                            placeholder="Enter customer name">
                        <button class="btn btn-primary" type="button" wire:click="searchbyCustomer" wire:loading.attr="disabled">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    @if($matches)
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            @foreach($matches as $i => $m)
                                <li wire:key="match-customer-{{ $i }}" class="list-group-item list-group-item-action"
                                    wire:click="useMatch({{ $i }})">
                                    {{ $m['customer'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Search by Vendor -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fa fa-search"></i> Search By Vendor Name
                    </label>
                    <div class="input-group">
                        <input type="text" wire:model="searchVendorInput" 
                            wire:keydown.enter="searchv" 
                            placeholder="Enter Vendor Name"
                            class="form-control">
                        <button class="btn btn-primary" wire:click="searchv" wire:loading.attr="disabled">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                    @if($matches_vendor)
                        <ul class="list-group position-absolute w-100 shadow-sm"
                            style="z-index:1050; max-height:220px; overflow-y:auto;">
                            @foreach($matches_vendor as $i => $vendor)
                                <li wire:key="match-vendor-{{ $vendor['data_id'] ?? $i }}" 
                                    class="list-group-item list-group-item-action"
                                    wire:click="useMatchve({{ $i }})">
                                    {{ $vendor['c_shortname'] ?? $vendor['c_name'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Reset Button -->
                <div class="col-md-12 mt-2 text-end">
                    <button class="btn btn-secondary" wire:click="resetFilters" wire:loading.attr="disabled">
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
        <div class="card-header">
            <i class="fa fa-list"></i> Manage Purchase Orders
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
                                // FIXED: Use the correct columns for alerts lookup
                                $alerts = \App\Models\alerts_tb::where('part_no', $order->part_no)
                                    ->where('rev', $order->rev)
                                    ->where('customer', $order->customer)
                                    ->get();
                            @endphp

                            @if($alerts->count() > 0)
                                <a href="javascript:void(0);" class="ttip_trigger text-danger">
                                    {{ $order->part_no }}
                                    <span class="badge bg-danger ms-1">{{ $alerts->count() }}</span>
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
                        <td>
                            @if($order->vendor)
                                {{ $order->vendor->c_shortname }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('purchase.orders.edit',$order->poid) }}"
                                    class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>

                                <a href="https://files.pcbsglobal.website/download-pdf1.php?id={{ $order->poid }}&oper=download"
                                    class="btn btn-xs btn-success">
                                    <i class="fa fa-download"></i> PDF
                                </a>

                                <a href="https://files.pcbsglobal.website/download-pdf1.php?id={{ $order->poid }}&oper=view" target="_blank"
                                    class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> View
                                </a>

                                <a href="https://files.pcbsglobal.website/download-doc1.php?id={{ $order->poid }}"
                                    class="btn btn-xs btn-secondary">
                                    <i class="fa fa-file-text"></i> DOC
                                </a>

                                <button wire:click="duplicate({{ $order->poid }})" 
                                    class="btn btn-xs btn-warning"
                                    wire:loading.attr="disabled">
                                    <i class="fa fa-copy"></i> Dup
                                </button>

                                <button wire:click="delete({{ $order->poid }})" 
                                    class="btn btn-xs btn-danger"
                                    wire:loading.attr="disabled">
                                    <i class="fa fa-trash"></i> Del
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No Purchase Orders Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <script>
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

        function confirm(message) {
            return window.confirm(message);
        }
    </script>
</div>