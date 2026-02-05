<div>
    <div>
    <div>
    @include('includes.flash')
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-pencil"></i>
            Edit Alerts – {{ $txtcust }} / {{ $pno }} / {{ $rev_inp ?: '—' }}
        </div>

        <form wire:submit.prevent="save">
            @csrf
            <table class="table table-bordered">
                <!-- Search Part No./Rev -->
                <tr>
                    <th class="w-25 align-middle">
                        <i class="fa fa-search"></i> Search Different Part
                    </th>
                    <td colspan="2">
                        <!-- Simple Search Input -->
                        <div class="position-relative">
                            <div class="input-group">
                                <input type="text"
                                       id="searchBox"
                                       placeholder="Search for a different customer/part/revision..."
                                       class="form-control"
                                       autocomplete="off">
                            </div>
                            
                            <!-- Results Dropdown -->
                            <div id="resultsDropdown" class="search-results" style="display: none;">
                                <div id="resultsList"></div>
                            </div>
                            
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i> Search if you want to edit alerts for a different part
                            </small>
                        </div>
                    </td>
                </tr>

                <!-- Customer Field -->
                <tr>
                    <th class="align-middle">
                        <i class="fa fa-user"></i> Customer
                    </th>
                    <td colspan="2">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-building"></i>
                            </span>
                            <input wire:model="txtcust" 
                                   type="text" 
                                   class="form-control"
                                   id="customerInput">
                        </div>
                        @error('txtcust') 
                        <span class="text-danger small">{{ $message }}</span> 
                        @enderror
                    </td>
                </tr>

                <!-- Part no. Field -->
                <tr>
                    <th class="align-middle">
                        <i class="fa fa-wrench"></i> Part no.
                    </th>
                    <td>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-hashtag"></i>
                            </span>
                            <input wire:model="pno" 
                                   type="text" 
                                   class="form-control"
                                   id="partInput">
                        </div>
                        @error('pno') 
                        <span class="text-danger small">{{ $message }}</span> 
                        @enderror
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <label class="me-2">
                                <i class="fa fa-refresh"></i> Rev.
                            </label>
                            <div class="input-group" style="width: 150px;">
                                <span class="input-group-text">
                                    <i class="fa fa-code-fork"></i>
                                </span>
                                <input wire:model="rev_inp" 
                                       type="text" 
                                       class="form-control"
                                       id="revInput">
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Alerts list -->
                <tr>
                    <th class="align-top">
                        <i class="fa fa-bell"></i> Alerts
                    </th>
                    <td colspan="2">
                        @foreach ($alerts as $idx => $alert)
                        <div class="row mb-2" wire:key="alert-{{ $alert['id'] }}">
                            <div class="col-md-6">
                                <label>
                                    <i class="fa fa-comment"></i> Alert #{{ $idx + 1 }}
                                </label>
                                <input wire:model="alerts.{{ $idx }}.text" 
                                       type="text" 
                                       class="form-control">
                                @error("alerts.$idx.text") 
                                <span class="text-danger small">{{ $message }}</span> 
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label>
                                    <i class="fa fa-eye"></i> Viewable In 
                                    <small class="text-danger">(Hold Shift to select multiple)</small>
                                </label>
                                <select wire:model="alerts.{{ $idx }}.viewable" 
                                        multiple 
                                        class="form-select" 
                                        size="6">
                                    <option value="quo">Quote</option>
                                    <option value="po">Purchase</option>
                                    <option value="con">Confirmation</option>
                                    <option value="pac">Packing</option>
                                    <option value="inv">Invoices</option>
                                    <option value="cre">Credit</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        wire:click="removeAlert({{ $idx }})">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach

                        <button type="button" 
                                class="btn btn-outline-primary btn-sm" 
                                wire:click="addAlert">
                            <i class="fa fa-plus"></i> Add Alert Line
                        </button>
                    </td>
                </tr>
            </table>

            <div class="card-body gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Save Changes
                    <i class="fa fa-spinner fa-spin" wire:loading></i>
                </button>
                <a href="{{ route('customers.alerts.manage-part') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Same JavaScript Search as Add Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    const resultsDropdown = document.getElementById('resultsDropdown');
    const resultsList = document.getElementById('resultsList');
    
    const customerInput = document.getElementById('customerInput');
    const partInput = document.getElementById('partInput');
    const revInput = document.getElementById('revInput');
    
    let debounceTimer;
    
    // Handle input
    searchBox.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const searchTerm = this.value.trim();
        
        if (searchTerm.length < 2) {
            hideResults();
            return;
        }
        
        // Show loading
        resultsList.innerHTML = '<div class="p-2 text-muted"><i class="fa fa-spinner fa-spin"></i> Searching...</div>';
        resultsDropdown.style.display = 'block';
        
        // Debounce search
        debounceTimer = setTimeout(() => {
            searchParts(searchTerm);
        }, 300);
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchBox.contains(e.target) && !resultsDropdown.contains(e.target)) {
            hideResults();
        }
    });
    
    // Handle keyboard
    searchBox.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideResults();
        }
    });
    
    function searchParts(searchTerm) {
        fetch(`/api/search-parts?q=${encodeURIComponent(searchTerm)}`)
            .then(response => {
                if (!response.ok) throw new Error('Search failed');
                return response.json();
            })
            .then(data => {
                displayResults(data);
            })
            .catch(error => {
                console.error('Error:', error);
                resultsList.innerHTML = '<div class="p-2 text-danger">Search error</div>';
            });
    }
    
    function displayResults(results) {
        if (!results || results.length === 0) {
            resultsList.innerHTML = '<div class="p-2 text-muted">No results found</div>';
            return;
        }
        
        let html = '';
        results.forEach(item => {
            // Format for display: Customer_PartNo_Rev
            const displayText = item.cust_name + '_' + item.part_no + 
                               (item.rev ? '_' + item.rev : '');
            
            html += `<div class="search-result-item p-2 border-bottom" 
                         onclick="selectResultForEdit('${item.cust_name}', '${escapeHtml(item.part_no)}', '${escapeHtml(item.rev || '')}')">
                        <div class="fw-bold">${escapeHtml(displayText)}</div>
                     </div>`;
        });
        
        resultsList.innerHTML = html;
    }
    
    function hideResults() {
        resultsDropdown.style.display = 'none';
    }
    
    // Helper to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Make selectResultForEdit function globally available
    window.selectResultForEdit = function(customer, partNo, revision) {
        // Set the input values directly
        customerInput.value = customer;
        partInput.value = partNo;
        revInput.value = revision || '';
        
        // Update Livewire properties
        @this.set('txtcust', customer);
        @this.set('pno', partNo);
        @this.set('rev_inp', revision || '');
        
        // Clear search and hide results
        searchBox.value = '';
        hideResults();
        
        // Reload alerts for the new part
       // @this.call('loadAlertsForPart');
    };
});
</script>

<style>
.search-results {
    position: absolute;
    width: 100%;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 2px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.search-result-item {
    cursor: pointer;
    padding: 8px 12px;
}
.search-result-item:hover {
    background-color: #f8f9fa;
}
</style>
</div>
</div>