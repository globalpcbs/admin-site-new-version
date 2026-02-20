<div>
        @include('includes.flash')
        <form wire:submit.prevent="submit" onkeydown="if(event.key === 'Enter') event.preventDefault();">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-plus-circle"></i> Add Vendor
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-building"></i> Vendor Name
                                <i class="fa fa-asterisk text-danger" style="font-size: 0.6em;"></i>
                            </label>
                            <input type="text" wire:model.defer="c_name" class="form-control">
                            @error('c_name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-tag"></i> Vendor Short Name
                                <i class="fa fa-asterisk text-danger" style="font-size: 0.6em;"></i>
                            </label>
                            <input type="text" wire:model.defer="c_shortname" class="form-control">
                            @error('c_shortname') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-map-marker"></i> Address 1
                            <i class="fa fa-asterisk text-danger" style="font-size: 0.6em;"></i>
                        </label>
                        <input type="text" wire:model.defer="c_address" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-map-marker"></i> Address 2
                        </label>
                        <input type="text" wire:model.defer="c_address2" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-map-marker"></i> Address 3
                        </label>
                        <input type="text" wire:model.defer="c_address3" class="form-control">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-phone"></i> Phone
                                <i class="fa fa-asterisk text-danger" style="font-size: 0.6em;"></i>
                            </label>
                            <input type="text" wire:model.defer="c_phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-fax"></i> Fax
                            </label>
                            <input type="text" wire:model.defer="c_fax" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-globe"></i> Website
                        </label>
                        <input type="text" wire:model.defer="c_website" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-credit-card"></i> Payment Terms
                            <i class="fa fa-asterisk text-danger" style="font-size: 0.6em;"></i>
                        </label>
                        <input type="text" wire:model.defer="e_payment" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-commenting"></i> Comments
                        </label>
                        <textarea wire:model.defer="e_comments" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fa fa-ellipsis-h"></i> Other
                        </label>
                        <textarea wire:model.defer="e_other" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right">
                        <i class="fa fa-save"></i> Add Vendor <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </div>
            </div>
        </form>

</div>