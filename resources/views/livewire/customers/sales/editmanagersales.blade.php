<div>
    {{-- flash messages ------------------------------------------------------ --}}
    @include('includes.flash')

    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-pencil"></i> Edit Sales Rep
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save">
                {{-- Rep name ------------------------------------------------ --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-user"></i> Rep's Name
                    </label>
                    <input type="text" wire:model.defer="repname" class="form-control">
                    @error('repname')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Company name ------------------------------------------- --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-building"></i> Company Name
                    </label>
                    <input type="text" wire:model.defer="compname" class="form-control">
                    @error('compname')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Customers represented (multi‑select) ------------------ --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-users"></i> Customer Represented
                    </label>
                    <select wire:model="invsoldto" multiple class="form-control" size="5">
                        @foreach ($customers as $customer)
                        <option value="{{ $customer->data_id }}">
                            {{ $customer->c_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('invsoldto')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Commission % ------------------------------------------- --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-percent"></i> Commission %
                    </label>
                    <input type="text" wire:model.defer="comval" class="form-control">
                    @error('comval')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Address lines ------------------------------------------ --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-map-marker"></i> Address 1
                    </label>
                    <input type="text" wire:model.defer="txtaddress" class="form-control">
                    @error('txtaddress')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Address 2</label>
                    <input type="text" wire:model.defer="txtaddress2" class="form-control">
                    @error('txtaddress2')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Address 3</label>
                    <input type="text" wire:model.defer="txtaddress3" class="form-control">
                    @error('txtaddress3')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Contact details ---------------------------------------- --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-phone"></i> Phone
                    </label>
                    <input type="text" wire:model.defer="txtphone2" class="form-control">
                    @error('txtphone2')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-fax"></i> Fax
                    </label>
                    <input type="text" wire:model.defer="txtfax2" class="form-control">
                    @error('txtfax2')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-globe"></i> Website
                    </label>
                    <input type="text" wire:model.defer="txtweb" class="form-control">
                    @error('txtweb')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Payment terms & comments ------------------------------- --}}
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-money"></i> Payment Terms
                    </label>
                    <input type="text" wire:model.defer="txtepay" class="form-control">
                    @error('txtepay')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fa fa-comment"></i> Comments
                    </label>
                    <textarea wire:model.defer="txtecomments" rows="4" class="form-control"></textarea>
                    @error('txtecomments')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Direct / Indirect checkbox ----------------------------- --}}
                <div class="form-check mb-3">
                    <input type="checkbox" wire:model="indirect" class="form-check-input" id="indirect">
                    <label class="form-check-label" for="indirect">
                        <i class="fa fa-random"></i> Indirect (uncheck for Direct)
                    </label>
                </div>

                {{-- Submit button ------------------------------------------ --}}
                <button type="submit" class="btn btn-primary btn-sm float-end rounded-0" wire:target="save"
                    wire:loading.attr="disabled">
                    <i class="fa fa-save"></i> Update Sales Rep
                    <i class="fa fa-spinner fa-spin" wire:loading wire:target="save"></i>
                </button>
            </form>
        </div>
    </div>
</div>