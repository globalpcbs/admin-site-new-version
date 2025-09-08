<div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                 <div class="col-lg-4">
                        <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                            <input type="text" class="form-control" wire:model="searchPartNoInput"
                                placeholder="Enter part number" wire:keydown.enter="searchq" wire:keyup="usekeyupno($event.target.value)" wire:key="searchPartNoInput-{{ now()->timestamp }}" />
                            <button class="btn btn-primary" type="button" wire:click="searchq">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div wire:ignore.self>
                            @if($matches_partno)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches_partno as $i => $m)
                                        <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                            wire:click="useMatchpn({{ $i }})">
                                            {{ $m['part_no'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Search by Customer Name -->
                    <div class="col-lg-4">
                        <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" wire:model="searchCustomerInput"
                                placeholder="Enter customer name" wire:keydown.enter="searchbyCustomer" wire:keyup="onKeyUp($event.target.value)" wire:key="searchCustomerInput-{{ now()->timestamp }}">
                            <button class="btn btn-primary" type="button" wire:click="searchbyCustomer">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div wire:ignore.self>
                            @if($matches)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches as $i => $m)
                                        <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                            wire:click="useMatch({{ $i }})">
                                            {{ $m['customer'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                {{-- Search by Vendor --}}
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fa fa-search"></i> Search By Vendor Name
                    </label>
                    <div class="input-group">
                        <input type="text" wire:model="searchVendorInput" wire:keydown.enter="searchv" wire:keyup="usekeyupvendor($event.target.value)" wire:key="searchVendorInput-{{ now()->timestamp }}" class="form-control"
                            placeholder="Enter Vendor Name">
                        <button class="btn btn-primary" wire:click="searchv">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                     <div wire:ignore.self>
                            @if($matches_vendor)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches_vendor as $i => $vendor)
                                        <li wire:key="match-{{ $vendor['data_id'] }}" 
                                            class="list-group-item list-group-item-action"
                                            wire:click="useMatchve({{ $i }})">
                                            {{ $vendor['c_shortname'] ?? $vendor['c_name'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                </div>

                {{-- Reset Button --}}
                <div class="col-md-12 mt-2 text-end">
                    <button class="btn btn-secondary" wire:click="resetFilters">
                        <i class="fa fa-times-circle"></i> Reset Filters
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Manage Purchase Orders
            <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
        </div>

        <div>
            <table class="table table-bordered table-sm table-striped font-xs">
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
                        <td>{{ $order->vendor->c_shortname }}</td>
                        <td>
                            <a href="{{ route('purchase.orders.edit',$order->poid) }}"
                                class="btn btn-sm btn-xs btn-primary">
                                <i class="fa fa-pencil"></i> Edit
                            </a>

                            <a href="{{ route('download.purchaseorder',$order->poid) }}"
                                class="btn btn-xs btn-sm btn-success">
                                <i class="fa fa-download"></i> PDF
                            </a>

                            <a href="{{ route('view.purchaseorder',$order->poid) }}" target="_blank"
                                class="btn btn-sm btn-xs btn-info">
                                <i class="fa fa-eye"></i> View PDF
                            </a>

                            <a href="{{ route('downloaddoc.purchaseorder',$order->poid) }}"
                                class="btn btn-sm btn-xs btn-secondary">
                                <i class="fa fa-file-text"></i> DOC
                            </a>

                            <button wire:click="delete({{ $order->poid }})" wire:key="delete-{{ $order->poid }}" class="btn btn-xs btn-sm btn-danger"
                                onclick="return confirm('Are you sure to delete?')">
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

        // Show on click
        trigger.addEventListener('click', function (e) {
            e.stopPropagation(); // prevent closing immediately
            document.querySelectorAll('.ttip_overlay').forEach(t => t.style.display = 'none');
            tooltip.style.display = 'block';
        });

        // Close tooltip on clicking close button
        closeBtn.addEventListener('click', function () {
            tooltip.style.display = 'none';
        });

        // Hide tooltip on clicking outside
        document.addEventListener('click', function (e) {
            if (!tooltip.contains(e.target) && e.target !== trigger) {
                tooltip.style.display = 'none';
            }
        });
    });
});
</script>

</div>