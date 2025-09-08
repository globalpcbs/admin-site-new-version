<div>
    @include('includes.flash')

    <div class="card shadow-sm">
        <div class="card-header bg-primary">
            <h5 class="card-title mb-2 text-white">
                <i class="fa fa-users"></i> Manage Customers
            </h5>
        </div>

        <div class="card-body">
            {{-- Customer Search Dropdown --}}
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-search"></i> Search by Customer Name
                    <i class="fa fa-spinner fa-spin" wire:loading></i>
                </label>
                <select wire:change="filterCustomers($event.target.value)" class="form-select">
                    <option value="">-- Select Customer --</option>
                    @foreach($allCustomers as $c)
                        <option value="{{ $c->c_name }}">{{ $c->c_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Customers Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-id-badge"></i> ID</th>
                            <th><i class="fa fa-user"></i> Name</th>
                            <th><i class="fa fa-edit"></i> Edit</th>
                            <th><i class="fa fa-trash"></i> Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td>{{ $customer->data_id }}</td>
                                <td>{{ ucfirst($customer->c_name) }}</td>
                                <td>
                                    <a href="{{ route('customers.edit',$customer->data_id) }}" class="btn btn-success btn-sm">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $customer->data_id }})" wire:key="delete-{{ $customer->data_id }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <i class="fa fa-info-circle"></i> No customers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button wire:click="previousPage" class="btn btn-primary" {{ $customers->onFirstPage() ? 'disabled' : '' }}>
                        <i class="fa fa-angle-left"></i> Previous
                    </button>

                    <span>
                        Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}
                    </span>

                    <button wire:click="nextPage" class="btn btn-primary" {{ $customers->hasMorePages() ? '' : 'disabled' }}>
                        Next <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm Delete Modal --}}
    @if ($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this customer?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteCustomer">
                            <i class="fa fa-trash"></i> Confirm Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
