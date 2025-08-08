<div>
    @include('includes.flash')
    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header fw-bold">Search By</div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search by Part Number -->
                    <div class="col-lg-5">
                        <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                            <input type="text" class="form-control" wire:model.debounce.500ms="searchPartNo"
                                placeholder="Enter part number">
                            <button class="btn btn-primary" type="button" wire:click="$refresh">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Search by Customer Name -->
                    <div class="col-lg-5">
                        <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" wire:model.debounce.500ms="searchCustomer"
                                placeholder="Enter customer name">
                            <button class="btn btn-primary" type="button" wire:click="$refresh">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> Manage Quotes
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm font-xs table-hover table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th><i class="fa fa-hashtag"></i> ID</th>
                        <th><i class="fa fa-file-text-o"></i> Quote #</th>
                        <th><i class="fa fa-user"></i> Customer Name</th>
                        <th><i class="fa fa-cube"></i> Part No</th>
                        <th><i class="fa fa-refresh"></i> Rev</th>
                        <th><i class="fa fa-calendar"></i> Added Date</th>
                        <th><i class="fa fa-download"></i> Downloads</th>
                        <th><i class="fa fa-cogs"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotes as $quote)
                    <tr>
                        <td>{{ $quote->ord_id }}</td>
                        <td>{{ $quote->ord_id + 10000 }}</td>
                        <td>{{ $quote->cust_name }}</td>
                        <td class="position-relative">
                            @php
                            $alerts = \App\Models\alerts_tb::where('part_no', $quote->part_no)
                            ->where('rev', $quote->rev)
                            ->where('customer', $quote->cust_name)
                            ->get();
                            @endphp

                            @if($alerts->count() > 0)
                            <!-- Hoverable Part Number -->
                            <a href="javascript:void(0)" class="text-danger fw-bold"
                                onmouseover="document.getElementById('alert-box-{{ $quote->ord_id }}').style.display='block'"
                                onmouseout="document.getElementById('alert-box-{{ $quote->ord_id }}').style.display='none'">
                                {{ $quote->part_no }}
                            </a>

                            <!-- Custom Tooltip Box -->
                            <div id="alert-box-{{ $quote->ord_id }}" style="
                 display:none;
                 position:absolute;
                 top:-10px;
                 left:150px;
                 background:#fff8f8;
                 border:1px solid #c33;
                 box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                 width:220px;
                 padding:10px;
                 z-index:1000;
                 border-radius:8px;
             ">
                                <h6 style="color:#c33; font-weight:bold; margin-bottom:8px;">Alerts</h6>
                                @foreach($alerts as $index => $alert)
                                <div style="font-size:13px; color:#333;">
                                    {{ $index + 1 }}. {{ $alert->alert }}
                                </div>
                                @endforeach
                            </div>
                            @else
                            {{ $quote->part_no }}
                            @endif
                        </td>


                        <td>{{ $quote->rev }}</td>
                        <td>{{ \Carbon\Carbon::parse($quote->ord_date)->format('m/d/Y') }}</td>
                        <td>
                            <a href="{{ route('download.downloadPdfqoute',$quote->ord_id)}}"
                                class="btn btn-primary btn-xs btn-sm">Download PDF</a>
                            <a href="{{ route('view.viewPdfqoute',$quote->ord_id)}}"
                                class="btn btn-info btn-xs btn-sm" target="_blank">VIEW PDF</a>
                            <a href="{{ route('downloaddoc.viewdocqoute',$quote->ord_id)}}"
                                class="btn btn-warning btn-xs btn-sm">Download DOC</a>
                        </td>
                        <td>
                            <a href="{{ route('qoutes.edit',$quote->ord_id) }}" class="btn btn-success btn-xs btn-sm"><i
                                    class="fa fa-edit"></i></a>
                            <button wire:click="deleteQuote({{ $quote->ord_id }})"
                                onclick="confirm('Are you sure you want to delete this quote?') || event.stopImmediatePropagation()"
                                class="btn btn-sm btn-xs btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                            <button wire:click="duplicateQuote({{ $quote->ord_id }})"
                                class="btn btn-xs btn-sm btn-primary">
                                <i class="fa fa-copy"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $quotes->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>