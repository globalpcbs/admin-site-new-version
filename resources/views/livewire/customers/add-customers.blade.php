<div>
    @include('includes.flash')
    <form wire:submit.prevent="submit" class="space-y-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-user-plus"></i> Add Customer 
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><i class="fa fa-user"></i> Customer Name</label>
                    <input type="text" wire:model="c_name" class="form-control">
                    @error('c_name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-tag"></i> Short Name</label>
                    <input type="text" wire:model="c_shortname" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" wire:model="c_email" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-map-marker"></i> Address 1</label>
                    <input type="text" wire:model="c_address" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-phone"></i> Phone</label>
                    <input type="text" wire:model="c_phone" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-globe"></i> Website</label>
                    <input type="text" wire:model="c_website" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-credit-card"></i> Payment Terms</label>
                    <input type="text" wire:model="e_payment" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-comment"></i> Comments</label>
                    <textarea wire:model="e_comments" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-university"></i> Account No</label>
                    <input type="text" wire:model="e_other" class="form-control">
                </div>

                <div class="mb-3">
                    <label><i class="fa fa-id-card"></i> Customer ID</label>
                    <input type="text" wire:model="e_cid" class="form-control">
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary btn-sm text-white">
                    <i class="fa fa-plus-circle"></i> Add Customer 
                    <i class="fa fa-spinner fa-spin" wire:loading></i>
                </button>
            </div>
        </div>
    </form>
</div>
