<div>
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-2" wire:submit.prevent>
                <div class="col-md-4">
                    <label class="form-label fw-bold"> <i class="fa fa-search"></i> Search By Part Number</label>
                    <div class="input-group">
                        <input type="text" wire:model.defer="partSearchInput" class="form-control"
                            placeholder="Enter Part Number">
                        <button type="button" wire:click="searchByPartNo" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold"> <i class="fa fa-search"></i> Search By Customer Name</label>
                    <div class="input-group">
                        <input type="text" wire:model.defer="customerSearchInput" class="form-control"
                            placeholder="Enter Customer Name">
                        <button type="button" wire:click="searchByCustomer" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary" wire:click="clearFilters">
                        <i class="fa fa-times-circle"></i> Reset Filters
                    </button>
                </div>
            </form>

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
                            <th class="text-center">Edit</th>
                            <th class="text-center">PDF</th>
                            <th class="text-center">DOC</th>
                            <th class="text-center">Delete</th>
                            <th class="text-center">Duplicate</th>
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
                            <td class="text-center">
                                <a href="{{ route('confirmation.edit',$order->poid) }}"
                                    class="btn btn-sm btn-xs btn-success">
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('download.confirmationorder',$order->poid) }}">
                                    <button class="btn btn-sm btn-xs btn-secondary">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </a>
                                <a href="{{ route('view.confirmationorder',$order->poid) }}" target="_blank">
                                    <button class="btn btn-sm btn-xs btn-secondary"><i class="fa fa-eye"></i></button>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('download.confirmationorderdoc',$order->poid) }}">
                                    <button class="btn btn-sm btn-xs btn-secondary"><i
                                            class="fa fa-file-word"></i></button>
                                </a>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-xs btn-danger" wire:click="delete({{ $order->poid }})"
                                    onclick="return confirm('Are you sure you want to delete this order?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-xs btn-info" wire:click="duplicate({{ $order->poid }})"
                                    onclick="return confirm('Duplicate this confirmation order?')">
                                    <i class="fa fa-clone"></i>
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