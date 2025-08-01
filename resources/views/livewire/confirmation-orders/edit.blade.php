<div>
    <div class="card">
        <div class="card-header">
            <i class="fa fa-edit"></i> Edit Confirmation Order Form
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update">

                {{-- Customer & Shipper --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fa fa-user"></i> Select Customer
                        </label>
                        <select wire:model="vid" class="form-select">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $c)
                            <option value="{{ $c->data_id }}">{{ $c->c_name }}</option>
                            @endforeach
                        </select>
                        @error('vid') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fa fa-truck"></i> Select Shipper
                        </label>
                        <select wire:model="sid" class="form-select">
                            <option value="">Select Shipper</option>
                            @foreach ($customers as $c)
                            <option value="c{{ $c->data_id }}">{{ $c->c_name }}</option>
                            @endforeach
                            <option disabled>────────── Shippers List ──────────</option>
                            @foreach ($shippers as $s)
                            <option value="s{{ $s->data_id }}">{{ $s->c_name }}</option>
                            @endforeach
                        </select>
                        @error('sid') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                {{-- Requester, Delivered To, Ship Via --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-user-circle"></i> Name of Requestor
                        </label>
                        <input type="text" class="form-control" wire:model="namereq">
                        @error('namereq') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-location-arrow"></i> Delivered to
                        </label>
                        <input type="text" class="form-control" wire:model="delto">
                        @error('delto') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-ship"></i> Ship Via
                        </label>
                        <select wire:model.live="svia" class="form-select">
                            <option value="">Select</option>
                            <option>Personal Delivery</option>
                            <option>Courier</option>
                            <option>Pickup</option>
                            <option>Other</option>
                        </select>
                        @error('svia') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @if($svia == "Other")
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-pencil"></i> Other (if any)
                        </label>
                        <input type="text" class="form-control" wire:model="svia_oth">
                        @error('svia_oth') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @endif
                </div>

                {{-- Location --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fa fa-map-marker"></i> City
                        </label>
                        <input type="text" class="form-control" wire:model="city">
                        @error('city') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="fa fa-map"></i> State
                        </label>
                        <input type="text" class="form-control" wire:model="state">
                        @error('state') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <!-- items-table.blade.php -->
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-striped align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th style="width:18%"><i class="fa fa-tag"></i> Item</th>
                                <th><i class="fa fa-info-circle"></i> Description</th>
                                <th class="text-end" style="width:12%"><i class="fa fa-sort-numeric-asc"></i> Qty</th>
                                <th class="text-end" style="width:15%"><i class="fa fa-usd"></i> Unit Price</th>
                                <th class="text-end" style="width:15%"><i class="fa fa-calculator"></i> Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $index => $row)
                            <tr wire:key="item-{{ $index }}">
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="items.{{ $index }}.item">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="items.{{ $index }}.itemdesc">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end"
                                        wire:model.lazy="items.{{ $index }}.qty">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm text-end"
                                        wire:model.lazy="items.{{ $index }}.uprice">
                                </td>
                                <td class="text-end">
                                    ${{ number_format($this->lineTotal($index), 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="4" class="text-end py-2 fs-6"><strong>TOTAL</strong></th>
                                <th class="text-end py-2 fs-6 fw-bold">
                                    ${{ number_format($this->total, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>


                {{-- Lookup & Reference --}}
                <h5 class="mt-4"><i class="fa fa-search"></i> Lookup & Reference</h5>
                <div class="row g-3 mb-4">
                    <div class="col-lg-12 position-relative">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Type part number …" wire:model="search"
                                wire:keyup="onKeyUp($event.target.value)" autocomplete="off">
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

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-user"></i> Customer</label>
                        <input type="text" class="form-control" wire:model="customer" value="{{ $customer }}">
                        @error('customer') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-cubes"></i> Part #</label>
                        <input type="text" class="form-control" wire:model="part_no" value="{{ $part_no }}">
                        @error('part_no') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-repeat"></i> Rev</label>
                        <input type="text" class="form-control" wire:model="rev" value="{{ $rev }}">
                        @error('rev') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-hashtag"></i> Our PO#</label>
                        <input type="text" class="form-control" wire:model="oo" value="{{ $oo }}">
                        @error('oo') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-hashtag"></i> Customer PO#</label>
                        <input type="text" class="form-control" wire:model="po" value="{{ $po }}">
                        @error('po') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-clone"></i> Layer Count</label>
                        <input type="text" class="form-control" wire:model="lyrcnt" value="{{ $lyrcnt }}">
                        @error('lyrcnt') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label"><i class="fa fa-percent"></i> Sales Tax</label>
                        <input type="text" class="form-control" wire:model="stax">
                        @error('stax') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>


                {{-- Comments --}}
                <div class="mb-3">
                    <label class="form-label"><i class="fa fa-commenting"></i> Comments</label>
                    <textarea class="form-control" rows="4" wire:model="comments"></textarea>
                    @error('comments') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Deliveries --}}
                <h5 class="mt-4"><i class="fa fa-calendar"></i> Multiple Deliveries?</h5>
                @foreach($deliveries as $index => $delivery)
                <div class="row mb-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Qty"
                            wire:model="deliveries.{{ $index }}.qty">
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control" wire:model="deliveries.{{ $index }}.date">
                    </div>
                </div>
                @endforeach

                {{-- Buttons --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update <i class="fa fa-spin fa-spinner" wire:loading></i>
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-link">
                        <i class="fa fa-arrow-left"></i> Go Back
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>