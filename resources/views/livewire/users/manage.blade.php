<div>
    <div class="mt-5">
        @include('includes.flash')
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-users"></i> Users List
            </div>
            <div>
                <table class="table table-bordered table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th><i class="fa fa-id-badge"></i> ID</th>
                            <th><i class="fa fa-user-circle"></i> Username</th>
                            <th><i class="fa fa-calendar"></i> Created At</th>
                            <th><i class="fa fa-cogs"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ ucfirst($user->username) }}</td>
                                <td> <i class="fa fa-clock"></i> {{ $user->created_at->diffForHumans() }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" wire:click="editUser({{ $user->id }})">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" wire:click="deleteUser({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" wire:key="delete-{{ $user->id }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Confirmation Modal for Delete --}}
        @if ($confirmingDelete)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-danger"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
                            <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this user?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                            <button type="button" class="btn btn-danger" wire:click="deleteUser">
                                <i class="fa fa-trash"></i> Confirm Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Edit User Modal --}}
@if ($editingUser)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-edit"></i> Edit User</h5>
                    <button type="button" class="btn-close" wire:click="$set('editingUser', false)"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateUser">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" class="form-control" wire:model.defer="username" required>
                            @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password (leave blank to keep current password)</label>
                            <input type="password" id="password" class="form-control" wire:model.defer="password" placeholder="Enter new password if you want to change it">
                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" class="form-control" wire:model.defer="password_confirmation" placeholder="Confirm new password">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$set('editingUser', false)">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
    </div>
</div>
