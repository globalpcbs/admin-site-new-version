<div>
    @if($alertMessage)
        <div 
            class="alert alert-{{ $alertType }} shadow"
            style="
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
            "
            x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => { show = false; $wire.dispatch('alert-hidden') }, 3000)"
        >
            <i class="fa fa-{{ $alertType == 'success' ? 'check' : 'times' }}-circle"></i> 
            {{ $alertMessage }}
        </div>
    @endif

    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header fw-bold">Search By</div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Search by Part Number -->
                    <div class="col-lg-5" 
                         x-data="{
                             partNo: '',
                             updateMatches() {
                                 if (this.partNo.length >= 2) {
                                     @this.usekeyupno(this.partNo)
                                 } else {
                                     @this.set('matches_partno', [])
                                 }
                             },
                             search() {
                                 let val = this.partNo;
                                 if (val.trim() === '') return;
                                 @this.set('searchPartNoInput', val).then(() => {
                                     @this.searchq();
                                 });
                                 this.partNo = ''; // clear own input
                             },
                             selectSuggestion(value) {
                                 this.partNo = value;
                                 this.search();
                             }
                         }"
                         x-on:clear-part-search.window="partNo = ''">
                        <label><i class="fa fa-cogs"></i> Search by Part Number:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                            <input type="text" class="form-control" x-model="partNo"
                                   placeholder="Enter part number"
                                   @keyup="updateMatches"
                                   @keydown.enter="search">
                            <button class="btn btn-primary" type="button" @click="search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div>
                            @if($matches_partno && count($matches_partno) > 0)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches_partno as $i => $m)
                                        <li wire:key="match-partno-{{ $i }}"
                                            class="list-group-item list-group-item-action"
                                            x-on:click="selectSuggestion('{{ $m['part_no'] }}')">
                                            {{ $m['part_no'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Search by Customer Name -->
                    <div class="col-lg-5"
                         x-data="{
                             customerName: '',
                             updateMatches() {
                                 if (this.customerName.length >= 2) {
                                     @this.onKeyUp(this.customerName)
                                 } else {
                                     @this.set('matches_customer', [])
                                 }
                             },
                             search() {
                                 let val = this.customerName;
                                 if (val.trim() === '') return;
                                 @this.set('searchCustomerInput', val).then(() => {
                                     @this.searchbyCustomer();
                                 });
                                 this.customerName = ''; // clear own input
                             },
                             selectSuggestion(value) {
                                 this.customerName = value;
                                 this.search();
                             }
                         }"
                         x-on:clear-customer-search.window="customerName = ''">
                        <label><i class="fa fa-user"></i> Search by Customer Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" class="form-control" x-model="customerName"
                                   placeholder="Enter customer name"
                                   @keyup="updateMatches"
                                   @keydown.enter="search">
                            <button class="btn btn-primary" type="button" @click="search">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div>
                            @if($matches_customer && count($matches_customer) > 0)
                                <ul class="list-group position-absolute w-100 shadow-sm"
                                    style="z-index:1050; max-height:220px; overflow-y:auto;">
                                    @foreach($matches_customer as $i => $m)
                                        <li wire:key="match-customer-{{ $i }}"
                                            class="list-group-item list-group-item-action"
                                            x-on:click="selectSuggestion('{{ $m['customer'] }}')">
                                            {{ $m['customer'] }}
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

        <!-- 📋 Results table (unchanged) -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                Manage Part‑Number Alerts - testing
                <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-hashtag"></i> #</th>
                            <th><i class="fa fa-user"></i> Customer</th>
                            <th><i class="fa fa-cube"></i> Part / Rev</th>
                            <th><i class="fa fa-bell"></i> Alerts</th>
                            <th class="text-center"><i class="fa fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alerts as $index => $row)
                        <tr wire:key="alert-row-{{ $row->first_id }}">
                            <td class="text-center">
                                {{ ($alerts->currentPage() - 1) * $alerts->perPage() + $index + 1 }}.
                            </td>

                            <td>{{ $row->customer }}</td>

                            <td>{{ $row->part_no }} / {{ $row->rev }}</td>

                            <td>
                                @foreach(explode("\n", $row->alerts) as $alertIndex => $alert)
                                    @if(trim($alert) !== '')
                                        <strong>{{ $alertIndex + 1 }}.</strong> {{ $alert }}<br />
                                    @endif
                                @endforeach
                            </td>

                            <td class="text-center">
                                <a href="{{ route('customers.alerts.edit', [
                                    'customer' => $row->customer,
                                    'part'     => $row->part_no,
                                    'rev'      => $row->rev ?? '',
                                ]) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <button class="btn btn-sm btn-outline-danger" 
                                    wire:confirm="Are you sure you want to delete all alerts for {{ $row->customer }}, part {{ $row->part_no }}, rev {{ $row->rev }}?"
                                    wire:click="deleteGroup('{{ $row->customer }}', '{{ $row->part_no }}', '{{ $row->rev }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">No records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-center">
                {{ $alerts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>