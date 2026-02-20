<div>
    @include('includes.flash')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @if(session('allocation_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('allocation_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fa fa-plus-square"></i> Add Stock</strong>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save" onkeydown="if(event.key === 'Enter') event.preventDefault();">
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
                                        wire:click="useMatch({{ $i }})" style="cursor: pointer;">
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
                                    <input class="form-check-input" type="checkbox" wire:model.defer="docsready" value="1" id="docsready">
                                    <label class="form-check-label" for="docsready">
                                        Panel
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="fa fa-hourglass-half"></i> Shelf Life
                                </label>
                                <select class="form-select" wire:model.defer="shelflife">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $shelflife == $i ? 'selected' : '' }}>
                                            {{ $i }} Month{{ $i > 1 ? 's' : '' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('shelflife') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row mt-3">
                            <!-- Unit Price -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-dollar"></i> Unit Price
                                </label>
                                <input type="number" step="0.01" class="form-control" wire:model="uprice" wire:change="calculateTotal">
                                @error('uprice') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fa fa-sort-numeric-asc"></i> Qty
                                </label>
                                <input type="number" class="form-control" wire:model="qty" wire:change="calculateTotal">
                                @error('qty') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <b>Total:</b> ${{ number_format($this->total, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
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
                    <a href="{{ route('misc.manage-stock') }}" class="btn btn-primary">
                        <i class="fa fa-list"></i> Manage Stock
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Allocation Popup (Optional for Add) -->
    @if($showAllocationPopup)
    <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Allocate Stock</h5>
                    <button type="button" class="btn-close" wire:click="closeAllocation"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveAllocation">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer</label>
                                <input type="text" class="form-control" wire:model="allocation_customer"
                                       wire:keyup.debounce.300ms="onCustomerKeyUp($event.target.value)">
                                @if(count($customer_matches) > 0)
                                <ul class="list-group" style="position: absolute; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                    @foreach($customer_matches as $match)
                                    <li class="list-group-item list-group-item-action" 
                                        style="cursor: pointer;"
                                        wire:click="useCustomerMatch('{{ $match }}')">
                                        {{ $match }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PO#</label>
                                <input type="text" class="form-control" wire:model="allocation_pono">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Due Date</label>
                                <input type="text" class="form-control" wire:model="allocation_duedate" placeholder="mm-dd-yyyy">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Allocation Date</label>
                                <input type="text" class="form-control" wire:model="allocation_date" placeholder="mm-dd-yyyy">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" wire:model="allocation_qut">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Allocate By</label>
                                <input type="text" class="form-control" wire:model="allocation_by">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Delivered On</label>
                                <input type="text" class="form-control" wire:model="allocation_deliveredon" placeholder="mm-dd-yyyy">
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeAllocation">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
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

