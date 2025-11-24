<div>
    @include('includes.flash')

    <div class="mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-list"></i> Manage Vendors
            <i wire:loading class="fa fa-spinner fa-spin float-end"></i>
        </div>

        <div class="card-body">
            {{-- Search Filter --}}
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-search"></i> Search by Customer Name <i class="fa fa-spinner fa-spin" wire:loading></i>
                </label>
                <select wire:change="filterVendors($event.target.value)" class="form-select">
                    <option value="">-- Select Vendor --</option>
                    @foreach($allVendors as $v)
                        <option value="{{ $v->c_name }}">{{ $v->c_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
                    {{-- Vendor Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-id-card"></i> ID</th>
                            <th><i class="fa fa-user"></i> Customer Name</th>
                            <th><i class="fa fa-user-circle"></i> Engineer Contact</th>
                            <th><i class="fa fa-pencil"></i> Edit</th>
                            <th><i class="fa fa-trash"></i> Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            <tr>
                                <td>{{ $vendor->data_id }}</td>
                                <td>{{ $vendor->c_name }}</td>
                                <td>{{ $vendor->e_name }}</td>
                                <td>
                                    <a href="{{ route('vendor.edit',$vendor->data_id) }}" class="btn btn-success btn-sm btn-primary">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                </td>
                                <td>
                                    <button wire:click="deleteVendor({{ $vendor->data_id }})" wire:confirm="Are you sure? You want delete vendor." class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteVendorModal">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No vendors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button wire:click="previousPage" class="btn btn-primary" {{ $vendors->onFirstPage() ? 'disabled' : '' }}>
                        <i class="fa fa-angle-left"></i> Previous
                    </button>

                    <span>
                        <i class="fa fa-file-text-o"></i> Page {{ $vendors->currentPage() }} of {{ $vendors->lastPage() }}
                    </span>

                    <button wire:click="nextPage" class="btn btn-primary" {{ $vendors->hasMorePages() ? '' : 'disabled' }}>
                        Next <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div wire:ignore.self class="modal fade" id="deleteVendorModal" tabindex="-1" aria-labelledby="deleteVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteVendorModalLabel">⚠️ Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this vendor?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button wire:click="deleteVendor" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>



</div>