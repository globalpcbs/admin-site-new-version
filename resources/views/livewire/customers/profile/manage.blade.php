<div>
    <div class="mt-4">
    @include('includes.flash')

    <div class="card">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fa fa-database"></i> Manage Customer Profiles
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
               <thead>
                    <tr class="table-secondary">
                        <th style="width: 80px;">
                            <i class="fa fa-hashtag" aria-hidden="true"></i>
                        </th>
                        <th style="width: 200px;">
                             Customer
                        </th>
                        <th style="width: 100%;">
                            <i class="fa fa-list-ul" aria-hidden="true"></i> Requirements
                        </th>
                        <th style="width: 120px;" class="text-center">
                             Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($profiles as $profile)
                        <tr>
                            <td>{{ $profile->profid }}</td>
                            <td>{{ $profile->customer->c_name ?? 'N/A' }}</td>
                            <td>
                                <ol class="mb-0 ps-3">
                                    @foreach ($profile->details as $detail)
                                        <li>{{ $detail->reqs }}</li>
                                    @endforeach
                                </ol>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('customers.profile.edit',$profile->profid) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-edit"></i> 
                                </a>
                                <button wire:click="confirmDelete({{ $profile->profid }})" class="btn btn-sm btn-outline-danger" wire:key="delete-{{ $profile->profid }}">
                                    <i class="fa fa-trash"></i> 
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $profiles->links() }}
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($confirmingDelete)
        <div class="modal d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fa fa-warning"></i> Confirm Delete</h5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this profile and its requirements?
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