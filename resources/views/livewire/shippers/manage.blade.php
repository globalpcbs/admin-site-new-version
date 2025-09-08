<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary">
            <h5 class="card-title mb-2 text-white">
                <i class="fa fa-ship"></i> Manage Shippers
            </h5>
        </div>
        <div class="card-body">
            {{-- Shipper Search Dropdown --}}
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-search"></i> Search by Shipper Name <i class="fa fa-spinner fa-spin"
                        wire:loading></i>
                </label>
                <select wire:change="filterShippers($event.target.value)" class="form-select">
                    <option value="">-- Select Shipper --</option>
                    @foreach($allShippers as $s)
                    <option value="{{ $s->c_name }}">{{ $s->c_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Shippers Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-id-badge"></i> ID</th>
                            <th><i class="fa fa-user"></i> Shipper Name</th>
                            <th><i class="fa fa-phone"></i> Contact Name</th>
                            <th><i class="fa fa-edit"></i> Edit</th>
                            <th><i class="fa fa-trash"></i> Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shippers as $shipper)
                        <tr>
                            <td>{{ $shipper->data_id }}</td>
                            <td>{{ ucfirst($shipper->c_name) }}</td>
                            <td>{{ ucfirst($shipper->e_name) }}</td>
                            <td>
                                <a href="{{ route('shippers.edit',$shipper->data_id) }}" class="btn btn-success btn-sm">
                                    <i class="fa fa-pencil"></i> Edit
                                </a>
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    wire:click="confirmDelete({{ $shipper->data_id }})"
                                    wire:key="delete-{{ $shipper->data_id }}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                <i class="fa fa-info-circle"></i> No shippers found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination Controls --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button wire:click="previousPage" class="btn btn-primary"
                        {{ $shippers->onFirstPage() ? 'disabled' : '' }}>
                        <i class="fa fa-angle-left"></i> Previous
                    </button>

                    <span>
                        <i class="fa fa-file-text-o"></i> Page {{ $shippers->currentPage() }} of
                        {{ $shippers->lastPage() }}
                    </span>

                    <button wire:click="nextPage" class="btn btn-primary"
                        {{ $shippers->hasMorePages() ? '' : 'disabled' }}>
                        Next <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            </div>
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
                    Are you sure you want to delete this shipper?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                    <button class="btn btn-danger" wire:click="deleteShipper">
                        <i class="fa fa-trash"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>