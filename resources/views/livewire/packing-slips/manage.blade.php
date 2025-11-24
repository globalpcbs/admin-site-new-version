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
    
    <div class="card mb-4">
        <div class="card-header fw-bold">Search By</div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Search by Part Number -->
                <div class="col-lg-5">
                    <label><i class="fa fa-cogs"></i> Search by Part Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                        <input type="text" class="form-control" wire:model="searchPartNoInput"
                            placeholder="Enter part number" wire:keydown.enter="searchq" wire:keyup="usekeyupno($event.target.value)" wire:key="searchPartNoInput-{{ now()->timestamp }}" />
                        <button class="btn btn-primary" type="button" wire:click="searchq">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div wire:ignore.self>
                        @if(!empty($matches_partno))
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
                    <label><i class="fa fa-user"></i> Search by Customer Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                        <input type="text" class="form-control" wire:model="searchCustomerInput"
                            placeholder="Enter customer name" wire:keydown.enter="searchbyCustomer" wire:keyup="onKeyUp($event.target.value)" wire:key="searchCustomerInput-{{ now()->timestamp }}">
                        <button class="btn btn-primary" type="button" wire:click="searchbyCustomer">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div wire:ignore.self>
                        @if(!empty($matches))
                            <ul class="list-group position-absolute w-100 shadow-sm"
                                style="z-index:1050; max-height:220px; overflow-y:auto;">
                                @foreach($matches as $i => $m)
                                    <li wire:key="match-{{ $i }}" class="list-group-item list-group-item-action"
                                        wire:click="useMatch({{ $i }})">
                                        {{ $m['c_name'] }}
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
            <i class="fa fa-list"></i> Manage Packing Slips
            <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped align-middle btn-sm text-nowrap">
                <thead class="table-light text-center">
                    <tr>
                        <th><i class="fa fa-id-badge"></i> Slip ID</th>
                        <th><i class="fa fa-list-ol"></i> Slip #</th>
                        <th><i class="fa fa-user"></i> Customer</th>
                        <th><i class="fa fa-cube"></i> Part No</th>
                        <th><i class="fa fa-refresh"></i> Rev</th>
                        <th><i class="fa fa-calendar"></i> Packing Date</th>
                        <th>Action</th>
                        <th>Invoiced</th>
                        <th>Logged</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packingSlips as $slip)
                    <tr>
                        <td>{{ $slip->invoice_id ?? 'N/A' }}</td>
                        <td>{{ $slip->invoice_id+9987 ?? 'N/A' }}</td>
                        <td>{{ $slip->custo->c_name ?? 'Customer Not Found' }}</td>
                        <td>{{ $slip->part_no ?? 'N/A' }}</td>
                        <td>{{ $slip->rev ?? 'N/A' }}</td>
                        <td>
                            {{ $slip->podate ? \Carbon\Carbon::parse($slip->podate)->format('m/d/Y') : 'N/A' }}
                        </td>
                        <td>
                            <a href="{{ route('packing.edit',$slip->invoice_id) }}">
                                <button type="button" class="btn btn-sm btn-xs btn-primary">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                            </a>
                            <a href="https://files.pcbsglobal.website/download-pdf3.php?id={{ $slip->invoice_id }}&oper=download">
                                <button type="button" class="btn btn-sm btn-xs btn-success">
                                    <i class="fa fa-download"></i>  pdf
                                </button>
                            </a>
                            <a href="https://files.pcbsglobal.website/download-pdf3.php?id={{ $slip->invoice_id }}&oper=view" target="_blank">
                                <button type="button" class="btn btn-sm btn-xs btn-info">
                                    <i class="fa fa-eye"></i> Pdf
                                </button>
                            </a>
                            <a href="https://files.pcbsglobal.website/download-doc3.php?id={{ $slip->invoice_id }}">
                                <button type="button" class="btn btn-sm btn-xs btn-warning">
                                    <i class="fa fa-download"></i> Doc
                                </button>
                            </a>
                            <button type="button" class="btn btn-sm btn-xs btn-danger"
                                wire:click="confirmDelete({{ $slip->invoice_id }})" wire:key="delete-{{ $slip->invoice_id }}">
                                <i class="fa fa-trash"></i> Del
                            </button>
                            <button type="button" class="btn btn-sm btn-xs btn-secondary"
                                wire:click="duplicate({{ $slip->invoice_id }})" wire:key="duplicate-{{ $slip->invoice_id }}">
                                <i class="fa fa-copy"></i> Duplicate
                            </button>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" wire:click="togglePending({{ $slip->invoice_id }})"
                                {{ $slip->pending == 'Yes' ? 'checked' : '' }} onclick="if (!this.checked) {
                const confirmed = confirm('Are you sure you want to unmark this as pending?');
                if (!confirmed) event.preventDefault();
            }" class="form-check-input mt-0">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input mt-0"
                                wire:change="isLogged({{ $slip->invoice_id }})">
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="14" class="text-center">No Packing Slips Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $packingSlips->links('pagination::bootstrap-5') }}
        </div>
    </div>
    
    @if ($confirmingDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this packing slip?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                    <button class="btn btn-danger" wire:click="deletePackingSlip({{ $deleteId }})">
                        <i class="fa fa-trash"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>