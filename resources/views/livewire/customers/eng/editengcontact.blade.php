<div>
    <div class="mt-2">
        @include('includes.flash')
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-edit"></i> Edit Engineering Contact
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="form-group mb-3">
                        <label>Select Customer</label>
                        <select class="form-control" wire:model="cid">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->data_id }}">{{ $cust->c_name }}</option>
                            @endforeach
                        </select>
                        @error('cid') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>First Name</label>
                        <input type="text" class="form-control" wire:model="txtename" placeholder="Enter First Name">
                        @error('txtename') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Last Name</label>
                        <input type="text" class="form-control" wire:model="txtelname" placeholder="Enter Last Name">
                    </div>

                    <div class="form-group mb-3">
                        <label>Phone</label>
                        <input type="text" class="form-control" wire:model="txtephone" placeholder="Enter Phone">
                    </div>

                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" wire:model="txteemail" placeholder="Enter Email">
                    </div>

                    <div class="form-group mb-3">
                        <label>Mobile</label>
                        <input type="text" class="form-control" wire:model="txteemob" placeholder="Enter Mobile">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-end">
                            <i class="fa fa-save"></i> Update Contact <i class="fa fa-spinner fa-spin" wire:loading></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
