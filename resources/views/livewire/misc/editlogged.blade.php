<div>
    @include('includes.flash')

    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-check-circle"></i> Logged Packing Slip</h5>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row g-3">
                    {{-- Our PO --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-barcode"></i> Our PO# Lookup
                        </label>
                        <input type="text" class="form-control" wire:model="part_no11"
                            wire:keyup="onKeyUp($event.target.value)">
                    </div>
                    @if($matches)
                    <ul class="list-group position-absolute w-100 shadow-sm"
                        style="z-index:1050; max-height:220px; overflow-y:auto;">
                        @foreach($matches as $i => $m)
                        <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                            wire:click="useMatch({{ $m['value'] }})">
                            {{ $m['label'] }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    {{-- Customer --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-user"></i> Customer
                        </label>
                        <select class="form-select" wire:model="customer">
                            <option value="">Select Customer</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->data_id }}" @if($c->c_name == $customer) selected
                                @endif>{{ $c->c_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Part No --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-cubes"></i> Part No
                        </label>
                        <input type="text" class="form-control" wire:model="part_no" id="part_no"
                            value="{{ $part_no }}">
                    </div>

                    {{-- Supplier --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-industry"></i> Supplier
                        </label>
                        <select class="form-control" wire:model="supplier_id" id="" class="form-control">
                            @foreach($shippers as $shipper)
                            <option value="{{ $shipper->data_id }}" @if(!empty($supplier->data_id) && $shipper->data_id
                                == $supplier->data_id)
                                selected
                                @endif
                                >
                                {{ $shipper->c_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rev --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-refresh"></i> Rev : {{ $rev }}
                        </label>
                        <input type="text" class="form-control" wire:model="rev" value="{{ $part_no }}" />
                    </div>

                    {{-- Rec'd On --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-calendar"></i> Rec'd On
                        </label>
                        <input type="date" class="form-control" wire:model="rec_on">
                    </div>

                    {{-- OTD --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-check"></i> OTD Y/N
                        </label>
                        <select class="form-select" wire:model="otd">
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    {{-- Customer PO --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-sticky-note"></i> Customer PO
                        </label>
                        <input type="text" class="form-control" wire:model="customer_po" value="{{ $customer_po }}">
                    </div>

                    {{-- Due Date --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-calendar-check-o"></i> Customer Due Date
                        </label>
                        <input type="date" class="form-control" wire:model="cus_due_date">
                    </div>

                    {{-- Quantity Ordered --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-sort-numeric-asc"></i> Quantity Ordered
                        </label>
                        <input type="text" class="form-control" wire:model="qty_ordered">
                    </div>

                    {{-- Quantity Received --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-arrow-circle-down"></i> Quantity Rec'd
                        </label>
                        <input type="text" class="form-control" wire:model="qty_rec">
                    </div>

                    {{-- Quantity Due --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-exclamation-circle"></i> Quantity Due
                        </label>
                        <input type="text" class="form-control" wire:model="qty_due">
                    </div>

                    {{-- Quantity Shipped --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-truck"></i> Quantity Shipped
                        </label>
                        <input type="text" class="form-control" wire:model="qty_shipped">
                    </div>

                    {{-- Shipped On --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-calendar-o"></i> Shipped On
                        </label>
                        <input type="date" class="form-control" wire:model="shipped_on">
                    </div>

                    {{-- Quantity Inspected --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-search"></i> Quantity Inspected
                        </label>
                        <input type="text" class="form-control" wire:model="qty_insp">
                    </div>

                    {{-- Quantity Passed --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-check-square-o"></i> Quantity Passed
                        </label>
                        <input type="text" class="form-control" wire:model="qty_passed">
                    </div>

                    {{-- Inspected By --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-user-md"></i> Inspected By
                        </label>
                        <input type="text" class="form-control" wire:model="inspected_by">
                    </div>

                    {{-- Solder Sample --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-flask"></i> Solder Sample
                        </label>
                        <input type="text" class="form-control" wire:model="solder_sample">
                    </div>

                    {{-- NCR --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-times-circle"></i> NCR
                        </label>
                        <input type="text" class="form-control" wire:model="ncr">
                    </div>

                    {{-- Comment --}}
                    <div class="col-md-12">
                        <label class="form-label">
                            <i class="fa fa-comment"></i> Comment
                        </label>
                        <textarea class="form-control" wire:model="comment" rows="3"></textarea>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-12 d-flex justify-content-start gap-2">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check-circle"></i> Log Packing Slip <i class="fa fa-spinner fa-spin"
                                wire:loading></i>
                        </button>
                        <a href="{{ route('packing.manage') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
    Livewire.on('update-part-no-input', partNo => {
        document.getElementById('part_no').value = partNo;
    });
    </script>
    @endpush
</div>