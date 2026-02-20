<div class="mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-edit"></i> Edit Vendor Main Contact
        </div>
        <div class="card-body">
            <form wire:submit.prevent="update" onkeydown="if(event.key === 'Enter') event.preventDefault();">
                <div class="mb-3">
                    <label for="cid" class="form-label">
                        <i class="fa fa-user"></i> Select Vendor
                    </label>
                    <select id="cid" class="form-control" wire:model="cid">
                        <option value="">-- Select Vendor --</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                        @endforeach
                    </select>
                    @error('cid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-user"></i> First Name
                    </label>
                    <input type="text" class="form-control" wire:model="txtename">
                    @error('txtename') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-user"></i> Last Name
                    </label>
                    <input type="text" class="form-control" wire:model="txtelname">
                    @error('txtelname') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-phone"></i> Phone
                    </label>
                    <input type="text" class="form-control" wire:model="txtephone">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-envelope"></i> Email
                    </label>
                    <input type="email" class="form-control" wire:model="txteemail">
                    @error('txteemail') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-mobile"></i> Mobile
                    </label>
                    <input type="text" class="form-control" wire:model="txteemob">
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Contact 
                        <i class="fa fa-spin fa-spinner" wire:loading></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
