<div>
    @include('includes.flash')
    <div class="card">
        <div class="card-header">
            <i class="fa fa-pencil"></i> Edit Packing Slip
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update" onkeydown="if(event.key === 'Enter') event.preventDefault();">
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
                <!-- Comments -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="fa fa-commenting"></i> Comments
                </label>
                
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
                <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

                <style>
                    .CodeMirror {
                        height: 150px !important;
                        min-height: 120px;
                        border-radius: 0 0 4px 4px !important;
                    }
                    .editor-toolbar {
                        border-radius: 4px 4px 0 0 !important;
                        border: 1px solid #ddd !important;
                        border-bottom: none !important;
                    }
                    .editor-toolbar .separator {
                        display: none !important;
                    }
                    .editor-toolbar a:not(.fa-bold):not(.fa-italic):not(.fa-list-ul):not(.fa-list-ol):not(.fa-quote-left) {
                        display: none !important;
                    }
                    .custom-align-btn {
                        display: inline-flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        padding: 5px 10px !important;
                        border: 1px solid transparent !important;
                        border-radius: 3px !important;
                        cursor: pointer !important;
                        font-size: 14px !important;
                        color: #333 !important;
                        background: transparent !important;
                        text-decoration: none !important;
                    }
                    .custom-align-btn:hover {
                        background: #f0f0f0 !important;
                    }
                    .comment-editor-wrapper {
                        position: relative;
                    }
                    .comment-editor-wrapper .input-group-text {
                        position: absolute;
                        top: 10px;
                        left: 10px;
                        z-index: 10;
                        background: transparent !important;
                        border: none !important;
                        color: #6c757d !important;
                        pointer-events: none;
                    }
                    .comment-editor-wrapper .editor-toolbar {
                        padding-left: 40px !important;
                    }
                    .comment-editor-wrapper .CodeMirror {
                        padding-left: 35px !important;
                    }
                </style>

                <div class="comment-editor-wrapper">
                    <span class="input-group-text"><i class="fa fa-commenting"></i></span>
                    
                    <div wire:ignore>
                        <input type="hidden" wire:model="comments" id="commentsContent">
                        
                        <div class="editor-toolbar" id="customToolbar">
                            <a class="fa fa-bold" title="Bold"></a>
                            <a class="fa fa-italic" title="Italic"></a>
                            <a class="custom-align-btn" data-align="left" title="Align Left">
                                <i class="fa fa-align-left"></i>
                            </a>
                            <a class="custom-align-btn" data-align="center" title="Align Center">
                                <i class="fa fa-align-center"></i>
                            </a>
                            <a class="custom-align-btn" data-align="right" title="Align Right">
                                <i class="fa fa-align-right"></i>
                            </a>
                            <a class="fa fa-list-ul" title="Bullet List"></a>
                            <a class="fa fa-list-ol" title="Numbered List"></a>
                            <a class="fa fa-quote-left" title="Quote"></a>
                        </div>
                        
                        <textarea id="txtcomments" name="txtcomments"></textarea>
                    </div>
                </div>
                
                @error('comments') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

                {{-- Submit Buttons --}}
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm @if($button_status == 1) disabled @endif">
                        <i class="fa fa-save"></i> Update <i class="fa fa-spin fa-spinner" wire:loading></i>
                    </button>
                    <a href="{{ route('packing.manage') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
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
                        @foreach(['quo' => 'Quote','po' => 'Purchase Order', 'con' => 'Confirmation','inv' => 'Invoice', 'cre' => 'Credit'] as $value => $label)
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
        </di v>

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

        <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let zIndexCounter = 1050;

                // Initializ  e interact.js for draggable modals
                interact('.d raggable-modal').draggable({
                    allo  wFrom: '.modal-drag-handle',
                    ignoreFrom: 'button, input, a, .btn, [wire\\:click], [wire\\:model]',
                    modifiers: [
                        interact.modifiers.restrictRect({
                            restriction: 'parent',
                            endOnly: true
                        })
                    ],
                    listener  s: {
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

                // Cente    r modals when they appear
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

                // L   ivewire event listeners
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
            // for alert edit 
            document.addEventListener('livewire:load', function () {
                Livewire.on('alert-types-updated', () => {
                    // Force re-render checkboxes
                    document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                        checkbox.checked = @json($this->alertTypes).includes(checkbox.value);
                    });
                });
            });
            document.addEventListener('livewire:init', function () {
                // Force checkbox updates when Livewire finishes rendering
                Livewire.on('alert-types-updated', () => {
                    setTimeout(() => {
                        document.querySelectorAll('[wire\\:model="alertTypes"]').forEach(checkbox => {
                            const shouldBeChecked = @this.alertTypes.includes(checkbox.value);
                            checkbox.checked = shouldBeChecked;
                            checkbox.dispatchEvent(new Event('change'));
                        });
                    }, 50);
                });
            });
        </script>
        
<script>
    let simplemdeComments = null;
    let isInitialized = false;
    let initTimer = null;
    
    function initCommentsEditor() {
        // Clear any pending timer
        if (initTimer) {
            clearTimeout(initTimer);
            initTimer = null;
        }
        
        // Prevent multiple initialization
        if (isInitialized) {
            return;
        }
        
        // Destroy any existing editor
        if (simplemdeComments) {
            try {
                simplemdeComments.toTextArea();
            } catch(e) {}
            simplemdeComments = null;
        }
        
        // Check if textarea is visible and ready
        const textarea = document.getElementById('txtcomments');
        if (!textarea) return;
        
        // Initialize editor
        simplemdeComments = new SimpleMDE({
            element: textarea,
            spellChecker: false,
            toolbar: false, // Disable default toolbar
            status: false,
            lineWrapping: true,
            placeholder: 'Enter comments here...',
            forceSync: true,
        });
        
        isInitialized = true;
        
        // Load existing content
        const existingContent = @json($comments);
        if (existingContent) {
            let displayContent = existingContent.replace(/<br\s*\/?>/gi, '\n');
            simplemdeComments.value(displayContent);
        }
        
        // Setup align buttons
        setupAlignButtons();
        
        // Save on change
        simplemdeComments.codemirror.on('change', function() {
            let rawContent = simplemdeComments.value();
            let contentWithBr = rawContent.replace(/\n/g, '<br />');
            document.getElementById('commentsContent').value = contentWithBr;
            @this.set('comments', contentWithBr);
        });
    }
    
    function setupAlignButtons() {
        const alignButtons = document.querySelectorAll('.custom-align-btn');
        alignButtons.forEach(btn => {
            btn.removeEventListener('click', handleAlignClick);
            btn.addEventListener('click', handleAlignClick);
        });
    }
    
    function handleAlignClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const align = this.dataset.align;
        const cm = simplemdeComments.codemirror;
        const selection = cm.getSelection();
        const cursor = cm.getCursor();
        
        if (selection) {
            const wrapped = `<div style="text-align: ${align};">${selection}</div>`;
            cm.replaceSelection(wrapped);
        } else {
            const line = cm.getLine(cursor.line);
            const wrapped = `<div style="text-align: ${align};">${line}</div>`;
            cm.replaceRange(wrapped, {line: cursor.line, ch: 0}, {line: cursor.line, ch: line.length});
        }
        
        let rawContent = simplemdeComments.value();
        let contentWithBr = rawContent.replace(/\n/g, '<br />');
        document.getElementById('commentsContent').value = contentWithBr;
        @this.set('comments', contentWithBr);
    }
    
    // Single initialization
    document.addEventListener('DOMContentLoaded', function() {
        initTimer = setTimeout(initCommentsEditor, 300);
    });
</script>
</div>