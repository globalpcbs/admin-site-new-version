<div class="my-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-edit"></i> Edit Vendor Engineering Contact
        </div>
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-building"></i> Select Vendor</label>
                    <select wire:model="coustid" class="form-select">
                        <option value="">-- Select Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                        @endforeach
                    </select>
                    @error('coustid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" wire:model="name" class="form-control">
                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" wire:model="lastname" class="form-control">
                    @error('lastname') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" wire:model="phone" class="form-control">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="form-control">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" wire:model="mobile" class="form-control">
                    @error('mobile') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary float-end">
                        <i class="fa fa-save"></i> Update Contact
                        <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
