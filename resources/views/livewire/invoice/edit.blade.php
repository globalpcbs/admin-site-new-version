<div>
    @include('includes.flash')

    <div class="card">
        <div class="card-header">
            <i class="fa fa-edit"></i> Edit Invoice #{{ $invoiceId }}
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update" onkeydown="if(event.key === 'Enter') event.preventDefault();">
                {{-- Sold To & Shipped To --}}
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="col-form-label"><i class="fa fa-user"></i> Sold To</label>
                        <select wire:model="vid" class="form-control">
                            <option value="">Select Customer</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->data_id }}" @if($vid == $cust->data_id) selected @endif>{{ $cust->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="col-form-label"><i class="fa fa-truck"></i> Shipped To</label>
                        <select wire:model="sid" class="form-control">
                            <option value="">Select Shipper</option>
                            @foreach($customers as $cust)
                                <option value="c{{ $cust->data_id }}" @if($sid == 'c' . $cust->data_id) selected @endif>{{ $cust->c_name }}</option>
                            @endforeach
                            @foreach($shippers as $shipper)
                                <option value="s{{ $shipper->data_id }}" @if($sid == 's' . $shipper->data_id) selected @endif>{{ $shipper->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Sales Info --}}
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <label class="col-form-label"><i class="fa fa-user"></i> Sales Rep</label>
                        <input type="text" wire:model="namereq" class="form-control" placeholder="Sales Rep" value="{{ $namereq }}">
                    </div>
                    <div class="col-sm-5">
                        <label class="col-form-label"><i class="fa fa-users"></i> Outside Sales Rep</label>
                        <select wire:model="salesrep" class="form-control">
                            <option value="">Select Sales Rep</option>
                            @foreach($reps as $rep)
                                <option value="{{ $rep->r_name }}" @if($salesrep == $rep->r_name) selected @endif>{{ $rep->r_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
    <label class="col-form-label"><i class="fa fa-percent"></i> Commission (%)</label>
    <input type="text" wire:model.live="commission" class="form-control" placeholder="Commission %">
</div>
                </div>

                {{-- Shipping Info --}}
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-truck"></i> Ship Via</label>
                    <div class="col-sm-10">
                        <select wire:model="svia" class="form-control">
                            <option value="Elecronic Data" @if($svia == 'Elecronic Data') selected @endif>Electronic Data</option>
                            <option value="Fedex" @if($svia == 'Fedex') selected @endif>Fedex</option>
                            <option value="Personal Delivery" @if($svia == 'Personal Delivery') selected @endif>Personal Delivery</option>
                            <option value="UPS" @if($svia == 'UPS') selected @endif>UPS</option>
                            <option value="Other" @if($svia == 'Other') selected @endif>Other</option>
                        </select>
                        @if($svia == 'Other')
                            <input type="text" wire:model="svia_oth" class="form-control mt-2" placeholder="Other Ship Via" value="{{ $svia_oth }}">
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-money"></i> Freight Charge</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="fcharge" class="form-control" placeholder="Freight Charge" value="{{ $fcharge }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-map-marker"></i> City</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="city" class="form-control" placeholder="City" value="{{ $city }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-flag"></i> State</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="state" class="form-control" placeholder="State" value="{{ $state }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label"><i class="fa fa-cubes"></i> Shipping Terms</label>
                    <div class="col-sm-10">
                        <select wire:model="sterms" class="form-control">
                            <option value="Prepaid" @if($sterms == 'Prepaid') selected @endif>Prepaid</option>
                            <option value="Collect" @if($sterms == 'Collect') selected @endif>Collect</option>
                        </select>
                    </div>
                </div>

                {{-- Line Items --}}
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

                    <tbody>
                        @foreach ($items as $index => $row)
                            <tr wire:key="row-{{ $index }}">
                                <!-- Item -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                        <input type="text" class="form-control"
                                            wire:model.live="items.{{ $index }}.item">
                                    </div>
                                </td>

                                <!-- Description -->
                                <td>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.live="items.{{ $index }}.description">
                                </td>

                                <!-- Qty -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fa fa-sort-numeric-asc"></i></span>
                                        <input type="text" class="form-control text-end"
                                            wire:model.live="items.{{ $index }}.qty">
                                    </div>
                                </td>

                                <!-- Unit price -->
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fa fa-usd"></i></span>
                                        <input type="text" class="form-control text-end"
                                            wire:model.live="items.{{ $index }}.unit_price">
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
                                    <input type="checkbox" wire:model.live="items.{{ $index }}.commission">
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

                        @if(count($matches) > 0)
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

                    <!-- Customer -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="fa fa-user"></i> Customer
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" wire:model="customer" value="{{ $customer }}">
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
                            <input type="text" class="form-control" wire:model="oo" value="{{ $oo }}">
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
                            <input type="text" class="form-control" wire:model="ord_by" value="{{ $ord_by }}">
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
                            <input type="text" class="form-control" wire:model="delto" value="{{ $delto }}">
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
                            <input type="date" class="form-control" wire:model="date1" value="{{ $date1 }}">
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
                            <input type="text" class="form-control" wire:model="stax" value="{{ $stax }}">
                        </div>
                        @error('stax') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Comments --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><i class="fa fa-commenting"></i> Comments</label>
                    <textarea rows="4" class="form-control" wire:model.defer="comments">{{ $comments }}</textarea>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success @if($button_status == 1) disabled @endif">
                        <i class="fa fa-save"></i> Update Invoice <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Modal -->
    <div class="modal fade @if($showAlertPopup) show @endif" id="alertModal" tabindex="-1"
        aria-hidden="@if(!$showAlertPopup) true @else false @endif"
        style="@if($showAlertPopup) display: block; background-color: transparent; @endif">
        <div class="modal-dialog modal-dialog-centered draggable-modal" style="max-width: 500px;">
            <div class="modal-content" style="background: #ccffff; border: 1px solid #999; font-size: 13px;">
                <div class="modal-header py-2 px-3 modal-drag-handle"
                    style="background: transparent; border-bottom: 1px solid #999; cursor: move;">
                    <label class="modal-title fw-bold text-dark m-0" style="font-size: 18px;">
                        <i class="fa fa-bell"></i> Part no Alerts</label>
                    <button type="button" class="btn btn-link text-danger p-0" style="font-size: 13px;"
                        wire:click="closeAlertPopup">Close</button>
                </div>

                <div class="modal-body pt-2 px-3">
                    @if(!empty($alertMessages))
                        @php 
                            $count = 1;
                        @endphp
                        @foreach($alertMessages as $index => $message)
                            <div class="pb-1 mb-1 border-bottom" wire:key="alert-{{ $message->id }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $count++ }}.</strong>
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

                    <div class="mt-2 mb-2">
                        <label class="form-label small mb-1">
                            @if($editingAlertId)
                                Edit Alert
                            @else
                                Add New Alert
                            @endif
                        </label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="{{ $newAlert }}" wire:model="newAlert"
                                style="pointer-events: auto;">
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

                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['quo' => 'Quote', 'con' => 'Confirmation','po' => 'Purchase Order', 'pac' => 'Packing', 'cre' => 'Credit'] as $value => $label)
                            <div class="form-check" style="margin-right: 0;">
                                <input type="checkbox" class="form-check-input" id="type-{{ $value }}" value="{{ $value }}"
                                    wire:model="alertTypes"
                                    wire:key="alert-type-{{ $value }}-{{ $editingAlertId ?? 'new' }}">
                                <label class="form-check-label" for="type-{{ $value }}">{{ $label }}</label>
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
    <div class="modal fade @if($showProfilePopup) show @endif" id="profileModal" tabindex="-1"
        aria-hidden="@if(!$showProfilePopup) true @else false @endif"
        style="@if($showProfilePopup) display: block; background-color: transparent; @endif">
        <div class="modal-dialog modal-dialog-centered draggable-modal" style="max-width: 400px; width: 50%;">
            <div class="modal-content" style="background: #f0f8ff; border: 1px solid #999; font-size: 13px;">
                <div class="modal-header py-2 px-3 modal-drag-handle"
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

    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        
        .modal {
            z-index: 1050;
            background-color: transparent;
            pointer-events: none;
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
        }

        .modal-drag-handle {
            cursor: move;
        }

        .modal-content * {
            pointer-events: auto;
        }
        
        .list-group {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 2px;
        }
        
        .list-group-item {
            padding: 8px 12px;
            border: none;
            border-bottom: 1px solid #eee;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .list-group-item:hover {
            background-color: #f5f5f5;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let zIndexCounter = 1050;

            // Initialize interact.js for draggable modals
            interact('.draggable-modal').draggable({
                allowFrom: '.modal-drag-handle',
                ignoreFrom: 'button, input, a, .btn, [wire\\:click], [wire\\:model]',
                modifiers: [
                    interact.modifiers.restrictRect({
                        restriction: 'parent',
                        endOnly: true
                    })
                ],
                listeners: {
                    start(event) {
                        bringToFront(event.target);
                    },
                    move(event) {
                        const target = event.target;
                        const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                        const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                        target.style.transform = `translate(${x}px, ${y}px)`;
                        target.setAttribute('data-x', x);
                        target.setAttribute('data-y', y);
                    }
                }
            });

            function bringToFront(modal) {
                zIndexCounter++;
                modal.style.zIndex = zIndexCounter;
            }

            // Function to show and center a modal
            function showAndCenterModal(modalId) {
                const modal = document.querySelector(`#${modalId}`);
                if (modal) {
                    // Add show class and display block
                    modal.classList.add('show');
                    modal.style.display = 'block';
                    
                    // Center the draggable modal
                    const draggableModal = modal.querySelector('.draggable-modal');
                    if (draggableModal) {
                        const windowWidth = window.innerWidth;
                        const windowHeight = window.innerHeight;
                        const modalWidth = draggableModal.offsetWidth;
                        const modalHeight = draggableModal.offsetHeight;

                        draggableModal.style.left = `${(windowWidth - modalWidth) / 2}px`;
                        draggableModal.style.top = `${(windowHeight - modalHeight) / 2}px`;
                        draggableModal.style.transform = 'translate(0px, 0px)';
                        draggableModal.setAttribute('data-x', 0);
                        draggableModal.setAttribute('data-y', 0);

                        bringToFront(draggableModal);
                    }
                    
                    // Add backdrop
                    addBackdrop();
                }
            }

            // Function to hide a modal
            function hideModal(modalId) {
                const modal = document.querySelector(`#${modalId}`);
                if (modal) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    removeBackdrop();
                }
            }

            // Backdrop management
            function addBackdrop() {
                let backdrop = document.querySelector('.modal-backdrop');
                if (!backdrop) {
                    backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    backdrop.style.zIndex = '1040';
                    document.body.appendChild(backdrop);
                }
            }

            function removeBackdrop() {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop && !document.querySelector('.modal.show')) {
                    backdrop.remove();
                }
            }

            // Livewire event listeners
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-alert-popup', () => {
                    setTimeout(() => {
                        showAndCenterModal('alertModal');
                    }, 100);
                });

                Livewire.on('show-profile-popup', () => {
                    setTimeout(() => {
                        showAndCenterModal('profileModal');
                    }, 100);
                });

                Livewire.on('alert-types-updated', () => {
                    setTimeout(() => {
                        document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                        });
                    }, 50);
                });

                Livewire.on('lookup-completed', () => {
                    console.log('Lookup completed');
                });
            });

            // Handle close buttons
            document.addEventListener('click', function(event) {
                if (event.target.closest('[wire\\:click="closeAlertPopup"]')) {
                    hideModal('alertModal');
                }
                
                if (event.target.closest('[wire\\:click="closeProfilePopup"]')) {
                    hideModal('profileModal');
                }
                
                // Close dropdown when clicking outside
                const lookupInput = document.querySelector('input[wire\\:keyup="onKeyUp"]');
                const dropdown = document.querySelector('.list-group');
                
                if (dropdown && lookupInput && 
                    !dropdown.contains(event.target) && 
                    !lookupInput.contains(event.target) &&
                    !event.target.closest('.list-group')) {
                    Livewire.dispatch('clear-matches');
                }
            });

            // Handle Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hideModal('alertModal');
                    hideModal('profileModal');
                }
                
                // Handle Enter key in lookup
                const lookupInput = document.querySelector('input[wire\\:keyup="onKeyUp"]');
                if (event.key === 'Enter' && lookupInput && lookupInput === document.activeElement) {
                    event.preventDefault();
                    
                    const firstMatch = document.querySelector('.list-group-item');
                    if (firstMatch) {
                        firstMatch.click();
                    }
                }
            });

            // Initial check for modals that should be visible
            setTimeout(() => {
                if (document.querySelector('#alertModal')?.classList.contains('show')) {
                    showAndCenterModal('alertModal');
                }
                if (document.querySelector('#profileModal')?.classList.contains('show')) {
                    showAndCenterModal('profileModal');
                }
            }, 200);
        });
    </script>
</div>