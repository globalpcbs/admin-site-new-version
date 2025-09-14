<div>
      <style>
        .table td, 
.table th {
    white-space: nowrap;   /* keep text on one line */
    vertical-align: middle;
}

.table {
    width: auto;           /* shrink to fit content */
    table-layout: auto;    /* let columns auto-size */
}

      </style>
      @include('includes.flash')
      <div class="card mb-4">
        <div class="card-header fw-bold">Search By</div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Search by Part Number -->
                <div class="col-lg-5">
                    <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        <input type="text" class="form-control" wire:model="searchPartNoInput"
                            placeholder="Enter part number" wire:keydown.enter="searchq" wire:keyup="usekeyupno($event.target.value)" wire:key="searchPartNoInput-{{ now()->timestamp }}" />
                        <button class="btn btn-primary" type="button" wire:click="searchq">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div wire:ignore.self>
                        @if($matches_partno)
                            <ul class="list-group position-absolute w-100 shadow-sm"
                                style="z-index:1050; max-height:220px; overflow-y:auto;">
                                @foreach($matches_partno as $i => $m)
                                    <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                        wire:click="useMatchpn({{ $i }})">
                                        {{ $m['part_no'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Search by Customer Name -->
                <div class="col-lg-5">
                    <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" wire:model="searchCustomerInput"
                            placeholder="Enter customer name" wire:keydown.enter="searchbyCustomer" wire:keyup="onKeyUp($event.target.value)" wire:key="searchCustomerInput-{{ now()->timestamp }}">
                        <button class="btn btn-primary" type="button" wire:click="searchbyCustomer">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div wire:ignore.self>
                        @if($matches)
                            <ul class="list-group position-absolute w-100 shadow-sm"
                                style="z-index:1050; max-height:220px; overflow-y:auto;">
                                @foreach($matches as $i => $m)
                                    <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                        wire:click="useMatch({{ $i }})">
                                        {{ $m['customer'] }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="col-lg-2">
                    <br />
                    <button class="btn btn-info mt-2" wire:click="resetFilters"><i class="fa fa-rotate-right"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Manage Invoice
            <i class="fa fa-spinner fa-spin float-end text-danger" wire:loading></i>
        </div>
        <div>

            <!-- Invoice Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped align-middle table-sm table-responsive">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-key"></i> ID</th>
                            <th><i class="fa fa-hashtag"></i> Inv</th>
                            <th><i class="fa fa-user"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part No</th>
                            <th><i class="fa fa-retweet"></i> Rev</th>
                            <th><i class="fa fa-calendar"></i> Inv Date</th>
                            <th><i class="fa fa-exclamation-circle"></i> Past <br /> Due</th>
                            <th><i class="fa fa-check-square-o"></i> Paid</th>
                            <th><i class="fa fa-envelope-o"></i> Stop <br /> Mails</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_id }}</td>
                            <td>{{ $invoice->invoice_id + 9976 }}</td>
                            <td>{{ optional($invoice->customerRel)->c_shortname ?? $invoice->customer }}</td>
                            <td>{{ $invoice->part_no }}</td>
                            <td>{{ $invoice->rev }}</td>
                            <td>{{ $invoice->podate }}</td>
                            <td>
                                <input type="checkbox" @if ($invoice->pending == 1)
                                checked
                                wire:click="togglePending({{ $invoice->invoice_id }})"
                                onclick="return confirm('Do you want to unmark this invoice as pending?')"
                                @else
                                wire:click="togglePending({{ $invoice->invoice_id }})"
                                @endif
                                >
                            </td>

                            <td>
                                <input type="checkbox" @if ($invoice->ispaid == '1')
                                checked
                                wire:click="togglePaid({{ $invoice->invoice_id }})"
                                onclick="return confirm('Do you want to unmark the invoice as unpaid?')"
                                title="Type: {{ $invoice->paytype }}, Detail: {{ $invoice->paydetail }}, Date:
                                {{ $invoice->paydate }}, Note: {{ $invoice->paynote }}"
                                @else
                                wire:click="openPaymentModal({{ $invoice->invoice_id }})"
                                @endif
                                >
                                @if ($invoice->ispaid == '1')
                                <div class="custom-tooltip shadow">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="fw-bold text-nowrap">Type:</td>
                                            <td>{{ ucwords($invoice->paytype) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Detail:</td>
                                            <td>{{ $invoice->paydetail }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Date:</td>
                                            <td>{{ $invoice->paydate }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold text-nowrap">Note:</td>
                                            <td>{{ $invoice->paynote }}</td>
                                        </tr>
                                    </table>
                                </div>

                                @endif
                            </td>

                            <td>
                                <input type="checkbox" @if ($invoice->mailstop == '1')
                                checked
                                wire:click="toggleMailStop({{ $invoice->invoice_id }})"
                                onclick="return confirm('Do you want to unmark Mail Stop for this invoice?')"
                                @else
                                wire:click="toggleMailStop({{ $invoice->invoice_id }})"
                                @endif
                                >
                            </td>

                            <td>
                                <a href="{{ route('invoice.pdf',$invoice->invoice_id) }}"
                                    class="btn btn-sm btn-outline-info btn-xs"><i class="fa fa-eye"></i> View PDF</a>
                                <a href="{{ route('invoice.pdf.download',$invoice->invoice_id) }}"
                                    class="btn btn-sm btn-outline-danger btn-xs"><i class="fa fa-download"></i> Download PDF</a>
                                <button class="btn btn-sm btn-outline-warning btn-xs"
                                    wire:click="duplicate({{ $invoice->invoice_id }})" wire:key="duplocate-{{ $invoice->invoice_id }}">
                                    <i class="fa fa-copy"> Duplicate </i>
                                </button>
                                <a href="{{ route('invoice.edit',$invoice->invoice_id) }}">
                                    <button class="btn btn-outline-success btn-xs"><i class="fa fa-edit"></i> Edit </button>
                                </a>
                                <button class="btn btn-sm btn-outline-danger btn-xs"
                                    wire:click="delete({{ $invoice->invoice_id }})" wire:confirm wire:key="delete-{{ $invoice->invoice_id }}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="15">No matching invoices found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $invoices->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    @if ($showPaymentModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fa fa-credit-card"></i> Enter Payment Details</h5>
                    <button type="button" class="btn-close" wire:click="$set('showPaymentModal', false)"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Payment Type</label><br>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" wire:model="paytype" value="check"> Check
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" wire:model="paytype" value="wire"> Wire
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" wire:model="paytype" value="transfer">
                            Transfer
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Detail</label>
                        <input type="text" class="form-control" wire:model.defer="paydetail">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" wire:model.defer="paydate" placeholder="mm/dd/yyyy">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Note</label>
                        <input type="text" class="form-control" wire:model.defer="paynote">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('showPaymentModal', false)">Cancel</button>
                    <button class="btn btn-primary" wire:click="savePayment">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif



</div>