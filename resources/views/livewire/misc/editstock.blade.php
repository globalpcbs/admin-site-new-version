<div>
    @include('includes.flash')
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fa fa-plus-square"></i> Add Stock</strong>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row g-3">
                    <!-- Lookup ID -->
                    <div class="col-md-12">
                        <div>
                            <label class="fw-bold">
                                <i class="fa fa-search text-primary"></i> Lookup & Reference
                            </label>

                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Type part number …"
                                    value="{{ $search }}" wire:model="search" wire:keyup="onKeyUp($event.target.value)"
                                    autocomplete="off">
                            </div>

                            <div wire:ignore.self>
                                @if($matches)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches as $i => $m)
                                    <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                        wire:click="useMatch({{ $i }})">
                                        {{ $m['label'] }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    <div class="row">
                        <!-- Customer -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-user"></i> Customer
                            </label>
                            <input type="text" class="form-control" wire:model="customer"
                                wire:key="customer-{{ $inputKey }}">
                            @error('customer') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <!-- Part # -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-cube"></i> Part #
                            </label>
                            <input type="text" class="form-control" wire:model="part_no"
                                wire:key="part-{{ $inputKey }}">
                            @error('part_no') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <!-- Rev -->
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fa fa-code-fork"></i> Rev
                            </label>
                            <input type="text" class="form-control" wire:model="rev" wire:key="rev-{{ $inputKey }}">
                            @error('rev') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-2 mt-1">
                            <label class="form-label">
                                <i class="fa fa-industry"></i> Supplier
                            </label>
                            <select class="form-select" wire:model.defer="supplier">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->data_id }}">{{ $supplier->c_name }}</option>
                                @endforeach
                            </select>
                            @error('supplier') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-2 mt-1">
                            <label class="form-label">
                                <i class="fa fa-calendar"></i> Date Added
                            </label>
                            <div wire:ignore>
    <input type="text"
           class="form-control"
           id="date_added"
           placeholder="MM-DD-YYYY">
</div>
                            @error('date_added') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                <i class="fa fa-calendar"></i> Manufacturing Date
                            </label>
                            <div wire:ignore>
                                <input type="text"
                                    class="form-control"
                                    id="manufacturing_date"
                                    placeholder="MM-DD-YYYY">
                            </div>
                            @error('manufacturing_date') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-3 mb-2">
                                <label class="form-label">
                                    <i class="fa fa-calendar"></i> D/C
                                </label>
                                <input type="text" class="form-control" wire:model.defer="dc">
                                @error('dc') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-lg-3 mb-2">
                                <label class="form-label">
                                    <i class="fa fa-paint-brush"></i> Finish
                                </label>
                                <input type="text" class="form-control" wire:model.defer="finish">
                                @error('finish') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" wire:model.defer="panel" id="panel">
                                    <label class="form-check-label" for="panel">
                                        Panel
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fa fa-hourglass-half"></i> Shelf Life
                                </label>
                                <select class="form-select" wire:model.defer="shelf_life">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == $shelf_life ? 'selected' : '' }}>{{ $i }} Month{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                @error('shelf_life') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row mt-3">

                            <!-- Unit Price -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-dollar"></i> Unit Price
                                </label>
                                <input type="number" step="0.01" class="form-control" wire:model="uprice">
                                @error('uprice') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-sort-numeric-asc"></i> Qty
                                </label>
                                <input type="text" class="form-control" wire:model.live="qty" wire:key="qty-{{ $inputKey }}">
                                @error('qty') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div wire:poll.500ms>
                                    <b>Total:</b> ${{ number_format($this->total, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-primary btn-sm btn-xs float-end" wire:click="openModal"> <i
                                class="fa fa-plus-circle"></i> Allocate</button>

                        <!-- Allocation Table -->
                        <label><i class="fa fa-clock text-warning"></i> Pending Allocations</label>
                        <table class="table table-sm font-xs table-bordered">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-user"></i> Customer</th>
                                    <th><i class="fa fa-file-text"></i> PO#</th>
                                    <th><i class="fa fa-calendar"></i> Due Date</th>
                                    <th><i class="fa fa-cubes"></i> Qty</th>
                                    <th><i class="fa fa-cogs"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pending as $row)
                                <tr>
                                    <td>{{ $row->customer }}</td>
                                    <td>{{ $row->pono }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->due_date)->format('m-d-Y') }}</td>
                                    <td>{{ $row->qut }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-xs btn-success"
                                            wire:click="edit({{ $row->id }})"><i class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-xs btn-sm btn-danger"
                                            wire:click="delete({{ $row->id }})" wire:confirm><i
                                                class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr>

                        <label><i class="fa fa-truck text-info"></i> Delivered Allocations</label>
                        <table class="table font-xs table-bordered">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-user"></i> Customer</th>
                                    <th><i class="fa fa-file-text-o"></i> PO#</th>
                                    <th><i class="fa fa-calendar"></i> Due Date</th>
                                    <th><i class="fa fa-cubes"></i> Qty</th>
                                    <th><i class="fa fa-truck"></i> Delivered On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delivered as $row)
                                <tr>
                                    <td>{{ $row->customer }}</td>
                                    <td>{{ $row->pono }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->due_date)->format('m-d-Y') }}</td>
                                    <td>{{ $row->qut }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->delivered_on)->format('m-d-Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Modal -->
                        @if($showModal)
                        <div class="modal show d-block" style="background: rgba(0,0,0,0.5);">
                            <div class="modal-dialog">
                                <div class="modal-content p-3">
                                    <label class="text-danger"> <b> <i class="fa fa-plus-circle"></i> Allocate Stock </b></label>
                                    <label for="">Customer</label>
                                    <input type="text" class="form-control form-control-sm mb-2" wire:keyup="onKeyUpForCustomer($event.target.value)" placeholder="Customer"
                                        wire:model.defer="alloc_customer" wire:key="alloc_customer-{{ $inputKey }}">
                                    <div wire:ignore.self>
                                        @if($customer_search)
                                        <ul class="list-group position-absolute w-100 shadow-sm"
                                            style="z-index:1050; min-height:220px; font-size: 10px; overflow-y:auto;">
                                            @foreach($customer_search as $i => $m)
                                            <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                                wire:click="useCustomerMatch({{ $i }})">
                                                {{ $m['c_shortname'] }}
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </div>
                                    <label for="">PO#</label>
                                    <input type="text" class="form-control form-control-sm mb-2" placeholder="PO#"
                                        wire:model.defer="alloc_pono">
                                    <label for="">Due Date</label>
                                    <input type="date" class="form-control form-control-sm mb-2" placeholder="Due Date"
                                        wire:model.defer="alloc_duedate">
                                    <label for="">Allocation Date</label>
                                    <input type="date" class="form-control form-control-sm mb-2" placeholder="Allocation Date"
                                        wire:model.defer="alloc_allocationdate">
                                    <label for="">Quantity</label>
                                    <input type="number" class="form-control form-control-sm mb-2" placeholder="Quantity"
                                        wire:model.defer="alloc_qut">
                                    <label for="">Allocate By</label>
                                    <input type="text" class="form-control form-control-sm mb-2" placeholder="Allocate By"
                                        wire:model.defer="alloc_allocate_by">
                                    <label for="">Delivered On</label>
                                    <input type="date" class="form-control form-control-sm mb-2" placeholder="Delivered On"
                                        wire:model.defer="alloc_deliveredon">

                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-xs btn-success me-2" type="button"
                                            wire:click="save"> <i class="fa fa-save"></i> Save</button>
                                        <button type="button" class="btn btn-sm btn-xs btn-secondary"
                                            wire:click="$set('showModal', false)">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif


                    </div>
                    <!-- Comments -->
                    <div class="col-md-12 mt-3">
                        <label class="form-label">
                            <i class="fa fa-comment"></i> Comments
                        </label>
                        <textarea class="form-control" rows="4" wire:model.defer="comments"></textarea>
                        @error('comments') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-success">
                        <i class="fa fa-plus-circle"></i> Submit
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('livewire:init', () => {

    flatpickr('#date_added', {
        dateFormat: 'm-d-Y',
        allowInput: true,
        onChange: function (_, dateStr) {
            @this.set('date_added', dateStr);
        }
    });

    flatpickr('#manufacturing_date', {
        dateFormat: 'm-d-Y',
        allowInput: true,
        onChange: function (_, dateStr) {
            @this.set('manufacturing_date', dateStr);
        }
    });

});

document.addEventListener('livewire:init', () => {

    Livewire.on('setDateAdded', value => {
        document.querySelector('#date_added')?._flatpickr?.setDate(value, true);
    });

    Livewire.on('setManufDate', value => {
        document.querySelector('#manufacturing_date')?._flatpickr?.setDate(value, true);
    });

});
</script>



