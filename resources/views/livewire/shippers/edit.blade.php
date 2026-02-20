<div>
     @include('includes.flash')
    <form wire:submit.prevent="updateShipper" onkeydown="if(event.key === 'Enter') event.preventDefault();">
        @csrf
        <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-ship"></i> Edit Shipper
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="text-header"> <b> <i class="fa fa-user-plus"></i> ADD SHIPPER </b> </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-user"></i> Shipper Name</label>
                                    <input type="text" class="form-control @error('c_name') is-invalid @enderror" wire:model.defer="c_name">
                                    @error('c_name') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-map-marker"></i> Address 1</label>
                                    <input type="text" class="form-control @error('c_address') is-invalid @enderror" wire:model.defer="c_address">
                                    @error('c_address') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-map-marker"></i> Address 2</label>
                                    <input type="text" class="form-control @error('c_address2') is-invalid @enderror" wire:model.defer="c_address2">
                                    @error('c_address2') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-map-marker"></i> Address 3</label>
                                    <input type="text" class="form-control @error('c_address3') is-invalid @enderror" wire:model.defer="c_address3">
                                    @error('c_address3') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-phone"></i> Phone</label>
                                    <input type="text" class="form-control @error('c_phone') is-invalid @enderror" wire:model.defer="c_phone">
                                    @error('c_phone') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-fax"></i> Fax</label>
                                    <input type="text" class="form-control @error('c_fax') is-invalid @enderror" wire:model.defer="c_fax">
                                    @error('c_fax') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-globe"></i> Website</label>
                                    <input type="text" class="form-control @error('c_website') is-invalid @enderror" wire:model.defer="c_website">
                                    @error('c_website') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-user"></i> Main Contact</label>
                                    <input type="text" class="form-control @error('c_bcontact') is-invalid @enderror" wire:model.defer="c_bcontact">
                                    @error('c_bcontact') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="text-header"> <b> <i class="fa fa-handshake"></i> Shipper's Main Contact </b> </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-user"></i> First Name</label>
                                    <input type="text" class="form-control @error('e_name') is-invalid @enderror" wire:model.defer="e_name">
                                    @error('e_name') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-user"></i> Last Name</label>
                                    <input type="text" class="form-control @error('e_lname') is-invalid @enderror" wire:model.defer="e_lname">
                                    @error('e_lname') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-phone"></i> Phone</label>
                                    <input type="text" class="form-control @error('e_phone') is-invalid @enderror" wire:model.defer="e_phone">
                                    @error('e_phone') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-envelope"></i> Email</label>
                                    <input type="email" class="form-control @error('e_email') is-invalid @enderror" wire:model.defer="e_email">
                                    @error('e_email') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-credit-card"></i> Payment Terms</label>
                                    <input type="text" class="form-control @error('e_payment') is-invalid @enderror" wire:model.defer="e_payment">
                                    @error('e_payment') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-comment"></i> Comments</label>
                                    <textarea class="form-control @error('e_comments') is-invalid @enderror" wire:model.defer="e_comments" rows="3"></textarea>
                                    @error('e_comments') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><i class="fa fa-info-circle"></i> Other</label>
                                    <textarea class="form-control @error('e_other') is-invalid @enderror" wire:model.defer="e_other" rows="3"></textarea>
                                    @error('e_other') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>

                            </div>
                        </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm float-end">
                        <i class="fa fa-plus-circle"></i> Update Shipper
                        <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </div>
        </div>
    </form>
</div>