<div>
    @include('includes.flash')

    <div class="card">
        <div class="card-header">
            <i class="fa fa-edit"></i> Edit Invoice #{{ $invoiceId }}
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update">
                {{-- Sold To & Shipped To --}}
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="col-form-label"><i class="fa fa-user"></i> Sold To</label>
                        <select wire:model="vid" class="form-control">
                            <option value="">Select Customer</option>
                            @foreach($customers as $cust)
                            <option value="{{ $cust->data_id }}">{{ $cust->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label"><i class="fa fa-truck"></i> Shipped To</label>
                        <select wire:model="sid" class="form-control">
                            <option value="">Select Shipper</option>
                            @foreach($customers as $cust)
                            <option value="c{{ $cust->data_id }}">{{ $cust->c_name }}</option>
                            @endforeach
                            @foreach($shippers as $shipper)
                            <option value="s{{ $shipper->data_id }}">{{ $shipper->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Sales Info --}}
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="col-form-label"><i class="fa fa-user"></i> Sales Rep</label>
                        <input type="text" wire:model="namereq" class="form-control" placeholder="Sales Rep">
                    </div>
                    <div class="col-sm-5">
                        <label class="col-form-label"><i class="fa fa-users"></i> Outside Sales Rep</label>
                        <select wire:model="salesrep" class="form-control">
                            <option value="">Select Sales Rep</option>
                            @foreach($reps as $rep)
                            <option value="{{ $rep->r_name }}">{{ $rep->r_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="col-form-label"><i class="fa fa-percent"></i> Commission (%)</label>
                        <input type="text" wire:model="commission" class="form-control" placeholder="Commission %">
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-truck"></i> Ship Via</label>
                    <div class="col-sm-10">
                        <select wire:model="svia" class="form-control">
                            <option value="Elecronic Data">Electronic Data</option>
                            <option value="Fedex">Fedex</option>
                            <option value="Personal Delivery">Personal Delivery</option>
                            <option value="UPS">UPS</option>
                            <option value="Other">Other</option>
                        </select>
                        @if($svia == 'Other')
                        <input type="text" wire:model="svia_oth" class="form-control mt-2" placeholder="Other Ship Via">
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-money"></i> Freight Charge</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="fcharge" class="form-control" placeholder="Freight Charge">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-map-marker"></i> City</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="city" class="form-control" placeholder="City">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-flag"></i> State</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="state" class="form-control" placeholder="State">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-cubes"></i> Shipping Terms</label>
                    <div class="col-sm-10">
                        <select wire:model="sterms" class="form-control">
                            <option value="Prepaid">Prepaid</option>
                            <option value="Collect">Collect</option>
                        </select>
                    </div>
                </div>

                {{-- Line Items --}}
                <table class="table table-bordered table-striped align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th style="width:18%"><i class="fa fa-tag"></i> Item</th>
                            <th><i class="fa fa-info-circle"></i> Description</th>
                            <th class="text-end" style="width:12%"><i class="fa fa-sort-numeric-asc"></i> Qty</th>
                            <th class="text-end" style="width:15%"><i class="fa fa-usd"></i> Unit&nbsp;Price</th>
                            <th class="text-end" style="width:15%"><i class="fa fa-calculator"></i> Line&nbsp;Total</th>
                            <th class="text-center"><i class="fa fa-percent"></i> Commission</th>
                        </tr>
                    </thead>
                    <!-- <pre>{{ var_export($items, true) }}</pre> -->
                    <tbody>
                        @foreach ($items as $index => $row)
                        <tr wire:key="row-{{ $index }}">
                            <!-- Item -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                    <input type="text" class="form-control"
                                        wire:model.debounce.300ms="items.{{ $index }}.item" wire:input="$refresh">
                                </div>
                            </td>

                            <!-- Description -->
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.debounce.300ms="items.{{ $index }}.description" wire:input="$refresh">
                            </td>

                            <!-- Qty -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa fa-sort-numeric-asc"></i></span>
                                    <input type="text" class="form-control text-end"
                                        wire:model.debounce.300ms="items.{{ $index }}.qty" wire:input="$refresh">
                                </div>
                            </td>

                            <!-- Unit price -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa fa-usd"></i></span>
                                    <input type="text" class="form-control text-end"
                                        wire:model.debounce.300ms="items.{{ $index }}.unit_price" wire:input="$refresh">
                                </div>
                            </td>

                            <!-- Line total with optional commission -->
                            <td class="text-end">
                                ${{ number_format($this->lineTotal($index), 2) }}
                                @if($row['commission'])
                                <div class="text-muted small">
                                    + ${{ number_format($this->lineTotal($index) * ($commission / 100), 2) }} Comm
                                </div>
                                @endif
                            </td>

                            <!-- Commission Checkbox -->
                            <td class="text-center">
                                <input type="checkbox" wire:model="items.{{ $index }}.commission"
                                    wire:change="$refresh">
                            </td>
                        </tr>
                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr class="table-light">
                            <th colspan="4" class="text-end py-2 fs-6"><strong>TOTAL</strong></th>
                            <th class="text-end py-2 fs-6 fw-bold">${{ number_format($this->total, 2) }}</th>
                            <th></th>
                        </tr>
                        <tr class="table-light">
                            <th colspan="4" class="text-end py-2 fs-6"><strong>COMMISSION (%)</strong></th>
                            <th class="text-end py-2 fs-6">{{ $commission }}</th>
                            <th></th>
                        </tr>
                        <tr class="table-light">
                            <th colspan="4" class="text-end py-2 fs-6"><strong>TOTAL COMMISSION</strong></th>
                            <th class="text-end py-2 fs-6 fw-bold">${{ number_format($this->totalCommission, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <!-- ░░ Lookup & Reference ░░ -->
                <h5><i class="fa fa-info-circle"></i> Other Information</h5>

                <div class="row g-3 mb-4">
                    {{-- Lookup & Reference (autocomplete) --}}
                    <div class="col-lg-12 position-relative">
                        <label class="fw-bold">
                            <i class="fa fa-search"></i> Lookup & Reference
                        </label>

                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>

                            <input type="text" class="form-control" placeholder="Type part number …"
                                value="{{ $search }}" wire:model="search" wire:keyup="onKeyUp($event.target.value)"
                                autocomplete="off">
                        </div>

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

                    <!-- Customer -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-user"></i> Customer
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" wire:model="customer">
                        </div>
                        @error('customer') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Part # -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-cubes"></i> Part #
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-cubes"></i></span>
                            <input type="text" class="form-control" wire:model="part_no">
                        </div>
                        @error('part_no') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Rev -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-repeat"></i> Rev
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-repeat"></i></span>
                            <input type="text" class="form-control" wire:model="rev">
                        </div>
                        @error('rev') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Our PO# -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-hashtag"></i> Our PO#
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                            <input type="text" class="form-control" wire:model="oo">
                        </div>
                        @error('oo') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Customer PO# -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-hashtag"></i> Customer PO#
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                            <input type="text" class="form-control" wire:model="po">
                        </div>
                        @error('po') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Ordered By -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-user-o"></i> Ordered By
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user-o"></i></span>
                            <input type="text" class="form-control" wire:model="ord_by">
                        </div>
                        @error('ord_by') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Layer Count -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-clone"></i> Layer Count
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-clone"></i></span>
                            <input type="text" class="form-control" wire:model="lyrcnt">
                        </div>
                        @error('lyrcnt') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Delivered to -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-location-arrow"></i> Delivered to
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-location-arrow"></i></span>
                            <input type="text" class="form-control" wire:model="delto">
                        </div>
                        @error('delto') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Delivered on -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-calendar"></i> Delivered on
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input type="date" class="form-control" wire:model="date1">
                        </div>
                        @error('date1') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Sales Tax -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-percent"></i> Sales Tax
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-percent"></i></span>
                            <input type="text" class="form-control" wire:model="stax">
                        </div>
                        @error('stax') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Comments --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="fa fa-commenting"></i> Comments</label>
                    <textarea rows="4" class="form-control" wire:model.defer="comments"></textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Update Invoice <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>