<div>
       <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                <div class="col-lg-4">
                        <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                            <input type="text" class="form-control"
                                placeholder="Enter part number" wire:model="searchPartNoInput"
                                placeholder="Enter part number" wire:keydown.enter="searchq" wire:keyup="usekeyupno($event.target.value)" wire:key="searchPartNoInput-{{ now()->timestamp }}"   />
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
            <i class="fa fa-list"></i> Confirmation Orders <i class="fa fa-spinner fa-spin float-end text-danger"
                wire:loading></i>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->poid }}</td>
                            <td>{{ $order->conf_no }}</td>
                            <td>{{ $order->customer }}</td>
                            <td>{{ $order->part_no }}</td>
                            <td>{{ $order->rev }}</td>
                            <td>{{ $order->podate }}</td>
                            <td>
                                <a href="{{ route('download.confirmationorder',$order->poid) }}">
                                    <button class="btn btn-sm btn-xs btn-secondary">
                                        <i class="fa fa-download"></i> Download pdf
                                    </button>
                                </a>
                                <a href="{{ route('view.confirmationorder',$order->poid) }}" target="_blank">
                                    <button class="btn btn-sm btn-xs btn-success"><i class="fa fa-eye"></i> View Pdf</button>
                                </a>
                                 <a href="{{ route('download.confirmationorderdoc',$order->poid) }}">
                                    <button class="btn btn-sm btn-xs btn-danger"><i
                                            class="fa fa-file-word"></i> Download Doc</button>
                                </a>
                                 <a href="{{ route('confirmation.edit',$order->poid) }}"
                                                                    class="btn btn-sm btn-xs btn-success">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                 <button class="btn btn-sm btn-xs btn-danger" wire:click="delete({{ $order->poid }})"
                                    onclick="return confirm('Are you sure you want to delete this order?')" wire:key="delete-{{ $order->poid }}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                                 <button class="btn btn-sm btn-xs btn-info" wire:click="duplicate({{ $order->poid }})"
                                    onclick="return confirm('Duplicate this confirmation order?')" wire:key="duplicate-{{ $order->poid }}">
                                    <i class="fa fa-clone"></i> Duplicate
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted">No confirmation orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
</div>