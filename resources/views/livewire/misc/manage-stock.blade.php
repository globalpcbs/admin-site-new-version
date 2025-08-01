<div>
    @include('includes.flash')
    <div class="card">
        <div class="card-header">
            <i class="fa fa-search"></i> Search Here
            <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input wire:model.defer="search" class="form-control" placeholder="Search by Part Number">
                </div>
                <div class="col-md-4">
                    <input wire:model.defer="searchCustomer" class="form-control" placeholder="Search by Customer">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary" wire:click="performSearch">
                        <i class="fa fa-search me-1"></i> Search
                    </button>
                    <button class="btn btn-secondary" wire:click="resetFilters">
                        <i class="fa fa-times me-1"></i> Reset
                    </button>
                </div>
            </div>

        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-list"></i> Manage Stock
        </div>
        <div>
            <table class="table table-bordered table-sm font-xs table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Stk#</th>
                        <th>Customer</th>
                        <th>Part No</th>
                        <th>Rev</th>
                        <th>Supplier</th>
                        <th>Date Add.</th>
                        <th>D/C</th>
                        <th>Finish</th>
                        <th>Mfg Date</th>
                        <th>Docs<br>Ready</th>
                        <th width="40">Stock Qty</th>
                        <th width="40">Remaining Qty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stocks as $s)
                    @php
                    $rowbg = '';
                    $mdate = '';

                    if (!empty($s->manuf_dt)) {
                    $mdt = explode('-', $s->manuf_dt);
                    if (count($mdt) === 3) {
                    $mdate = $mdt[0] . '-' . $mdt[2];
                    $timestamp = strtotime($mdt[2] . '-' . $mdt[0] . '-' . $mdt[1]);
                    $daysOld = (time() - $timestamp) / (3600 * 24);
                    if (
                    ($s->finish == 'HASL' && $daysOld > 170) ||
                    (in_array($s->finish, ['ENIG', 'ENEPIG']) && $daysOld > 350)
                    ) {
                    $rowbg = 'table-danger';
                    }
                    }
                    }
                    @endphp
                    <tr class="{{ $rowbg }}">
                        <td>{{ $s->stkid }}</td>
                        <td>{{ $s->customer }}</td>
                        <td class="ctr">
                            @php
                            $comment = $s->comments;
                            $aflag = strlen($comment);
                            @endphp

                            @if ($comment != '')
                            <div style="position: relative; clear: both">
                                <div class="ttip_overlay" id="div_{{ $s->stkid }}" style="z-index: 1000;
                        padding: 0px 10px 10px;
                        background: rgb(255, 238, 238);
                        border: 1px solid rgb(51, 102, 153);
                        position: absolute;
                        top: -10px;
                        left: 150px;
                        text-align: left;
                        width: 200px;
                        margin-top: -50px;
                        display: none;">
                                    <h6 class="fw-bold">Comment</h6>
                                    {!! nl2br(e($comment)) !!}
                                </div>
                            </div>
                            @endif

                            @if ($aflag > 1)
                            <a href="javascript:void(0)" class="dalerts text-decoration-underline text-primary"
                                data-stkid="{{ $s->stkid }}">
                                {{ $s->part_no }}
                            </a>
                            @else
                            {{ $s->part_no }}
                            @endif
                        </td>

                        <td>{{ $s->rev }}</td>
                        <td>{{ $s->vendor->c_name ?? '-' }}</td>
                        <td>{{ substr($s->dtadded, -10) }} </td>
                        <td>{{ $s->dc }}</td>
                        <td>{{ $s->finish }}</td>
                        <td>{{ $s->manuf_dt }}</td>
                        <td>{{ $s->docs_ready ? 'Yes' : 'No' }}</td>
                        <td>{{ $s->qty }}</td>
                        <td>
                            @php
                            // Fetch allocated quantity (undelivered)
                            $remaining_qut = 0;
                            $allocation = DB::select("SELECT SUM(qut) AS qut FROM stock_allocation WHERE stock_id = ?
                            AND delivered_on = '00-00-0000'", [$s->stkid]);

                            if (!empty($allocation[0]->qut)) {
                            $remaining_qut = $s->qty - $allocation[0]->qut;
                            } else {
                            $remaining_qut = $s->qty;
                            }

                            // Get allocation rows (undelivered)
                            $allocations = DB::select("SELECT * FROM stock_allocation WHERE stock_id = ? AND
                            delivered_on = '00-00-0000'", [$s->stkid]);
                            @endphp

                            @if(count($allocations) > 0)
                            <div style="position: relative; clear: both">
                                <div class="ttip_overlay" id="aldiv_{{ $s->stkid }}" style="z-index: 1000;
                                padding: 0px 10px 10px;
                                background: rgb(255, 238, 238);
                                border: 1px solid rgb(51, 102, 153);
                                position: absolute;
                                top: -50px;
                                left: -290px;
                                text-align: left;
                                width: 270px;
                                margin-top: -40px;
                                display: none;">
                                    <label>Stock Allocation</label>
                                    <table class="al_tb table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>PO#</th>
                                                <th>Qty</th>
                                                <th>Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allocations as $allocation)
                                            <tr>
                                                <td>{{ $allocation->customer }}</td>
                                                <td>{{ $allocation->pono }}</td>
                                                <td>{{ $allocation->qut }}</td>
                                                <td>{{ \Carbon\Carbon::parse($allocation->due_date)->format('m-d-Y') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <label>Comment</label>
                                    {!! nl2br(e($s->comments)) !!}
                                </div>
                            </div>
                            @endif

                            @if($s->qty > $remaining_qut)
                            <a href="javascript:void(0)" class="allocations" id="p{{ $s->stkid }}"
                                style="color:red;">{{ $remaining_qut }}</a>
                            @else
                            {{ $remaining_qut }}
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('misc.edit.stock',$s->stkid) }}" class="btn btn-sm btn-xs btn-info">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-xs btn-danger" wire:click="delete({{ $s->stkid }})">
                                <i class="fa fa-trash"></i>
                            </button>
                            <button class="btn btn-sm btn-xs btn-secondary" wire:click="duplicate({{ $s->stkid }})">
                                <i class="fa fa-copy"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach

                    @if ($stocks->isEmpty())
                    <tr>
                        <td colspan="15" class="text-center">No stock found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{ $stocks->links() }}
        </div>
    </div>
</div>