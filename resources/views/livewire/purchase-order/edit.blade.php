<div class="card mb-4">
    <div class="card-header">
        <strong>Edit PURCHASE ORDER FORM</strong>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="update">
            {{-- Cancellation --}}
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-ban text-danger"></i> Cancellation
                </label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cancellation" wire:model="iscancel" value="yes">
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="cancellation" wire:model="iscancel" value="no">
                    <label class="form-check-label">No</label>
                </div>
            </div>

            {{-- Vendor / Shipper --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fa fa-industry text-primary"></i> Select Vendor
                    </label>
                    <select class="form-select" wire:model="vid">
                        <option value="">Select Vendor</option>
                        @foreach(\App\Models\vendor_tb::orderBy('c_name')->get() as $vendor)
                        <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fa fa-truck text-success"></i> Select Shipper
                    </label>
                    <select class="form-select" wire:model="sid">
                        <option value="">Select Shipper</option>
                        @foreach(\App\Models\shipper_tb::orderBy('c_name')->get() as $shipper)
                        <option value="{{ $shipper->data_id }}">{{ $shipper->c_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Requisitioner and Ship Via --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fa fa-user text-secondary"></i> Name of Requisitioner
                    </label>
                    <input type="text" class="form-control" wire:model="namereq">
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fa fa-plane text-info"></i> Ship Via
                    </label>
                    <select class="form-select" wire:model="svia">
                        <option value="Fedex">Fedex</option>
                        <option value="UPS">UPS</option>
                        <option value="Personal Delivery">Personal Delivery</option>
                        <option value="Elecronic Data">Electronic Data</option>
                        <option value="Other">Other</option>
                    </select>
                    @if($svia == 'Other')
                    <input type="text" class="form-control mt-2" placeholder="Specify other" wire:model="svia_oth">
                    @endif
                </div>
            </div>

            {{-- City / State / Shipping Terms --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fa fa-building-o text-muted"></i> City
                    </label>
                    <input type="text" class="form-control" wire:model="city">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fa fa-map text-muted"></i> State
                    </label>
                    <input type="text" class="form-control" wire:model="state">
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <i class="fa fa-briefcase text-primary"></i> Shipping Terms
                    </label>
                    <select class="form-select" wire:model="sterms">
                        <option value="Prepaid">Prepaid</option>
                        <option value="priority">Priority</option>
                        <option value="ground">Ground</option>
                        <option value="2nd Day">2nd Day</option>
                    </select>
                </div>
            </div>


            {{-- Item Rows --}}
            <h5 class="mt-4 mb-2">Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%">
                                <i class="fa fa-cube text-primary"></i> Item
                            </th>
                            <th style="width: 35%">
                                <i class="fa fa-file-text text-info"></i> Description
                            </th>
                            <th style="width: 15%">
                                <i class="fa fa-sort-numeric-asc text-success"></i> QTY
                            </th>
                            <th style="width: 20%">
                                <i class="fa fa-usd text-warning"></i> Unit Price
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i < 6; $i++) <tr>
                            <td>
                                <input type="text" class="form-control" wire:model.live="items.{{ $i }}.item">
                            </td>
                            <td>
                                <select class="form-select mb-1" wire:model.live="items.{{ $i }}.dpdesc">
                                    <option value="">Select</option>
                                    <option value="pcbp">PCB Fabrication (Production)</option>
                                    <option value="pcbeo">PCB Fabrication (Expedited Order)</option>
                                    <option value="nre">NRE</option>
                                    <option value="exf">Expedite Fee</option>
                                    <option value="suc">Set-up Charge</option>
                                    <option value="frt">Freight</option>
                                    <option value="etst">E-Test</option>
                                    <option value="fpb">Flying Probe</option>
                                    <option value="etstf">E-Test Fixture</option>
                                    <option value="sf">Surface Finish</option>
                                    <option value="oth">Other</option>
                                </select>
                                @if($items[$i]['dpdesc'] ?? '' === 'oth')
                                <input type="text" class="form-control" placeholder="Other desc"
                                    wire:model.live="items.{{ $i }}.desc">
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control" wire:model.live="items.{{ $i }}.qty">
                            </td>
                            <td>
                                <input type="text" class="form-control" wire:model.live="items.{{ $i }}.uprice">
                            </td>
                            </tr>
                            @endfor
                    </tbody>
                </table>
            </div>

            {{-- Total Price (display only) --}}
            <div class="mb-3 text-end fw-bold">
                <i class="fa fa-calculator text-danger"></i> Total Price:
                ${{ number_format($total, 2) }}
            </div>

            {{-- Lookup ID --}}
            <div class="mb-3">
                {{-- Lookup & Reference (autocomplete) --}}
                <div>
                    <label class="fw-bold">
                        <i class="fa fa-search text-primary"></i> Lookup & Reference
                    </label>

                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Type part number …" value="{{ $search }}"
                            wire:model="search" wire:keyup="onKeyUp($event.target.value)" autocomplete="off">
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

            {{-- Part, Customer, Rev, PO, Layer Count, Req Name --}}
            <div class="row mb-3">
                <div class="col-md-6 mt-2">
                    <label class="form-label"><i class="fa fa-cogs text-muted"></i> Part #</label>
                    <input type="text" class="form-control" wire:model="part_no" wire:key="part-{{ $inputKey }}">
                </div>

                <div class="col-md-6 mt-2">
                    <label class="form-label"><i class="fa fa-user text-primary"></i> Customer</label>
                    <input type="text" class="form-control" wire:model="customer" wire:key="customer-{{ $inputKey }}">
                </div>

                <div class="col-md-6 mt-2">
                    <label class="form-label"><i class="fa fa-code-fork text-info"></i> Rev</label>
                    <input type="text" class="form-control" wire:model="rev" wire:key="rev-{{ $inputKey }}">
                </div>

                <div class="col-md-6 mt-2">
                    <label class="form-label"><i class="fa fa-file-text text-warning"></i> Customer PO#</label>
                    <input type="text" class="form-control" wire:model="cpo" wire:key="po-{{ $inputKey }}">
                </div>

                <div class="col-md-6 mt-2">
                    <label class="form-label"><i class="fa fa-layers text-success"></i> Layer Count</label>
                    <input type="text" class="form-control" wire:model="no_layer" wire:key="no_layer-{{ $inputKey }}">
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label"><i class="fa fa-user-plus text-secondary"></i> Name of
                        Requestor</label>
                    <input type="text" class="form-control" wire:model="namereq1">
                </div>
            </div>
            {{-- Dates --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label"><i class="fa fa-calendar text-primary"></i> Ordered On</label>
                    <input type="date" class="form-control" wire:model="ordon">
                </div>
                <div class="col-md-6">
                    <label class="form-label"><i class="fa fa-calendar-check-o text-success"></i> Required Dock
                        Date</label>
                    <input type="date" class="form-control" wire:model="date1">
                </div>
            </div>

            {{-- ROHS --}}
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-certificate text-success"></i> ROHS Certificate Required
                </label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rohs" wire:model="rohs" value="yes">
                    <label class="form-check-label">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="rohs" wire:model="rohs" value="no">
                    <label class="form-check-label">No</label>
                </div>
            </div>
            {{-- Comments --}}
            <div class="mb-3">
                <label class="form-label"><i class="fa fa-comment text-muted"></i> Comments</label>
                <textarea class="form-control" wire:model="comments" rows="4"></textarea>
            </div>

            <div wire:poll.500ms="$refresh"></div>
            {{-- Buttons --}}
            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Submit
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fa fa-undo"></i> Reset
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-link">Go back to front page</a>
            </div>
        </form>
    </div>
</div>