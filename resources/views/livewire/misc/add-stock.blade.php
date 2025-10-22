<div>
    @include('includes.flash')

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong><i class="fa fa-plus-square"></i> Add Stock</strong>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="save">
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
                            <input type="date" class="form-control" wire:model.defer="date_added">
                            @error('date_added') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                <i class="fa fa-calendar"></i> Manufacturing Date
                            </label>
                            <input type="date" class="form-control" wire:model.defer="manufacturing_date">
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
                                        <option value="{{ $i }} Month{{ $i > 1 ? 's' : '' }}">{{ $i }} Month{{ $i > 1 ? 's' : '' }}</option>
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
                                <input type="number" class="form-control" wire:model="qty">
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
                </div>
            </form>
        </div>
    </div>
</div>