<div>
    @include('includes.flash')
    <div class="card">
        <div class="card-header">
            <i class="fa fa-pencil"></i> Edit Packing Slip
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update">
                {{-- Bill to / Shipped to --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-user"></i> Bill To</label>
                        <select class="form-select" wire:model="vid">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->data_id }}">{{ $customer->c_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-truck"></i> Shipped To</label>
                        <select class="form-select" wire:model="sid">
                            <option value="">Select Shipper</option>
                            @foreach($customers as $customer)
                            <option value="c{{ $customer->data_id }}">{{ $customer->c_name }}</option>
                            @endforeach
                            <option disabled>--------- Shippers List ------------</option>
                            @foreach($shippers as $shipper)
                            <option value="s{{ $shipper->data_id }}">{{ $shipper->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Ship Via --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-send"></i> Ship Via</label>
                        <select class="form-select" wire:model.live="svia">
                            <option value="">Select Method</option>
                            <option value="Electronic Data">Electronic Data</option>
                            <option value="Fedex">Fedex</option>
                            <option value="Personal Delivery">Personal Delivery</option>
                            <option value="UPS">UPS</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    @if($svia == 'Other')
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="fa fa-pencil"></i> Other (Specify)</label>
                        <input type="text" class="form-control" wire:model.defer="svia_oth">
                    </div>
                    @endif
                </div>

                {{-- Packing Items with 6 visible rows --}}
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fa fa-tag"></i> Item</th>
                                <th><i class="fa fa-align-left"></i> Description</th>
                                <th><i class="fa fa-cubes"></i> Qty Ordered</th>
                                <th><i class="fa fa-check-square-o"></i> Qty Shipped</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 6; $i++) <tr>
                                <td><input type="text" class="form-control" wire:model.lazy="items.{{ $i }}.item"></td>
                                <td><input type="text" class="form-control" wire:model.lazy="items.{{ $i }}.desc"></td>
                                <td><input type="text" class="form-control" wire:model.lazy="items.{{ $i }}.qty"></td>
                                <td><input type="text" class="form-control" wire:model.lazy="items.{{ $i }}.shipqty">
                                </td>
                                </tr>
                                @endfor
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2" class="text-end"><i class="fa fa-calculator"></i> Total</th>
                                <th><span class="fw-bold">{{ number_format($totalOrdered, 2) }}</span></th>
                                <th><span class="fw-bold">{{ number_format($totalShipped, 2) }}</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- Lookup Section --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="fa fa-search"></i> Select Customer</label>
                        <select class="form-select" wire:model="customer" wire:change="mainContact">
                            <option value="">--Select Customer--</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->data_id }}">{{ $c->c_shortname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fa fa-users"></i> Customer Main Contact
                        </label>
                        <div class="form-check" style="max-height:150px; overflow-y:auto;">
                            @if(!empty($maincontacts))
                            @foreach($maincontacts as $contact)
                            <div>
                                <input type="checkbox" class="form-check-input" wire:model="selectedMainContacts"
                                    value="{{ $contact->enggcont_id }}">
                                <label class="form-check-label">
                                    {{ ucfirst($contact->name) }} {{ ucfirst($contact->lastname) }}
                                </label>
                            </div>
                            @endforeach
                            @else
                            <div class="text-muted">No Main Contact Available Yet</div>
                            @endif
                        </div>
                    </div>
                </div>

                <h5><i class="fa fa-info-circle"></i> Other Information</h5>

                <!-- ░░ Lookup & Reference ░░ -->
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
                    <!-- {{ $search }} -->
                    <!-- Customer -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-user"></i> Customer {{ $customer_look }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" wire:model="customer_look"
                                value="{{ $customer_look }}" />
                        </div>
                        @error('customer_look') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <!-- Part # -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-cubes"></i> Part #
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-cubes"></i></span>
                            <input type="text" class="form-control" wire:model="part_no" value="{{ $part_no }}">
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
                            <input type="text" class="form-control" wire:model="rev" value="{{ $rev }}">
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
                            <input type="text" class="form-control" wire:model="oo" value="{{ $oo }}" />
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
                            <input type="text" class="form-control" wire:model="po" value="{{ $po }}">
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
                            <input type="text" class="form-control" wire:model="ord_by" value="{{ $ord_by }}" />
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
                            <input type="text" class="form-control" wire:model="lyrcnt" value="{{ $lyrcnt }}">
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

                <!-- Comments -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-commenting"></i> Comments
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-commenting"></i></span>
                        <textarea rows="4" class="form-control" wire:model.defer="comments"></textarea>
                    </div>
                    @error('comments') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                {{-- Submit Buttons --}}
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-save"></i> Update <i class="fa fa-spin fa-spinner" wire:loading></i>
                    </button>
                    <a href="{{ route('packing.manage') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>