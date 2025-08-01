<div>
    @include('includes.flash')
    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-2" wire:submit.prevent>
                <div class="col-md-4">
                    <label class="form-label fw-bold"> <i class="fa fa-search"></i> Search By Part Number</label>
                    <div class="input-group">
                        <input type="text" wire:model.defer="partSearchInput" class="form-control"
                            placeholder="Enter Part Number">
                        <button type="button" wire:click="searchByPartNo" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold"> <i class="fa fa-search"></i> Search By Customer Name</label>
                    <div class="input-group">
                        <input type="text" wire:model.defer="customerSearchInput" class="form-control"
                            placeholder="Enter Customer Name">
                        <button type="button" wire:click="searchByCustomer" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-secondary" wire:click="clearFilters">
                        <i class="fa fa-times-circle"></i> Reset Filters
                    </button>
                </div>
            </form>

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
                <table class="table table-bordered table-hover table-striped align-middle table-sm font-xs">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-key"></i> ID</th>
                            <th><i class="fa fa-hashtag"></i> Inv</th>
                            <th><i class="fa fa-user"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part No</th>
                            <th><i class="fa fa-retweet"></i> Rev</th>
                            <th><i class="fa fa-calendar"></i> Inv Date</th>
                            <th><i class="fa fa-file-pdf-o"></i> PDF</th>
                            <th><i class="fa fa-file-word-o"></i> DOC</th>
                            <th><i class="fa fa-copy"></i> Clone</th>
                            <th><i class="fa fa-exclamation-circle"></i> Past Due</th>
                            <th><i class="fa fa-check-square-o"></i> Paid</th>
                            <th><i class="fa fa-envelope-o"></i> Stop Mails</th>
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
                                <a href="{{ route('invoice.pdf',$invoice->invoice_id) }}"
                                    class="btn btn-sm btn-outline-info btn-xs"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('invoice.pdf.download',$invoice->invoice_id) }}"
                                    class="btn btn-sm btn-outline-danger btn-xs"><i class="fa fa-download"></i></a>
                            </td>

                            <td>
                                <a href="{{ route('invoice.docs.download',$invoice->invoice_id) }}"
                                    class="btn btn-sm btn-outline-primary btn-xs"><i class="fa fa-file"></i></a>
                            </td>

                            <td>
                                <button class="btn btn-sm btn-outline-warning btn-xs"
                                    wire:click="duplicate({{ $invoice->invoice_id }})">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </td>

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
                                <a href="{{ route('invoice.edit',$invoice->invoice_id) }}">
                                    <button class="btn btn-outline-success btn-xs"><i class="fa fa-edit"></i></button>
                                </a>
                                <button class="btn btn-sm btn-outline-danger btn-xs"
                                    onclick="if(confirm('Are you sure you want to delete this invoice?')) @this.delete({{ $invoice->invoice_id }})">
                                    <i class="fa fa-trash"></i>
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
                        <input type="text" class="form-control" wire:model.defer="paydate" placeholder="mm/dd/yyyy">
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