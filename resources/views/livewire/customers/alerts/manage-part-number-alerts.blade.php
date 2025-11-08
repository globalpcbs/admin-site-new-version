<div>

    <!-- 🔍 Search bar ----------------------------------------------------------->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">

                {{-- Part‑number search --}}
                <div class="col-md-6">
                    <input wire:model.defer="partInput" class="form-control" placeholder="Search by part number …">
                    @error('partInput')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror

                    <button type="button" class="btn btn-sm btn-primary mt-1" wire:click="search_by_part_number"
                        wire:target="search_by_part_number" wire:loading.attr="disabled">
                        <i class="fa fa-search"></i> Search
                        <i class="fa fa-spinner fa-spin" wire:loading wire:target="search_by_part_number"></i>
                    </button>
                </div>

                {{-- Customer‑name search --}}
                <div class="col-md-6">
                    <input wire:model.defer="customerInput" class="form-control"
                        placeholder="Search by customer name …">
                    @error('customerInput')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror

                    <button type="button" class="btn btn-sm btn-primary mt-1" wire:click="search_by_customer_name"
                        wire:target="search_by_customer_name" wire:loading.attr="disabled">
                        <i class="fa fa-search"></i> Search
                        <i class="fa fa-spinner fa-spin" wire:loading wire:target="search_by_customer_name"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- 📋 Results table ----------------------------------------------------->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
            Manage Part‑Number Alerts
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th><i class="fa fa-hashtag"></i> #</th>
                        <th><i class="fa fa-user"></i> Customer</th>
                        <th><i class="fa fa-cube"></i> Part / Rev</th>
                        <th><i class="fa fa-bell"></i> Alerts</th>
                        <th class="text-center"><i class="fa fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alerts as $index => $row)
                    <tr wire:key="alert-row-{{ $row->first_id  }}">
                        <td class="text-center">
                            {{ ($alerts->currentPage() - 1) * $alerts->perPage() + $index + 1 }}.
                        </td>

                        <td>{{ $row->customer }}</td>

                        <td>{{ $row->part_no }} / {{ $row->rev }}</td>

                        <td>
                            @foreach(explode("\n", $row->alerts) as $alertIndex => $alert)
                                @if(trim($alert) !== '')
                                    <strong>{{ $alertIndex + 1 }}.</strong> {{ $alert }}<br />
                                @endif
                            @endforeach
                        </td>

                        <td class="text-center">
                            <a href="{{ route('customers.alerts.edit', [
                                'customer' => $row->customer,
                                'part'     => $row->part_no,
                                'rev'      => $row->rev ?? '',
                            ]) }}" class="btn btn-sm btn-outline-success">
                                <i class="fa fa-pencil"></i>
                            </a>

                            <button class="btn btn-sm btn-outline-danger" 
                                wire:click="confirmDelete('{{ $row->customer }}', '{{ $row->part_no }}', '{{ $row->rev }}')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-3">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{ $alerts->links('pagination::bootstrap-5') }}
        </div>
    </div>

    {{-- Delete Confirmation Modal (Bootstrap 5) --}}
    @if ($confirmingDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
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
                        Delete all alerts for
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