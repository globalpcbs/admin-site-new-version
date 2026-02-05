<div>
    <!-- Alert Message using component variables -->
<!-- Alert Message using component variables -->
@if($showAlert)
    <div class="alert alert-dismissible fade show auto-dismiss 
        @switch($alertType)
            @case('success')
                alert-success
                @break
            @case('warning')
                alert-danger
                @break
            @case('info')
                alert-info
                @break
        @endswitch">
        @switch($alertType)
            @case('success')
                <i class="fa fa-check-square"></i>
                @break
            @case('warning')
                <i class="fa fa-exclamation-triangle"></i>
                @break
            @case('info')
                <i class="fa fa-info-circle"></i>
                @break
        @endswitch
        {{ $alertMessage }}
        <button type="button" class="btn-close" wire:click="hideAlert"></button>
    </div>
@endif

    <style>
    .auto-dismiss {
        animation: fadeOut 0.5s ease-in 3s forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
            display: none;
            visibility: hidden;
        }
    }
    </style>

    <script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for the hide alert event
        Livewire.on('hide-alert-after-delay', () => {
            setTimeout(() => {
                @this.hideAlert();
            }, 3000);
        });
    });
    </script>

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
                            <tr wire:key="vendor-{{ $vendor->data_id }}">
                                <td>{{ $vendor->data_id }}</td>
                                <td>{{ $vendor->c_name }}</td>
                                <td>{{ $vendor->e_name }}</td>
                                <td>
                                    <a href="{{ route('vendor.edit',$vendor->data_id) }}" class="btn btn-success btn-sm btn-primary">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                </td>
                                <td>
                                    <button wire:click="deleteVendor({{ $vendor->data_id }})" 
                                            wire:confirm="Are you sure you want to delete this vendor?" 
                                            wire:key="delete-btn-{{ $vendor->data_id }}"
                                            class="btn btn-sm btn-danger">
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
            </div>
            <div class="card-body">
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
</div>