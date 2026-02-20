<div>
    <div class="mt-4">
           @if (session()->has('success'))
        <div 
            class="alert alert-success shadow"
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
            x-init="setTimeout(() => show = false, 3000)"
        >
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif
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

  <div class="card mb-2">
    <div class="card-header bg-primary text-white">
        <h5>
            <b>
                <i class="fa fa-list"></i> Manage Vendor Profiles
                <i class="fa fa-spin fa-spinner float-end" wire:loading></i>
            </b>
        </h5>
    </div>
      <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th><i class="fa fa-id-badge"></i></th>
                <th><i class="fa fa-user"></i> Vendor</th>
                <th><i class="fa fa-list-alt"></i> Requirements</th>
                <th><i class="fa fa-edit"></i> Edit</th>
                <th><i class="fa fa-trash"></i> Delete</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vendors as $vendor)
                <tr>
                    <td>{{ $vendor->profid }}</td>
                    <td>{{ optional($vendor->vendor)->c_name ?? 'N/A' }}</td>
                    <td>
                        @foreach ($vendor->requirements as $index => $req)
                          <span class="badge bg-danger">{{ $index + 1 }}</span>  {!! nl2br(e($req->reqs)) !!}<br>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('vendors.profile.edit',$vendor->profid) }}" class="btn btn-sm btn-success">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-danger" wire:confirm="Are you sure You want to delete this profile?" wire:click="deleteVendorProfile({{ $vendor->profid }})" wire:key="delete-{{ $vendor->profid }}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No vendor profiles found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
      <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center mt-3">
                <button wire:click="previousPage" class="btn btn-primary" {{ $vendors->onFirstPage() ? 'disabled' : '' }}>
                    <i class="fa fa-angle-left"></i> Previous
                </button>

                <span>
                    <i class="fa fa-file-text-o"></i> Page {{ $vendors->currentPage() }} of {{ $vendors->lastPage() }}
                </span>

                <button wire:click="nextPage" class="btn btn-primary" {{ $vendors->hasMorePages() ? '' : 'disabled' }}>
                    Next <i class="fa fa-angle-right"></i>
                </button>
            </div>

    </div>
  </div>

    {{-- Confirmation Modal --}}
    @if ($confirmingDelete)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> Confirm Deletion</h5>
                    <button type="button" class="btn-close" wire:click="$set('confirmingDelete', false)"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this vendor profile?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                    <button class="btn btn-danger" wire:click="deleteVendorProfile">
                        <i class="fa fa-trash"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

</div>