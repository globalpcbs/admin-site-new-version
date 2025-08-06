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
                        @for($i = 0; $i < 6; $i++)
                            <tr>
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
            {{-- Buttons --}}
            <div>
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Update <i class="fa fa-spinner fa-spin" wire:loading></i>
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fa fa-undo"></i> Reset
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-link">Go back to front page</a>
            </div>
        </form>
    </div>
<!-- Alert Modal -->
<div class="modal fade @if($showAlertPopup) show d-block @endif" tabindex="-1"
    style="@if($showAlertPopup) display: block; @endif" role="dialog">
    <div class="modal-dialog modal-dialog-centered alert-draggable" style="max-width: 500px;">
        <div class="modal-content" style="background: #ccffff; border: 1px solid #999; font-size: 13px;">
            <div class="modal-header py-2 px-3 alert-draggable-header"
                style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                <label class="modal-title fw-bold text-dark m-0" style="font-size: 18px;">
                    <i class="fa fa-bell"></i> Part no Alerts</label>
                <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                    wire:click="closeAlertPopup">Close</button>
            </div>

            <div class="modal-body pt-2 px-3">
                @if(!empty($alertMessages))
                @foreach($alertMessages as $index => $message)
                <div class="pb-1 mb-1 border-bottom" wire:key="alert-{{ $message->id }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $index + 1 }}.</strong>
                            <span style="font-size: 13px;">{{ $message->alert }}</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm text-primary" style="font-size: 12px;"
                                wire:click="editAlert({{ $message->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm text-danger" style="font-size: 12px;"
                                wire:click="deleteAlert({{ $message->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-muted mb-3" style="font-size: 13px;">No alerts found.</div>
                @endif

                <!-- New or Edit Alert Input -->
                <div class="mt-2 mb-2">
                    <label class="form-label small mb-1">
                        @if($editingAlertId)
                        Edit Alert
                        @else
                        Add New Alert
                        @endif
                    </label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" value="{{ $newAlert }}" wire:model="newAlert">
                        <br />
                        @error('newAlert')
                        <font color="red">{{ $message }}</font>
                        @enderror
                        @if($editingAlertId)
                        <button class="btn btn-success" wire:click.prevent="updateAlert">Update</button>
                        <button class="btn btn-secondary" wire:click="cancelEdit">Cancel</button>
                        @else
                        <button class="btn btn-outline-dark" wire:click="addAlert">Add Alert</button>
                        @endif
                    </div>
                </div>

                <!-- Checkboxes -->
                <div class="d-flex flex-wrap gap-2">
                    @foreach([
                    'quo' => 'Quote',
                    'con' => 'Confirmation',
                    'pac' => 'Packing',
                    'inv' => 'Invoice',
                    'cre' => 'Credit'
                    ] as $value => $label)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="type-{{ $value }}" value="{{ $value }}"
                            wire:model="alertTypes" @checked(in_array($value, $alertTypes ?? []))>
                        <label class="form-check-label" for="type-{{ $value }}">
                            {{ $label }}
                        </label>
                    </div>
                    @endforeach
                    @error('alertTypes')
                    <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="modal fade @if($showProfilePopup) show d-block @endif" tabindex="-1"
    style="@if($showProfilePopup) display: block; @endif" role="dialog">
    <div class="modal-dialog modal-dialog-centered profile-draggable" style="max-width: 400px; width: 50%;">
        <div class="modal-content" style="background: #f0f8ff; border: 1px solid #999; font-size: 13px;">
            <div class="modal-header py-2 px-3 profile-draggable-header"
                style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                <label class="modal-title fw-bold text-dark m-0" style="font-size: 16px;">
                    <i class="fa fa-user-circle"></i> Customer Profile Requirements</label>
                <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                    wire:click="closeProfilePopup">Close</button>
            </div>

            <div class="modal-body pt-2 px-4" style="max-height: 70vh; overflow-y: auto;">
                @if(!empty($profileMessages))
                @foreach($profileMessages as $profile)
                <div class="mb-3">
                    @foreach($profile->details as $detail)
                    @if(str_contains($detail->viewable, 'cre'))
                    <div class="pb-2 mb-2 border-bottom">
                        <div class="d-flex justify-content-between">
                            <div style="width: 95%;">
                                <strong>{{ $loop->iteration }}.</strong>
                                <span style="font-size: 13px; word-wrap: break-word;">{{ $detail->reqs }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endforeach
                @else
                <div class="text-muted mb-3" style="font-size: 13px;">No profile requirements found.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Vendor Alert Modal -->
<div class="modal fade @if($showVendorAlertPopup) show d-block @endif" tabindex="-1"
    style="@if($showVendorAlertPopup) display: block; @endif" role="dialog">
    <div class="modal-dialog modal-dialog-centered vendor-draggable" style="max-width: 500px;">
        <div class="modal-content" style="background: #fffaf0; border: 1px solid #999; font-size: 13px;">
            <div class="modal-header py-2 px-3 vendor-draggable-header"
                style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                <label class="modal-title fw-bold text-dark m-0" style="font-size: 18px;">
                    <i class="fa fa-exclamation-triangle"></i> Vendor Requirements</label>
                <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                    wire:click="closeVendorAlertPopup">Close</button>
            </div>

            <div class="modal-body pt-2 px-3">
                @if(!empty($vendorAlertMessages))
                @foreach($vendorAlertMessages as $profile)
                @foreach($profile->requirements as $requirement)
                <div class="pb-2 mb-2 border-bottom">
                    <div class="d-flex justify-content-between">
                        <div style="width: 95%;">
                            <strong>{{ $loop->iteration }}.</strong>
                            <span style="font-size: 13px; word-wrap: break-word;">
                                {{ $requirement->reqs }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
                @endforeach
                @else
                <div class="text-muted mb-3" style="font-size: 13px;">
                    No vendor requirements found.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
 <style>
                .modal {
                    z-index: 1040;
                    background-color: transparent;
                    pointer-events: none;
                    /* Allow clicks to pass through modal container */
                }

                .modal.show {
                    z-index: 1050;
                    display: block;
                }

                .draggable-modal {
                    position: fixed;
                    margin: 0;
                    z-index: 1050;
                    pointer-events: auto;
                    /* Enable interactions within modal */
                }

                .mod al-drag-handle {
                    cursor: move;
                }

                /* Ensure all interactive elements are clickable */
                .modal-content * {
                    pointer-events: auto;
                }
        </style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dragging for all modals
    initDraggableModal('.alert-draggable', '.alert-draggable-header');
    initDraggableModal('.profile-draggable', '.profile-draggable-header');
    initDraggableModal('.vendor-draggable', '.vendor-draggable-header');

    function initDraggableModal(dialogSelector, headerSelector) {
        const dialog = document.querySelector(dialogSelector);
        const header = document.querySelector(headerSelector);

        if (!dialog || !header) return;

        let isDragging = false;
        let offsetX = 0, offsetY = 0;
        let originalPosition = {};

        header.addEventListener('mousedown', function(e) {
            isDragging = true;
            const rect = dialog.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;

            // Store original position
            originalPosition = {
                left: dialog.style.left,
                top: dialog.style.top,
                position: dialog.style.position,
                margin: dialog.style.margin,
                zIndex: dialog.style.zIndex
            };

            dialog.style.position = 'absolute';
            dialog.style.margin = '0';
            dialog.style.zIndex = '1055';
            dialog.style.cursor = 'move';

            document.body.style.userSelect = 'none';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            
            dialog.style.left = `${e.clientX - offsetX}px`;
            dialog.style.top = `${e.clientY - offsetY}px`;
        });

        document.addEventListener('mouseup', function() {
            if (!isDragging) return;
            
            isDragging = false;
            document.body.style.userSelect = '';
            dialog.style.cursor = 'default';
        });

        // Reset position if modal is closed while dragging
        document.addEventListener('mouseleave', function() {
            if (isDragging) {
                isDragging = false;
                document.body.style.userSelect = '';
                dialog.style.cursor = 'default';
            }
        });
    }
});

document.addEventListener('livewire:init', () => {
    Livewire.on('alert-types-updated', () => {
        document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
            checkbox.checked = checkbox.value.includes(checkbox.value);
        });
    });
});
</script>
</div>