<div class="mt-4">
    @include('includes.flash')

    <div class="card">
        <div class="card-header bg-primary text-white">
            <label class="mb-0"> <i class="fa fa-list"></i> Manage Vendor Main Contacts <i class="fa fa-spinner fa-spin" wire:loading></i> </label>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <td colspan="5">
                            <label for=""> <i class="fa fa-search"></i> Search via vendors</label>
                            <select wire:change="filterVendors($event.target.value)" class="form-select">
                                <option value="">-- Select Vendor --</option>
                                @foreach($vendorList as $vendor)
                                    <option value="{{ $vendor->data_id }}" {{ $selectedVendor == $vendor->data_id ? 'selected' : '' }}>
                                        {{ $vendor->c_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>    
                    </tr>
                    <tr>
                         <th><i class="fa fa-hashtag"></i> ID</th>
                        <th><i class="fa fa-user"></i> Name</th>
                        <th><i class="fa fa-user-plus"></i> Last Name</th>
                        <th><i class="fa fa-pencil"></i> Edit</th>
                        <th><i class="fa fa-trash"></i> Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td>{{ $contact->enggcont_id  }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->lastname }}</td>
                            <td><a href="{{ route('vendors.main.edit',$contact->enggcont_id) }}" class="btn btn-success btn-sm text-white"> <i class="fa fa-edit"></i> Edit</a></td>
                            <td>
                                <a href="#" wire:click.prevent="deleteContact({{ $contact->enggcont_id }})" wire:confirm="Are you sure? you want to delete" wire:key="delete-{{ $contact->enggcont_id }}" class="btn btn-danger btn-sm text-white">
                                   <i class="fa fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">No contacts found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Buttons -->
            <div class="d-flex justify-content-center align-items-center mt-3">
                <button class="btn btn-secondary me-2" wire:click="previousPage" @disabled($contacts->onFirstPage())>Previous</button>
                <span>Page {{ $contacts->currentPage() }} of {{ $contacts->lastPage() }}</span>
                <button class="btn btn-secondary ms-2" wire:click="nextPage" @disabled(!$contacts->hasMorePages())>Next</button>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Confirmation -->
    @if ($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
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
