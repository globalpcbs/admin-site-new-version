<div>
    @include('includes.flash')
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-2" wire:submit.prevent>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fa fa-search"></i> Search By Part Number
                    </label>
                    <div class="input-group">
                        <input type="text" wire:model.defer="partSearchInput" class="form-control"
                            placeholder="Enter Part Number">
                        <button type="button" wire:click="searchByPartNo" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fa fa-search"></i> Search By Customer Name
                    </label>
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
            <i class="fa fa-list"></i> Manage Packing Slips
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped align-middle btn-sm text-nowrap">
                <thead class="table-light text-center">
                    <tr>
                        <th><i class="fa fa-id-badge"></i> Slip ID</th>
                        <th><i class="fa fa-list-ol"></i> Slip #</th>
                        <th><i class="fa fa-user"></i> Customer</th>
                        <th><i class="fa fa-cube"></i> Part No</th>
                        <th><i class="fa fa-refresh"></i> Rev</th>
                        <th><i class="fa fa-calendar"></i> Packing Date</th>
                        <th><i class="fa fa-pencil"></i> Edit</th>
                        <th><i class="fa fa-file-pdf-o"></i> PDF</th>
                        <th><i class="fa fa-file-word-o"></i> DOC</th>
                        <th><i class="fa fa-trash"></i></th>
                        <th><i class="fa fa-clone"></i></th>
                        <th>Invoiced</th>
                        <th>Logged</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packingSlips as $slip)
                    <tr>
                        <td>{{ $slip->invoice_id ?? 'N/A' }}</td>
                        <td>{{ $slip->invoice_id+9987 ?? 'N/A' }}</td>
                        <td>{{ $slip->custo->c_name ?? 'N/A' }}</td>
                        <td>{{ $slip->part_no ?? 'N/A' }}</td>
                        <td>{{ $slip->rev ?? 'N/A' }}</td>
                        <td>
                            {{ $slip->podate ? \Carbon\Carbon::parse($slip->podate)->format('m/d/Y') : 'N/A' }}
                        </td>
                        <td>
                            <a href="{{ route('packing.edit',$slip->invoice_id) }}">
                                <button type="button" class="btn btn-sm btn-xs btn-primary">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('download.packingpdf',$slip->invoice_id) }}">
                                <button type="button" class="btn btn-sm btn-xs btn-success">
                                    <i class="fa fa-download"></i>
                                </button>
                            </a>
                            <a href="{{ route('view.packingpdf',$slip->invoice_id) }}">
                                <button type="button" class="btn btn-sm btn-xs btn-info">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('download.packingdocs',$slip->invoice_id) }}">
                                <button type="button" class="btn btn-sm btn-xs btn-warning">
                                    <i class="fa fa-download"></i>
                                </button>
                            </a>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-xs btn-danger"
                                wire:click="confirmDelete({{ $slip->invoice_id }})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-xs btn-secondary"
                                wire:click="duplicate({{ $slip->invoice_id }})">
                                <i class="fa fa-copy"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" wire:click="togglePending({{ $slip->invoice_id }})"
                                {{ $slip->pending == 'Yes' ? 'checked' : '' }} onclick="if (!this.checked) {
                const confirmed = confirm('Are you sure you want to unmark this as pending?');
                if (!confirmed) event.preventDefault();
            }" class="form-check-input mt-0">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input mt-0"
                                wire:change="isLogged({{ $slip->invoice_id }})">
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="14" class="text-center">No Packing Slips Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $packingSlips->links('pagination::bootstrap-5') }}
        </div>
    </div>
    @if ($confirmingDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this packing slip?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                    <button class="btn btn-danger" wire:click="deletePackingSlip">
                        <i class="fa fa-trash"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>