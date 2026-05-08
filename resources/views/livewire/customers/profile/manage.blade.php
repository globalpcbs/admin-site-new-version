<div>
    <div class="mt-4">
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
                <i class="fa fa-{{ $alertType == 'success' ? 'check' : ($alertType == 'warning' ? 'trash' : 'times') }}-circle"></i> 
                {{ $alertMessage }}
            </div>
        @endif
        
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

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>
                    <b>
                        <i class="fa fa-list"></i> Manage Customer Profiles
                        <i class="fa fa-spin fa-spinner" wire:loading></i>
                        <a href="{{ route('customers.profile.add') }}">
                            <button class="btn btn-light float-end btn-sm"><i class="fa fa-plus-circle"></i> Add Profile</button>
                        </a>
                    </b>
                </h5>
            </div>
            
            <!-- Customer Filter Dropdown -->
            <div class="card-body bg-light">
                <div class="row">
                    <div class="col-md-12">
                        <label for="customerFilter" class="form-label">
                            <i class="fa fa-filter"></i> Filter by Customer:
                        </label>
                        <select id="customerFilter" wire:change="filterCustomers($event.target.value)" class="form-select">
                            <option value="">-- All Customers --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->data_id }}" {{ $selectedCustomer == $customer->data_id ? 'selected' : '' }}>
                                    {{ $customer->c_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr class="table-secondary">
                            <th style="width: 80px;">
                                <i class="fa fa-hashtag" aria-hidden="true"></i> ID
                            </th>
                            <th style="width: 200px;">
                                <i class="fa fa-user"></i> Customer
                            </th>
                            <th style="width: 100%;">
                                <i class="fa fa-list-ul" aria-hidden="true"></i> Requirements
                            </th>
                            <th style="width: 120px;" class="text-center">
                                <i class="fa fa-cogs"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($profiles as $profile)
                            <tr>
                                <td>{{ $profile->profid }}</td>
                                <td>
                                    <strong>{{ $profile->customer->c_name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    @if($profile->details && $profile->details->count() > 0)
                                        <ol class="mb-0 ps-3">
                                            @foreach ($profile->details as $detail)
                                                <li>{{ $detail->reqs }}</li>
                                            @endforeach
                                        </ol>
                                    @else
                                        <span class="text-muted">No requirements added</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('customers.profile.edit', $profile->profid) }}" class="btn btn-sm btn-outline-success" title="Edit Profile">
                                        <i class="fa fa-edit"></i> 
                                    </a>
                                    <button 
                                        wire:click="confirmDelete({{ $profile->profid }})" 
                                        class="btn btn-sm btn-outline-danger" 
                                        wire:key="delete-{{ $profile->profid }}"
                                        title="Delete Profile"
                                    >
                                        <i class="fa fa-trash"></i> 
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    <i class="fa fa-inbox fa-2x text-muted"></i><br>
                                    No profiles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $profiles->links('pagination::bootstrap-5') }}
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        @if($confirmingDelete)
            <div class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-exclamation-triangle"></i> Confirm Delete
                            </h5>
                            <button type="button" class="btn-close btn-close-white" wire:click="$set('confirmingDelete', false)"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this profile and its requirements?</p>
                            <p class="text-danger mb-0"><small>This action cannot be undone.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" wire:click="$set('confirmingDelete', false)">Cancel</button>
                            <button class="btn btn-danger btn-sm" wire:click="deleteProfile">Yes, Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>