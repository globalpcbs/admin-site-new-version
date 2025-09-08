<div>
    @include('includes.flash')
    <div class="card">
        <div class="card-header">
            <i class="fa fa-list"></i> Logged Packing slips
            <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
        </div>
        <div>
            <table class="table table-bordered table-responsive font-xs table-striped table-sm">
                <thead class="table-light text-center">
                    <tr>
                        <th>Customer</th>
                        <th>Part No</th>
                        <th>Supplier</th>
                        <th>OTD</th>
                        <th>Customer PO</th>
                        <th>Cust Due Date</th>
                        <th>Qty Ordered</th>
                        <th>Qty Rec</th>
                        <th>Qty Due</th>
                        <th>Qty Shipped</th>
                        <th>Shipped on</th>
                        <th>Qty Insp</th>
                        <th>S/S</th>
                        <th>Qty Passed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                    <tr class="text-center">
                        <td>{{ optional(\App\Models\data_tb::find($row->customer))->c_shortname }}</td>
                        <td>{{ $row->part_no }}</td>
                        <td>{{ optional(\App\Models\vendor_tb::find($row->supplier))->c_shortname }}</td>
                        <td>{{ $row->otd }}</td>
                        <td>{{ $row->customer_po }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->cus_due_date)->format('m/d/Y') }}</td>
                        <td>{{ $row->qty_ordered }}</td>
                        <td>{{ $row->qty_rec }}</td>
                        <td>{{ $row->qty_due }}</td>
                        <td>{{ $row->qty_shipped }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->shipped_on)->format('m/d/Y') }}</td>
                        <td>{{ $row->qty_insp }}</td>
                        <td>{{ $row->solder_sample }}</td>
                        <td>{{ $row->qty_passed }}</td>
                        <td>
                            <a href="{{ route('misc.edit.logged',$row->id) }}" class="btn btn-success btn-sm btn-xs"><i
                                    class="fa fa-edit"></i></a>
                            <button class="btn btn-danger btn-sm btn-xs" wire:click="delete({{ $row->id }})"
                                wire:confirm><i class="fa fa-trash"></i></button>
                            <a href="#" wire:click.prevent="duplicate({{ $row->id }})"
                                class="btn btn-info btn-sm btn-xs"><i class="fa fa-clone"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>