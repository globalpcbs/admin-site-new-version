<div class="container py-4">
    @include('includes.flash')
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

    <!-- 💳 card with table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">
            <i class="fa fa-credit-card"></i> Credit Records
            <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            @foreach ([
    'credit_id' => '#',
    'podate' => 'Date',
    'customer' => 'Customer',
    'part_no' => 'Part #',
    'rev' => 'Rev',
] as $field => $label)
                                <th wire:click="sortBy('{{ $field }}')" style="cursor:pointer">
                                    {{ $label }}
                                    @if ($sortField === $field)
                                        <span wire:loading.remove>
                                            {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                        </span>
                                    @endif
                                </th>
                            @endforeach
                            <th>PDF</th>
                            <th>Duplicate</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($credits as $credit)
                            <tr>
                                <td>{{ $credit->credit_id }}</td>
                                <td>{{ $credit->podate }}</td>
                                <td>{{ $credit->customer }}</td>
                                <td>{{ $credit->part_no }}</td>
                                <td>{{ $credit->rev }}</td>
                                <td>
                                    <a href="{{ route('credit.pdf.download', $credit->credit_id) }}" target="_blank"
                                        class="btn btn-sm btn-danger btn-xs">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <a href="{{ route('credit.pdf', $credit->credit_id) }}" target="_blank"
                                        class="btn btn-sm btn-warning btn-xs">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info btn-xs"
                                        wire:click="duplicateRecord({{ $credit->credit_id }})">
                                        <i class="fa fa-clone"></i> Duplicate
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('credit.edit', $credit->credit_id) }}" class="btn btn-sm btn-xs btn-success">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-xs btn-danger"
                                        wire:click="confirmDelete({{ $credit->credit_id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $credits->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- ⚠️ delete-confirmation modal -->
    @if ($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" aria-modal="true"
            role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-exclamation-triangle"></i>
                            Confirm Deletion
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                    </div>

                    <div class="modal-body">
                        <p class="mb-0">
                            Delete record for
                            <strong>{{ $delCustomer }}</strong>,
                            part <strong>{{ $delPart }}</strong>,
                            rev <strong>{{ $delRev }}</strong>?
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">
                            Cancel
                        </button>

                        <button class="btn btn-danger" wire:click="deleteGroup">
                            <i class="fa fa-trash"></i> Confirm Delete
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>