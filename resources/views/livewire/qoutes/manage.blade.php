<div>
    @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" id="successAlert">
                <i class="fa fa-check-square"></i>  {{ session('success') }}
            </div>
            
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('successAlert');
                    alert.classList.remove('show');
                    setTimeout(() => alert.style.display = 'none', 150);
                }, 3000);
            </script>
    @endif
    @if($alertMessage)
        <div class="container mt-2">
            <div class="alert alert-{{ $alertType }}" 
                x-data="{ show: true }" 
                x-show="show"
                x-init="setTimeout(() => { show = false; $wire.dispatch('alert-hidden') }, 3000)">
                <i class="fa fa-{{ $alertType == 'success' ? 'check' : 'times' }}-circle"></i> 
                {{ $alertMessage }}
            </div>
        </div>
    @endif

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
                            <input type="text" class="form-control" wire:model="searchPartNoInput"
                                placeholder="Enter part number" 
                                wire:keydown.enter="searchq"
                                wire:keyup="usekeyupno($event.target.value)" 
                                wire:key="searchPartNoInput-{{ now()->timestamp }}" />
                            <button class="btn btn-primary" type="button" wire:click="searchq">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div wire:ignore.self>
                            @if($matches_partno && count($matches_partno) > 0)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches_partno as $i => $m)
                                        <li wire:key="match-partno-{{ $i }}" class="list-group-item list-group-item-action"
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
                                placeholder="Enter customer name" 
                                wire:keydown.enter="searchbyCustomer"
                                wire:keyup="onKeyUp($event.target.value)" 
                                wire:key="searchCustomerInput-{{ now()->timestamp }}">
                            <button class="btn btn-primary" type="button" wire:click="searchbyCustomer">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div wire:ignore.self>
                            @if($matches && count($matches) > 0)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches as $i => $m)
                                        <li wire:key="match-customer-{{ $i }}" class="list-group-item list-group-item-action"
                                            wire:click="useMatch({{ $i }})">
                                            {{ $m['cust_name'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <br />
                        <button class="btn btn-info mt-2" wire:click="filterclose">
                            <i class="fa fa-rotate-right"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> Manage Quotes
                <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover table-striped">
                <thead>
                    <tr>
                        <th><i class="fa fa-hashtag"></i> ID</th>
                        <th><i class="fa fa-file-text-o"></i> Quote #</th>
                        <th><i class="fa fa-user"></i> Customer Name</th>
                        <th><i class="fa fa-cube"></i> Part No</th>
                        <th><i class="fa fa-refresh"></i> Rev</th>
                        <th><i class="fa fa-calendar"></i> Added Date</th>
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
                            <a href="https://globalpcbs.com/admin/download-pdf.php?id={{ $quote->ord_id }}&oper=view"
                                class="btn btn-primary btn-xs btn-sm">Download PDF</a>
                            <a href="{{ route('view.viewPdfqoute',$quote->ord_id)}}"
                                class="btn btn-info btn-xs btn-sm" target="_blank">VIEW PDF</a>
                            <a href="{{ route('downloaddoc.viewdocqoute',$quote->ord_id)}}"
                                class="btn btn-warning btn-xs btn-sm">Download DOC</a>
                            <a href="{{ route('qoutes.edit',$quote->ord_id) }}" class="btn btn-success btn-xs btn-sm">
                                <i class="fa fa-edit"></i> Edit 
                            </a>
                            <button wire:click="deleteQuote({{ $quote->ord_id }})" 
                                wire:key="delete-{{ $quote->ord_id }}"
                                onclick="confirm('Are you sure you want to delete this quote?') || event.stopImmediatePropagation()"
                                class="btn btn-sm btn-xs btn-danger">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                            <button wire:click="duplicateQuote({{ $quote->ord_id }})" 
                                wire:key="duplicate-{{ $quote->ord_id }}"
                                class="btn btn-xs btn-sm btn-primary">
                                <i class="fa fa-copy"></i> Duplicate
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