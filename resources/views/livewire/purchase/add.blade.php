<div>
    <div class="card">
        <div class="card-header">
            <strong><i class="fa fa-plus-circle"></i> Add Purchase Order</strong>
        </div>

        <div class="card-body">
            @include('includes.flash')

            <form wire:submit.prevent="save">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Vendor</label>
                        <select wire:model="vid" class="form-select">
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Shipper</label>
                        <select wire:model="sid" class="form-select">
                            <option value="">Select Shipper</option>
                            @foreach($dataShippers as $shipper)
                            <option value="c{{ $shipper->data_id }}">{{ $shipper->c_name }}</option>
                            @endforeach
                            <option disabled>--- Shipper List ---</option>
                            @foreach($shippers as $shipper)
                            <option value="{{ $shipper->data_id }}">{{ $shipper->c_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Requisitioner</label>
                        <input wire:model="namereq" type="text" class="form-control">
                    </div>
                </div>

                {{-- Item Rows --}}
                <hr>
                <h5 class="fw-bold">Order Items</h5>
                @foreach($items as $index => $item)
                <div class="row mb-2">
                    <div class="col-md-1">
                        <input wire:model="items.{{ $index }}.item" class="form-control" placeholder="Item">
                    </div>
                    <div class="col-md-3">
                        <input wire:model="items.{{ $index }}.itemdesc" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-2">
                        <input wire:model="items.{{ $index }}.qty" type="number" class="form-control" placeholder="Qty">
                    </div>
                    <div class="col-md-2">
                        <input wire:model="items.{{ $index }}.uprice" type="number" step="0.01" class="form-control"
                            placeholder="Unit Price">
                    </div>
                    <div class="col-md-2">
                        <input value="{{ number_format((float)$item['qty'] * (float)$item['uprice'], 2) }}"
                            class="form-control bg-light" readonly>
                    </div>
                </div>
                @endforeach

                <div class="mt-3">
                    <h6>Total: <strong>${{ number_format($totalPrice, 2) }}</strong></h6>
                </div>

                <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-save"></i> Save Purchase
                    Order</button>
            </form>
        </div>
    </div>
</div>