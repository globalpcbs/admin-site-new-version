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
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5>
                <i class="fas fa-address-book me-2"></i> Manage Engineering Contacts
            </h5>
        </div>
        <div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                              <td colspan="6">
                                    <label for="customerSelect" class="form-label mb-1">
                                        <i class="fas fa-search me-1"></i>Search by Customer <i class="fa fa-spin fa-spinner" wire:loading></i>
                                    </label>
                                       <select wire:change="filterCustomers($event.target.value)" class="form-select" id="customerSelect">
                                            <option value="">-- Select Customer --</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->data_id }}">{{ $c->c_name }}</option>
                                            @endforeach
                                        </select>
                              </td>
                            </tr>
                            <tr>
                                <th><i class="fa fa-hashtag"></i> ID</th>
                                <th><i class="fa fa-user-tag"></i> Customer Name</th>
                                <th><i class="fa fa-user-circle"></i> Name</th>
                                <th><i class="fa fa-user-circle"></i> Last Name</th>
                                <th><i class="fa fa-edit"></i> Edit</th>
                                <th><i class="fa fa-trash-alt"></i> Delete</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if ($contacts->count())
                                @foreach ($contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->enggcont_id }}</td>
                                        <td>{{ $contact->customer->c_name ?? 'N/A' }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->lastname }}</td>
                                        <td>
                                            <a href="{{ route('customers.eng.edit',$contact->enggcont_id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                        <td>
                                            <button wire:click="deleteCustomer({{ $contact->enggcont_id }})"
                                                class="btn btn-sm btn-danger" wire:confirm="Are you sure you want to delete this engineering contact?" wire:key="delete-{{ $contact->enggcont_id }}">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else 
                            <tr>
                                <td colspan="6">
                                    <i class="fas fa-info-circle me-1"></i> No Engineering Contacts Found.
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 p-2">
                    {{ $contacts->links() }}
                </div>
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
                    Are you sure you want to delete this contact?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" wire:click="$set('confirmingDelete', false)">Cancel</button>
                    <button class="btn btn-danger" wire:click="deleteCustomer">
                        <i class="fa fa-trash"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

</div>
