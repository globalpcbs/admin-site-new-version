<div>
    <div class="container-fluid">
        @include('includes.flash')
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold"> <i class="fa fa-search"></i> Search By</h6>
                        <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
                        @if($searchTerm)
                        <button wire:click="resetSearch" class="btn btn-sm btn-outline-secondary float-end me-2">
                            <i class="fa fa-times"></i> Clear Search
                        </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Part Number</span>
                                    <input wire:model.debounce.500ms="searchPart" type="text" class="form-control"
                                        placeholder="Enter part number...">
                                    <button class="btn btn-primary" type="button" wire:click="search('part')">
                                        <i class="fa fa-search"></i> Submit
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Customer Name</span>
                                    <input wire:model.debounce.500ms="searchCustomer" type="text" class="form-control"
                                        placeholder="Enter customer name...">
                                    <button class="btn btn-primary" type="button" wire:click="search('customer')">
                                        <i class="fa fa-search"></i> Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">
                    <i class="fa fa-bell fa-fw"></i> Manage Reminders
                </h6>
                @if($searchTerm)
                <small class="text-muted">
                    Showing results for:
                    <strong>{{ $searchBy === 'part' ? 'Part Number' : 'Customer Name' }}</strong>
                    containing <strong>"{{ $searchTerm }}"</strong>
                </small>
                @endif
            </div>

            <div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fa fa-hashtag fa-fw"></i> Quote#</th>
                                <th><i class="fa fa-user fa-fw"></i> Customer</th>
                                <th><i class="fa fa-cog fa-fw"></i> Part No</th>
                                <th><i class="fa fa-code-fork fa-fw"></i> Rev</th>
                                <th width="70"><i class="fa fa-power-off fa-fw"></i> Status</th>
                                <th><i class="fa fa-clock-o fa-fw"></i> Last Reminder</th>
                                <th><i class="fa fa-calendar fa-fw"></i> Period <small>(days)</small></th>
                                <th><i class="fa fa-cogs fa-fw"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reminders as $reminder)
                            <tr>
                                <td>{{ $reminder->quoteid }}</td>
                                <td>{{ $reminder->order->cust_name ?? 'N/A' }}</td>
                                <td>{{ $reminder->order->part_no ?? 'N/A' }}</td>
                                <td>{{ $reminder->order->rev ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $reminder->enabled === 'yes' ? 'success' : 'danger' }}">
                                        <i
                                            class="fa {{ $reminder->enabled === 'yes' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ ucfirst($reminder->enabled) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fa fa-clock-o"></i> {{ $reminder->lastreminder }}
                                </td>
                                <td>
                                    <i class="fa fa-calendar"></i> {{ $reminder->days }}
                                </td>
                                <td class="text-center">
                                    <button wire:click="toggleStatus({{ $reminder->id }})"
                                        class="btn btn-xs btn-sm btn-{{ $reminder->enabled === 'yes' ? 'warning' : 'success' }}"
                                        title="{{ $reminder->enabled === 'yes' ? 'Disable' : 'Enable' }}">
                                        <i
                                            class="fa {{ $reminder->enabled === 'yes' ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                        {{ $reminder->enabled === 'yes' ? 'Disable' : 'Enable' }}
                                    </button>

                                    <button wire:confirm="Are you sure you want to delete this reminder?"
                                        wire:click="deleteReminder({{ $reminder->id }})"
                                        class="btn btn-xs btn-sm btn-danger" title="Delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <i class="fa fa-exclamation-circle"></i> No reminders found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $reminders->links() }}
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal remains the same -->
        <!-- ... -->
    </div>
</div>

<!-- Scripts and styles remain the same -->