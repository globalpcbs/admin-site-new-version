<div>
    <div>
    <div>
    @include('includes.flash')
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-plus-circle"></i> Add Alert
        </div>
        <div>
            <form wire:submit.prevent="save">
                <table class="table table-bordered">
                    <tr>
                        <th class="w-25 align-middle">
                            <i class="fa fa-search"></i> Search Part
                        </th>
                        <td colspan="2">
                            <!-- Simple Search Input -->
                            <div class="position-relative">
                                <div class="input-group">
                                    <input type="text"
                                           id="searchBox"
                                           placeholder="Type customer, part no, or revision..."
                                           class="form-control"
                                           autocomplete="off">
                                </div>
                                
                                <!-- Results Dropdown -->
                                <div id="resultsDropdown" class="search-results" style="display: none;">
                                    <div id="resultsList"></div>
                                </div>
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
                                <input type="text" 
                                       wire:model="txtcust" 
                                       class="form-control"
                                       placeholder="Customer name"
                                       id="customerInput">
                            </div>
                            @error('txtcust')
                            <div class="text-danger small mt-1">
                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                            </div>
                            @enderror
                        </td>
                    </tr>

                    <!-- Part No Field -->
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
                                       placeholder="Part number"
                                       id="partInput">
                            </div>
                            @error('pno')
                            <div class="text-danger small mt-1">
                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                            </div>
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
                                    <input wire:model="rev" 
                                           type="text" 
                                           class="form-control"
                                           placeholder="Revision"
                                           id="revInput">
                                </div>
                                <button type="button" 
                                        class="btn btn-outline-primary btn-sm ms-2"
                                        wire:click="addAlert">
                                    <i class="fa fa-plus"></i> Add Alert
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Alerts Section -->
                    <tr>
                        <th class="align-top">
                            <i class="fa fa-bell"></i> Alerts
                        </th>
                        <td colspan="2">
                            @if(count($alerts) === 0)
                            <div class="alert alert-info py-2">
                                <i class="fa fa-info-circle"></i> No alerts added yet.
                            </div>
                            @endif
                            
                            @foreach ($alerts as $index => $alert)
                            <div class="card mb-3">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                <i class="fa fa-comment"></i> Alert #{{ $index + 1 }}
                                            </label>
                                            <textarea wire:model="alerts.{{ $index }}.text" 
                                                      rows="2"
                                                      class="form-control"
                                                      placeholder="Enter alert message..."></textarea>
                                            @error("alerts.$index.text")
                                            <div class="text-danger small mt-1">
                                                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">
                                                <i class="fa fa-eye"></i> Viewable In
                                                <small class="text-danger">(Hold Ctrl to select multiple)</small>
                                            </label>
                                            <select wire:model="alerts.{{ $index }}.viewable" 
                                                    multiple 
                                                    class="form-select"
                                                    size="4">
                                                <option value="quo">Quote</option>
                                                <option value="po">Purchase Order</option>
                                                <option value="con">Confirmation</option>
                                                <option value="pac">Packing</option>
                                                <option value="inv">Invoices</option>
                                                <option value="cre">Credit</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            @if($index > 0)
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger"
                                                    wire:click="removeAlert({{ $index }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            <div class="mt-2">
                                <button type="button" 
                                        class="btn btn-outline-success btn-sm"
                                        wire:click="addAlert">
                                    <i class="fa fa-plus-circle"></i> Add Another Alert
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <button type="button" 
                                class="btn btn-secondary"
                                wire:click="resetForm">
                            <i class="fa fa-refresh"></i> Reset
                        </button>
                        <button type="submit" 
                                class="btn btn-primary"
                                wire:loading.attr="disabled">
                            <i class="fa fa-save"></i> 
                            <span wire:loading.remove>Save Alerts</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Simple JavaScript Search -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    const resultsDropdown = document.getElementById('resultsDropdown');
    const resultsList = document.getElementById('resultsList');
    
    const customerInput = document.getElementById('customerInput');
    const partInput = document.getElementById('partInput');
    const revInput = document.getElementById('revInput');
    
    let debounceTimer;
    
    // Focus on search box
    searchBox.focus();
    
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
                         onclick="selectResult('${item.cust_name}', '${escapeHtml(item.part_no)}', '${escapeHtml(item.rev || '')}')">
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
    
    // Make selectResult function globally available
    window.selectResult = function(customer, partNo, revision) {
        // Set the input values directly
        customerInput.value = customer;
        partInput.value = partNo;
        revInput.value = revision || '';
        
        // Update Livewire properties (optional - only if you need backend to know)
        @this.set('txtcust', customer);
        @this.set('pno', partNo);
        @this.set('rev', revision || '');
        
        // Clear search and hide results
        searchBox.value = '';
        hideResults();
        
        // Focus on alerts section
        document.querySelector('textarea').focus();
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