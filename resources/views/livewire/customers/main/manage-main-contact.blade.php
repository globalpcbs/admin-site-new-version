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
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5>
                <i class="fa fa-address-book"></i> Manage Main Contacts
            </h5>
        </div>
        <div class="card-body">

            <!-- Customer Filter -->
            <div class="mb-3">
                <label class="form-label">
                    <i class="fa fa-search"></i> Search by Customer 
                    <i class="fa fa-spinner fa-spin" wire:loading></i>
                </label>
                <select wire:change="filterCustomers($event.target.value)" class="form-select">
                    <option value="">All Customers</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->data_id }}">{{ $customer->c_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Contacts Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Name</th>
                            <th>Last Name</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contacts as $contact)
                            <tr>
                              <td>{{ $contact->enggcont_id ?? 'N/A' }}</td>
                                <td>{{ $contact->customer->c_name ?? 'N/A' }}</td>
                                <td>{{ $contact->name ?? 'N/A' }}</td>
                                <td>{{ $contact->lastname ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('customers.main.edit',$contact->enggcont_id) }}">
                                        <button class="btn btn-sm btn-success">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger" wire:key="delete-{{ $contact->enggcont_id }}" wire:confirm="Are you sure you want to delete this main contact?" wire:click="deleteCustomer({{ $contact->enggcont_id }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $contacts->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    @if ($confirmingDelete)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa fa-exclamation-triangle"></i> Confirm Deletion
                        </h5>
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
