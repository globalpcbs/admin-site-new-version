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
    <div class="container py-4">
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

        <!-- 💳 card with table -->
        <div class="card shadow-sm">
            <div class="card-header bg-light fw-bold">
                <i class="fa fa-credit-card"></i> Credit Records
                <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                @foreach ([
        'credit_id' => '#',
        'Credit_no' => 'credit #',
        'podate' => 'Date',
        'customer' => 'Customer',
        'part_no' => 'Part #',
        'rev' => 'Rev',
    ] as $field => $label)
                                    <th wire:click="sortBy('{{ $field }}')" style="cursor:pointer">
                                        {{ $label }}
                                        @if ($sortField === $field)
                                            <span wire:loading.remove>
                                                {{ $sortDirection === 'asc' ? '▲' : '▼' }}
                                            </span>
                                        @endif
                                    </th>
                                @endforeach
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($credits as $credit)
                                <tr>
                                    <td>{{ $credit->credit_id }}</td>
                                    <td>{{ $credit->credit_id + 10098 }}</td>
                                    <td>{{ $credit->podate }}</td>
                                    <td>{{ $credit->customer }}</td>
                                    <td>{{ $credit->part_no }}</td>
                                    <td>{{ $credit->rev }}</td>
                                    <td>
                                        <a href="{{ route('credit.pdf.download', $credit->credit_id) }}" target="_blank"
                                            class="btn btn-sm btn-danger btn-xs">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                        <a href="{{ route('credit.pdf', $credit->credit_id) }}" target="_blank"
                                            class="btn btn-sm btn-warning btn-xs">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <button class="btn btn-sm btn-info btn-xs"
                                            wire:click="duplicateRecord({{ $credit->credit_id }})" wire:key="duplicate-{{ $credit->credit_id }}">
                                            <i class="fa fa-clone"></i> Duplicate
                                        </button>
                                        <a href="{{ route('credit.edit', $credit->credit_id) }}" class="btn btn-sm btn-xs btn-success">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <button class="btn btn-sm btn-xs btn-danger"
                                            wire:click="confirmDelete({{ $credit->credit_id }})" wire:key="delete-{{ $credit->credit_id }}">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                {{ $credits->links('pagination::bootstrap-5') }}
            </div>
        </div>

        <!-- ⚠️ delete-confirmation modal -->
        @if ($confirmingDelete)
            <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fa fa-exclamation-triangle"></i>
                                Confirm Deletion
                            </h5>
                            <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                        </div>

                        <div class="modal-body">
                            <p class="mb-0">
                                Delete record for
                                <strong>{{ $delCustomer }}</strong>,
                                part <strong>{{ $delPart }}</strong>,
                                rev <strong>{{ $delRev }}</strong>?
                            </p>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">
                                Cancel
                            </button>

                            <button class="btn btn-danger" wire:click="deleteGroup">
                                <i class="fa fa-trash"></i> Confirm Delete
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    </div>
</div>