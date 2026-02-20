<div>
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
            <i class="fa fa-{{ $alertType == 'success' ? 'check' : 'times' }}-circle"></i> 
            {{ $alertMessage }}
        </div>
    @endif
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-2 text-white">
                <i class="fa fa-industry"></i> Manage Vendor Engineering Contacts
            </h5>
        </div>
        <div class="card-body">
            {{-- Vendor Filter Dropdown --}}
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-search"></i> Search by Vendor <i class="fa fa-spinner fa-spin" wire:loading></i>
                </label>
                <select wire:change="filterVendors($event.target.value)" class="form-select">
                    <option value="">-- Select Vendor --</option>
                    @foreach($vendorList as $vendor)
                        <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Contacts Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-hashtag"></i> </th>
                            <th><i class="fa fa-industry"></i> Vendor</th>
                            <th><i class="fa fa-user"></i> Name</th>
                            <th><i class="fa fa-phone"></i> Phone</th>
                            <th><i class="fa fa-envelope"></i> Email</th>
                            <th><i class="fa fa-mobile"></i> Mobile</th>
                            <th><i class="fa fa-pencil"></i> Edit</th>
                            <th><i class="fa fa-trash"></i> Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contacts as $index => $contact)
                            <tr>
                                <td>{{ $contacts->firstItem() + $index }}</td>
                                <td>{{ $contact->vendor }}</td>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->phone }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->mobile }}</td>
                                <td>
                                    <a href="{{ route('customers.eng.edit', $contact->enggcont_id ) }}" class="btn btn-success btn-sm">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm" wire:click="deleteContact({{ $contact->enggcont_id }})" wire:confirm="Are you sure You want to delete this contact?" wire:key="delete-{{ $contact->enggcont_id }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    <i class="fa fa-info-circle"></i> No contacts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button wire:click="previousPage" class="btn btn-primary" {{ $contacts->onFirstPage() ? 'disabled' : '' }}>
                        <i class="fa fa-angle-left"></i> Previous
                    </button>

                    <span>
                        <i class="fa fa-file-text-o"></i> Page {{ $contacts->currentPage() }} of {{ $contacts->lastPage() }}
                    </span>

                    <button wire:click="nextPage" class="btn btn-primary" {{ $contacts->hasMorePages() ? '' : 'disabled' }}>
                        Next <i class="fa fa-angle-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    @if ($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-exclamation-triangle"></i> Confirm Deletion
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this contact?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteContact">
                            <i class="fa fa-trash"></i> Confirm Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
