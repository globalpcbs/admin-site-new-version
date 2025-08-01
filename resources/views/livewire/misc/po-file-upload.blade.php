<div>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-upload"></i> Upload/View POs</h5>
        </div>

        <div>
            <table class="table table-bordered table-striped table-hover" id="po-table">
                <thead class="table-light">
                    <tr>
                        <th><i class="fa fa-user"></i> Customer</th>
                        <th><i class="fa fa-barcode"></i> Part No</th>
                        <th><i class="fa fa-refresh"></i> Rev</th>
                        <th><i class="fa fa-cogs"></i> Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $rec)
                    <tr>
                        <td>{{ $rec->cust_name }} </td>
                        <td>{{ $rec->part_no }}</td>
                        <td>{{ $rec->rev }}</td>
                        <td>
                            <a href="{{ route('misc.po-file-upload-work', [
                    'customer' => $rec->cust_name,
                    'part_no' => $rec->part_no,
                    'rev' => $rec->rev 
                ]) }}" class="btn btn-info btn-sm btn-xs">
                                <i class="fa fa-upload"></i> Upload/View POs
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('livewire:load', function() {
        let table = new DataTable('#po-table');
    });
    </script>
    @endpush
</div>