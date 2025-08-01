<div class="mt-4" style="max-width: 400px; margin:auto;">
    @include('includes.flash')

    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-lock"></i> Change Password
        </div>
        <div class="card-body">
            <form wire:submit.prevent="changePassword">
                <div class="form-group mb-3">
                    <label for="current_password" class="form-label">
                        <i class="fa fa-key"></i> Current Password
                    </label>
                    <input type="password" id="current_password" wire:model.defer="current_password" class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="new_password" class="form-label">
                        <i class="fa fa-unlock-alt"></i> New Password
                    </label>
                    <input type="password" id="new_password" wire:model.defer="new_password" class="form-control @error('new_password') is-invalid @enderror">
                    @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="new_password_confirmation" class="form-label">
                        <i class="fa fa-unlock"></i> Confirm New Password
                    </label>
                    <input type="password" id="new_password_confirmation" wire:model.defer="new_password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-save"></i> Update Password <i class="fa fa-spinner fa-spin" wire:loading></i>
                </button>
            </form>
        </div>
    </div>
</div>
