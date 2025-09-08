<div>
    <div>
        @include('includes.flash')
        <div class="card mb-3">
            <div class="row g-3 align-items-end card-body">
                <div class="col-md-2">
                    <label for="from" class="form-label">
                        <i class="fa fa-calendar"></i> From
                    </label>
                    <input type="date" class="form-control" wire:model.defer="from" id="from">
                </div>

                <div class="col-md-2">
                    <label for="to" class="form-label">
                        <i class="fa fa-calendar"></i> To
                    </label>
                    <input type="date" class="form-control" wire:model.defer="to" id="to">
                </div>

                <div class="col-md-2">
                    <label for="partNumber" class="form-label">
                        <i class="fa fa-cube"></i> Part Number
                    </label>
                    <input type="text" class="form-control" wire:model.defer="partNumber" id="partNumber">
                </div>

                <div class="col-md-3">
                    <label for="customerName" class="form-label">
                        <i class="fa fa-user"></i> Customer Name
                    </label>
                    <input type="text" class="form-control" wire:model.defer="customerName" id="customerName">
                </div>

                <div class="col-md-2">
                    <label for="vendorName" class="form-label">
                        <i class="fa fa-industry"></i> Vendor Name
                    </label>
                    <input type="text" class="form-control" wire:model.defer="vendorName" id="vendorName">
                </div>
            </div>

            <div class="card-footer">
                <div class="float-end">
                    <button wire:click="refreshData" class="btn btn-primary btn-sm">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button wire:click="resetFilters" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <i class="fa fa-bar-chart"></i> Status Report
                <i class="fa fa-spinner fa-spin float-end text-danger" wire:loading></i>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table table-bordered table-responsive table-sm font-xs3 table-striped table-hover">
                        <thead class="table-primary text-white">
                            <tr>
                                <th>Ord#</th>
                                <th>Customer</th>
                                <th>P/N</th>
                                <th>Rev</th>
                                <th>Due Date</th>
                                <th>Customer PO</th>
                                <th>Our PO</th>
                                <th>Vendor</th>
                                <th>WT</th>
                                <th>Inv. #</th>
                                <th>Invoiced on</th>
                                <th>Credit#</th>
                                <th>Expected Dock</th>
                                <th>Cust. D/D</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->poid }}</td>
                                @php
                                $customer = \App\Models\data_tb::where('c_name', $order->customer)->first();
                                @endphp

                                <td style="position: relative;">
                                    @if($customer)
                                    <a href="javascript:void(0);" class="ttip_trigger">
                                        {{ $customer->c_shortname }}
                                    </a>

                                    <div class="ttip_overlay bg-light p-3 border shadow"
                                        style="position: absolute; top: 100%; left: 0; width: 300px; display: none; z-index: 9999;">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-2">{{ $customer->c_name }}</h5>
                                            <a href="javascript:void(0);" class="ttip_close text-danger">×</a>
                                        </div>
                                        <p class="mb-0">
                                            @if ($customer->c_address)
                                            {{ $customer->c_address }}<br>
                                            @endif
                                            @if ($customer->c_address2 || $customer->c_address3)
                                            {{ $customer->c_address2 }} {{ $customer->c_address3 }}<br>
                                            @endif
                                            @if ($customer->c_phone)
                                            Phone: {{ $customer->c_phone }}<br>
                                            @endif
                                            @if ($customer->c_fax)
                                            Fax: {{ $customer->c_fax }}<br>
                                            @endif
                                        </p>
                                    </div>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="javascript:void(0);" wire:click="openModal({{ $order->poid }})">
                                        {{ $order->part_no }}
                                    </a>
                                </td>
                                <td>{{ $order->rev }}</td>
                                <td style="position: relative;">
                                    <a href="javascript:void(0);" class="ttip_trigger">
                                        {{ $order->dweek }}
                                    </a>

                                    <div class="ttip_overlay bg-light p-3 border shadow"
                                        style="position: absolute; top: 100%; left: 0; width: 300px; display: none; z-index: 9999;">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-2">Note:</h6>
                                            <a href="javascript:void(0);" class="ttip_close text-danger">×</a>
                                        </div>
                                        <p class="mb-0">{{ $order->note }}</p>
                                    </div>
                                </td>
                                <td>{{ $order->po }}</td>
                                <td>
                                    <a href="{{ route('purchase.orders.edit',$order->poid) }}" target="_blank">
                                        {{ $order->poid+9933 }}
                                    </a>
                                </td>
                                <td>{{ $order->vc }}</td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkbox{{ $order->poid }}"
                                            value="{{ $order->poid }}" @checked($order->allow === 'true')
                                        wire:click="toggleOrder('{{ $order->poid }}', $event.target.checked)"
                                        >
                                    </div>
                                </td>
                                <td>
                                    @if($order->invoice_id)
                                    <a href="{{ route('invoice.edit',['id' => $order->invoice_id]) }}" target="_blank">
                                        {{ $order->invoice_id + 9976 }}
                                    </a>
                                    @else
                                    {{ $order->invoice_id + 9976 }}
                                    @endif
                                </td>
                                <td>{{ $order->invoicedon }}</td>
                                <td class="text-center" width="8%">
                                    @php
                                    $credit = \App\Models\credit_tb::where('inv_id', $order->invoice_id +
                                    9976)->first();
                                    @endphp

                                    @if ($credit)
                                    {{ $credit->credit_id + 10098 }}
                                    <a href="{{ route('credit.pdf',$credit->credit_id) }}" target="_blank">V</a>
                                    <a href="{{ route('credit.edit',$credit->credit_id) }}" target="_blank">E</a>
                                    <a href="{{ route('credit.pdf.download',$credit->credit_id) }}">D</a>
                                    @endif
                                </td>
                                <td>{{ $order->supli_due }}</td>
                                <td>{{ $order->cus_due }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-xs rounded-0"
                                        wire:click="openNoteModal({{ $order->poid }})">
                                        Note
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                <div class="text-end mt-2">
                    <small class="text-muted">Total: {{ count($orders) }} record(s)</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Update Due Dates (POID: {{ $selectedPoid }})
                    <button type="button" class="btn-close btn-sm btn-xs" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateDueDates">
                        <div class="mb-3">
                            <label class="form-label">Customer Due Date</label>
                            <input type="date" class="form-control" wire:model.defer="cus_due">
                            @error('cus_due') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Supplier Due Date</label>
                            <input type="date" class="form-control" wire:model.defer="sup_due">
                            @error('sup_due') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($showNoteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Edit Note (POID: {{ $poidForNote }})
                    <button type="button" class="btn-close btn-sm btn-xs"
                        wire:click="$set('showNoteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <textarea wire:model.defer="note" class="form-control" rows="5"
                        placeholder="Enter note..."></textarea>
                </div>
                <div class="modal-footer">
                    <button wire:click="saveNote" class="btn btn-primary">Save Note</button>
                    <button wire:click="$set('showNoteModal', false)" class="btn btn-secondary">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif


</div>