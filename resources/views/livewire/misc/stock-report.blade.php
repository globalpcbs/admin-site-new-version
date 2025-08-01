<div class="py-4">
    <div class="card">
        <div class="card-header">
            <i class="fa fa-search"></i> Search Here Via Part No
        </div>
        <div class="card-body">
            <form wire:submit.prevent="searchByPartNo">
                <div class="row mb-3 align-items-center">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Search by Part No"
                            wire:model.defer="search">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary me-2">Search</button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="resetSearch">Show
                            All</button>
                    </div>
                </div>
            </form>


        </div>
    </div>
    <siv class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Stock Report
            <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
        </div>
        <table class="table table-bordered table-sm table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Stk#</th>
                    <th><a href="#" wire:click.prevent="sortBy('customer')">Customer @if($sortField=='customer') {!!
                            $sortDirection=='asc' ? '&darr;' : '&uarr;' !!} @endif</a></th>
                    <th>Part No</th>
                    <th>Rev</th>
                    <th>Supplier</th>
                    <th><a href="#" wire:click.prevent="sortBy('dtadded')">Date Add. @if($sortField=='dtadded') {!!
                            $sortDirection=='asc' ? '&darr;' : '&uarr;' !!} @endif</a></th>
                    <th>D/C</th>
                    <th>Finish</th>
                    <th>Mfg Date</th>
                    <th>Docs Ready</th>
                    <th>In Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $row)
                @php
                $highlight = false;
                if ($row->manuf_dt) {
                $mdt = explode('-', $row->manuf_dt);
                if (count($mdt) === 3) {
                $mdate = $mdt[0] . '-' . $mdt[2];
                $timestamp = strtotime("{$mdt[2]}-{$mdt[0]}-{$mdt[1]}");
                $days = (time() - $timestamp) / (3600 * 24);
                if (
                ($row->finish === 'HASL' || $row->finish === 'ENEPIG') && $days > 170 ||
                ($row->finish === 'ENIG' && $days > 350)
                ) {
                $highlight = true;
                }
                }
                }
                @endphp
                <tr @if($highlight) class="table-danger" @endif>
                    <td>{{ $row->stkid }}</td>
                    <td>{{ $row->c_shortname }}</td>
                    <td>{{ $row->part_no }}</td>
                    <td>{{ $row->rev }}</td>
                    <td>
                        {{ \App\Models\vendor_tb::where('data_id', $row->supplier)->value('c_shortname') }}
                    </td>
                    <td>
                        @php
                        try {
                        $formatted = \Carbon\Carbon::createFromFormat('l-m-d-Y', $row->dtadded)->format('m-d-Y');
                        } catch (\Exception $e) {
                        $formatted = $row->dtadded;
                        }
                        @endphp
                        {{ $formatted }}
                    </td>
                    <td>{{ $row->dc }}</td>
                    <td>{{ $row->finish }}</td>
                    <td>{{ $row->manuf_dt ? $mdate ?? '' : '' }}</td>
                    <td>{{ $row->docsready == 1 ? 'Yes' : 'No' }}</td>
                    <td>{{ $row->ssadd }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </siv>

    <div>
        {{ $stocks->links() }}
    </div>
</div>