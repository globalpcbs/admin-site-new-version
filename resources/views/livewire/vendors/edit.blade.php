<div>
    @include('includes.flash')
    <form wire:submit.prevent="submit">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-edit"></i> Edit Vendor
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model.defer="c_name" class="form-control">
                        @error('c_name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vendor Short Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model.defer="c_shortname" class="form-control">
                        @error('c_shortname') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address 1 <span class="text-danger">*</span></label>
                    <input type="text" wire:model.defer="c_address" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address 2</label>
                    <input type="text" wire:model.defer="c_address2" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address 3</label>
                    <input type="text" wire:model.defer="c_address3" class="form-control">
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" wire:model.defer="c_phone" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fax</label>
                        <input type="text" wire:model.defer="c_fax" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Website</label>
                    <input type="text" wire:model.defer="c_website" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                    <input type="text" wire:model.defer="e_payment" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Comments</label>
                    <textarea wire:model.defer="e_comments" class="form-control" rows="4"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Other</label>
                    <textarea wire:model.defer="e_other" class="form-control" rows="4"></textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-sm float-right">
                    <i class="fa fa-save"></i> Update Vendor <i class="fa fa-spinner fa-spin" wire:loading></i>
                </button>
            </div>
        </div>
    </form>
</div>
