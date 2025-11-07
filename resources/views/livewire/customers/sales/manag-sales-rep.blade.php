<div>
    <!-- resources/views/livewire/customers/sales/manag-sales-rep.blade.php -->
@include('includes.flash')
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-list"></i> Manage Sales Rep
    </div>
    <div class="card-body">
        <!-- 🔍 Dropdown filter -->
        <select wire:model.defer="search" {{-- .defer avoids double network hits --}} wire:change="$refresh"
            {{-- force refresh after selection --}} class="form-select">
            <option value="">-- All Reps --</option>
            @foreach ($allReps as $rep)
            <option value="{{ $rep->repid }}">{{ $rep->r_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- 🗒️ Results -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th><i class="fa fa-hashtag"></i> #</th>
                    <th><i class="fa fa-user"></i> Rep Name</th>
                    <th class="text-center"><i class="fa fa-cogs"></i> Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($reps as $rep)
                <tr wire:key="rep-{{ $rep->id }}">
                    <td>{{ $rep->repid }}</td>
                    <td>{{ $rep->r_name }}</td>
                    <td class="text-center">
                        <a href="{{ route('customers.sales.manage.edit',$rep->repid) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-edit"></i> Edit
                        </a>

                        <button class="btn btn-sm btn-danger" wire:click="deleteConfirm({{ $rep->repid }})" wire:key="delete-{{ $rep->repid }}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No reps found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-2">
        {{ $reps->links() }} {{-- pagination --}}
    </div>
</div>
</div>