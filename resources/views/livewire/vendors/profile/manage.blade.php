<div>
    <div class="mt-4">
        @if (session()->has('success'))
            <div 
                class="alert alert-success shadow"
                style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 300px;
                "
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
            >
                <i class="fa fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        
        @if($alertMessage)
            <div 
                class="alert alert-{{ $alertType }} shadow"
                style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 300px;
                "
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => { show = false; $wire.dispatch('alert-hidden') }, 3000)"
            >
                <i class="fa fa-{{ $alertType == 'success' ? 'check-circle' : 'exclamation-triangle' }}"></i> 
                {{ $alertMessage }}
            </div>
        @endif

        <div class="card mb-2">
            <div class="card-header bg-primary text-white">
                <h5>
                    <b>
                        <i class="fa fa-list"></i> Manage Vendor Profiles
                        <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
                        <a href="{{ route('vendors.profile.add') }}">
                            <button class="btn btn-light float-end btn-sm"><i class="fa fa-plus-circle"></i> Add Profile</button>
                        </a>
                    </b>
                </h5>
            </div>
            
            <!-- Vendor Filter Dropdown -->
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-12">
                        <label for="vendorFilter" class="form-label">
                            <i class="fa fa-filter"></i> Filter by Vendor:
                        </label>
                        <select id="vendorFilter" wire:change="filterVendors($event.target.value)" class="form-select">
                            <option value="">-- All Vendors --</option>
                            @foreach($vendorsList as $vendor)
                                <option value="{{ $vendor->data_id }}" {{ $selectedVendor == $vendor->data_id ? 'selected' : '' }}>
                                    {{ $vendor->c_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width: 80px;"><i class="fa fa-id-badge"></i> ID</th>
                            <th style="width: 200px;"><i class="fa fa-user"></i> Vendor</th>
                            <th><i class="fa fa-list-alt"></i> Requirements</th>
                            <th style="width: 100px;" class="text-center"><i class="fa fa-edit"></i> Edit</th>
                            <th style="width: 100px;" class="text-center"><i class="fa fa-trash"></i> Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendors as $vendor)
                            <tr>
                                <td>{{ $vendor->profid }}</td>
                                <td>
                                    <strong>{{ optional($vendor->vendor)->c_name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    @if($vendor->requirements && $vendor->requirements->count() > 0)
                                        @foreach ($vendor->requirements as $index => $req)
                                            <div class="mb-1">
                                                <span class="badge bg-danger me-2">{{ $index + 1 }}</span>
                                                {!! nl2br(e($req->reqs)) !!}
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No requirements added</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('vendors.profile.edit', $vendor->profid) }}" class="btn btn-sm btn-outline-success" title="Edit Profile">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button 
                                        class="btn btn-sm btn-outline-danger" 
                                        wire:confirm="Are you sure you want to delete this vendor profile?"
                                        wire:click="deleteVendorProfile({{ $vendor->profid }})"
                                        wire:key="delete-{{ $vendor->profid }}"
                                        title="Delete Profile"
                                    >
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="fa fa-inbox fa-2x"></i><br>
                                    No vendor profiles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer">
                {{ $vendors->links('pagination::bootstrap-5') }}
            </div>
        </div>

        {{-- Confirmation Modal --}}
        @if ($confirmingDelete)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-exclamation-triangle"></i> Confirm Deletion
                            </h5>
                            <button type="button" class="btn-close btn-close-white" wire:click="$set('confirmingDelete', false)"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this vendor profile?</p>
                            <p class="text-danger mb-0"><small>This action cannot be undone. All requirements associated with this profile will also be deleted.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                            <button class="btn btn-danger" wire:click="deleteVendorProfile">
                                <i class="fa fa-trash"></i> Confirm Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>