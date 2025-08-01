<div>
    <div class="card">
        <div class="card-header bg-white border-bottom">
            <i class="fa fa-bar-chart"></i> Invoice list for Sales Rep
        </div>

        <div class="card-body">
            {{-- Sales Rep Dropdown --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="selectedRep" class="form-label fw-bold">Select Rep:</label>
                    <select wire:model="selectedRep" id="selectedRep" class="form-select">
                        <option value="">Select</option>
                        @foreach ($reps as $rep)
                        <option value="{{ $rep->repid }}">{{ $rep->r_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th colspan="8">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    @if ($selectedRep)
                                    Invoice list for {{ $reps->firstWhere('repid', $selectedRep)->r_name }}
                                    @else
                                    Select a Sales Rep to view invoices
                                    @endif
                                </span>
                                <input type="text" wire:model.debounce.500ms="search" class="form-control w-25"
                                    placeholder="Search invoices" @if (!$selectedRep) disabled @endif>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>Invoice #</th>
                        <th>Customer</th>
                        <th>Part No</th>
                        <th>Invoice Date</th>
                        <th>Commission%</th>
                        <th>Commission Due</th>
                        <th>Commission Paid On</th>
                        <th>Select</th>
                    </tr>
                </thead>

                <tbody>
                    @if ($selectedRep)
                    @forelse ($invoices as $inv)
                    <tr>
                        <td>
                            <a href="{{ route('invoice.edit', ['id' => $inv->invoice_id]) }}" target="_blank">
                                {{ $inv->invoice_id + 9976 }}
                            </a>
                        </td>
                        <td>
                            @php
                            $short = \App\Models\data_tb::where('c_name', $inv->customer)->value('c_shortname');
                            @endphp
                            {{ $short ?? $inv->customer }}
                        </td>
                        <td>{{ $inv->part_no }}</td>
                        <td>{{ $inv->podate }}</td>
                        <td>{{ $inv->commision }}</td>
                        <td>{{ $inv->due_date ?? 'Pending' }}</td>
                        <td>{{ $inv->com_date ?? 'Pending' }}</td>
                        <td><input type="checkbox" value="{{ $inv->invoice_id }}"></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No invoices found.</td>
                    </tr>
                    @endforelse
                    @else
                    <tr>
                        <td colspan="8" class="text-center text-muted">Please select a sales rep to view data.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if ($selectedRep)
        <div class="mt-3">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>