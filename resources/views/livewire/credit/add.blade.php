<div>
    @include('includes.flash')

    <div class="card">
        <div class="card-header">
            <i class="fa fa-plus-circle"></i> Add Credit
        </div>

        <form wire:submit.prevent="save" class="card-body">

            <!-- ░░ Sold to / Shipped to ░░ -->
            <div class="row g-3 mb-3">
                <!-- Sold to -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-user"></i> Sold&nbsp;to
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <select class="form-select" wire:model.defer="vid">
                            <option value="">Select customer…</option>
                            @foreach ($customers as $c)
                            <option value="{{ $c->data_id }}">{{ $c->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('vid') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <!-- Shipped to -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-truck"></i> Shipped&nbsp;to
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-truck"></i></span>
                        <select class="form-select" wire:model.defer="sid">
                            <option value="">Select…</option>
                            @foreach ($customers as $c)
                            <option value="c{{ $c->data_id }}">{{ $c->c_name }}</option>
                            @endforeach
                            <option disabled>────────── Shippers List ──────────</option>
                            @foreach ($shippers as $s)
                            <option value="s{{ $s->data_id }}">{{ $s->c_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('sid') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- ░░ Rep / Ship via / Charges / City / State / Terms ░░ -->
            <div class="row g-3 mb-4">
                <!-- Sales Rep -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-user-circle-o"></i> Sales Rep
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user-circle-o"></i></span>
                        <input type="text" class="form-control" wire:model.defer="namereq">
                    </div>
                    @error('namereq') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <!-- Ship Via -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-paper-plane"></i> Ship Via
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-paper-plane"></i></span>
                        <select class="form-select" wire:model="svia">
                            <option>Elecronic Data</option>
                            <option>Fedex</option>
                            <option>Personal Delivery</option>
                            <option>UPS</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Ship Via – Other -->
                @if ($svia === 'Other')
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-pencil"></i> Specify
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-pencil"></i></span>
                        <input type="text" class="form-control" wire:model.defer="svia_oth">
                    </div>
                    @error('svia_oth') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>
                @endif

                <!-- Freight Charge -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-dollar"></i> Freight Charge
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                        <input type="text" class="form-control" wire:model.defer="fcharge">
                    </div>
                    @error('fcharge') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <!-- City -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-building-o"></i> City
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-building-o"></i></span>
                        <input type="text" class="form-control" wire:model.defer="city">
                    </div>
                    @error('city') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <!-- State -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-map-o"></i> State
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-map-o"></i></span>
                        <input type="text" class="form-control" wire:model.defer="state">
                    </div>
                    @error('state') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <!-- Shipping Terms -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fa fa-archive"></i> Shipping Terms
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-archive"></i></span>
                        <select class="form-select" wire:model.defer="sterms">
                            <option>Prepaid</option>
                            <option>Collect</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ░░ Items table ░░ -->
            <h5 class="mb-2">
                <i class="fa fa-list-ul"></i> Items
            </h5>




            <div class="table-responsive mb-3">
                <table class="table table-bordered table-striped align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th style="width:18%"><i class="fa fa-tag"></i> Item</th>
                            <th><i class="fa fa-info-circle"></i> Description</th>
                            <th class="text-end" style="width:12%"><i class="fa fa-sort-numeric-asc"></i> Qty</th>
                            <th class="text-end" style="width:15%"><i class="fa fa-usd"></i> Unit&nbsp;Price</th>
                            <th class="text-end" style="width:15%"><i class="fa fa-calculator"></i> Line&nbsp;Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $index => $row)
                        <tr wire:key="row-{{ $index }}">
                            <!-- Item -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                    <input type="text" class="form-control" wire:model.defer="items.{{ $index }}.item">
                                </div>
                            </td>

                            <!-- Description -->
                            <td>
                                <input type="text" class="form-control form-control-sm"
                                    wire:model.defer="items.{{ $index }}.desc">
                            </td>

                            <!-- Qty -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa fa-sort-numeric-asc"></i></span>
                                    <input type="text" class="form-control text-end"
                                        wire:model.lazy="items.{{ $index }}.qty">
                                    {{--  ↑  change .lazy  or  .debounce.300ms  --}}
                                </div>
                            </td>

                            <!-- Unit price -->
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="fa fa-usd"></i></span>
                                    <input type="text" class="form-control text-end"
                                        wire:model.lazy="items.{{ $index }}.uprice">
                                </div>
                            </td>

                            <!-- Line total -->
                            <td class="text-end">
                                ${{ number_format($this->lineTotal($index), 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <!-- Grand Total -->
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

            <!-- ░░ Lookup & Reference ░░ -->
            <div class="row g-3 mb-4">
                {{-- Lookup & Reference (autocomplete) --}}
                <div class="col-lg-12 position-relative">
                    <label class="fw-bold">
                        <i class="fa fa-search"></i> Lookup & Reference
                    </label>

                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>

                        <input type="text" class="form-control" placeholder="Type part number …" value="{{ $search }}"
                            wire:model="search" wire:keyup="onKeyUp($event.target.value)" autocomplete="off">
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

            <!-- Alerts panel -->
            @if ($alertHtml)
            <div class="alert alert-info mb-4" {!! $alertHtml !!}></div>
            @endif

            <!-- Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save <i class="fa fa-spinner fa-spin" wire:loading></i>
                </button>
                <button type="reset" wire:click=\"$refresh\" class="btn btn-warning" class=\"btn btn-secondary\">
                    <i class=\"fa fa-undo me-1\"></i> Reset
                </button>
            </div>
        </form>
    </div>
    <!-- Alert Modal -->
    <div class="modal fade @if($showAlertPopup) show d-block @endif" id="alertModal" tabindex="-1"
        style="@if($showAlertPopup) display: block; @endif" role="dialog">
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
                                style="pointer-events: auto;"> <!-- Ensure input is always clickable -->
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
    <div class="modal fade @if($showProfilePopup) show d-block @endif" id="profileModal" tabindex="-1"
        style="@if($showProfilePopup) display: block; @endif" role="dialog">
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

    .modal-drag-handle {
        cursor: move;
    }

    /* Ensure all interactive elements are clickable */
    .modal-content * {
        pointer-events: auto;
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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

        // Center modals when they appear
        function centerModal(modalId) {
            const modal = document.querySelector(`#${modalId} .draggable-modal`);
            if (modal) {
                const windowWidth = window.innerWidth;
                const windowHeight = window.innerHeight;
                const modalWidth = modal.offsetWidth;
                const modalHeight = modal.offsetHeight;

                modal.style.left = `${(windowWidth - modalWidth) / 2}px`;
                modal.style.top = `${(windowHeight - modalHeight) / 2}px`;
                modal.style.transform = 'translate(0px, 0px)';
                modal.setAttribute('data-x', 0);
                modal.setAttribute('data-y', 0);

                bringToFront(modal);
            }
        }

        // Livewire event listeners
        document.addEventListener('livewire:init', () => {
            Livewire.on('alert-types-updated', () => {
                document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                    checkbox.checked = checkbox.value.includes(checkbox.value);
                });
            });

            Livewire.on('showAlertPopup', () => {
                centerModal('alertModal');
            });

            Livewire.on('showProfilePopup', () => {
                centerModal('profileModal');
            });
        });

        // Initial centering if modals are already visible
        if (document.querySelector('#alertModal.show')) {
            centerModal('alertModal');
        }
        if (document.querySelector('#profileModal.show')) {
            centerModal('profileModal');
        }
    });
    </script>
</div>