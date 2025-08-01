<div class="my-4">
    @include('includes.flash')
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-user-plus"></i> Add Vendor Engineering Contact
        </div>
        <div class="card-body">

            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label for="coustid" class="form-label"><i class="fa fa-building"></i> Select Vendor</label>
                    <select wire:model="coustid" id="coustid" class="form-select">
                        <option value="">-- Select Vendor --</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                        @endforeach
                    </select>
                    @error('coustid') <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-user"></i> Name</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Enter first name">
                    @error('name') <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-user"></i> Last Name</label>
                    <input type="text" wire:model="lastname" class="form-control" placeholder="Enter last name">
                    @error('lastname') <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-phone"></i> Phone</label>
                    <input type="text" wire:model="phone" class="form-control" placeholder="Enter phone number">
                    @error('phone') <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="Enter email">
                    @error('email') <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-mobile"></i> Mobile</label>
                    <input type="text" wire:model="mobile" class="form-control" placeholder="Enter mobile number">
                    @error('mobile') <small class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary float-end btn-sm">
                        <i class="fa fa-plus-circle"></i> Add Contact <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
