<div>
    <div class="mt-2">
        @include('includes.flash')
        <form wire:submit.prevent="update">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-edit"></i> Edit Profile
                </div>
                <div>
                    <table class="table table-bordered table-hover">
                        <tr>
                            <td class="fw-bold" style="width: 180px;">
                                <i class="fa fa-users"></i> Current Customer
                            </td>
                            <td>
                                <select wire:model="cid" class="form-select form-select-sm" disabled>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $cust)
                                        <option value="{{ $cust->data_id }}" @if($cust->data_id == $profile->custid) selected @endif>{{ $cust->c_name }}</option>
                                    @endforeach
                                </select>
                                @error('cid') <div class="text-danger small">{{ $message }}</div> @enderror
                            </td>
                            <td style="width: 180px;"></td>
                            <td class="text-end">
                                <button type="button" wire:click="addRequirement" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-plus-circle"></i> Add Requirement
                                </button>
                            </td>
                        </tr>
                        <tr><th><i class="fa fa-list"></i> Requirements</th></tr>
                        <tr>
                            <td colspan="4">
                                @foreach ($requirements as $index => $req)
                                    <div wire:key="req-{{ $index }}" class="mb-3 border p-2 bg-light rounded">
                                        <div class="row align-items-center">
                                            <div class="col-lg-2">
                                                <label class="form-label mb-0">Req. #{{ $index + 1 }}:</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" wire:model="requirements.{{ $index }}.req" class="form-control form-control-sm">
                                                @error("requirements.$index.req") <div class="text-danger small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-lg-2">
                                                @foreach (['quo1' => 'Quote', 'po1' => 'Purchase', 'conf1' => 'Confirmation', 'pack1' => 'Packing', 'inv1' => 'Invoices', 'cred1' => 'Credit'] as $value => $label)
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            wire:model="requirements.{{ $index }}.checkboxes.{{ $value }}"
                                                            id="checkbox_{{ $index }}_{{ $value }}">
                                                        <label class="form-check-label" for="checkbox_{{ $index }}_{{ $value }}">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="col-lg-2">
                                                <small class="text-danger"><b>Press shift while muliple selection</b></small>
                                                <select class="form-select" wire:model="requirements.{{ $index }}.viewable" multiple size="8" style="width: 150px;">
                                                    @foreach (['quo0' => 'Quote', 'po0' => 'Purchase', 'con0' => 'Confirmation', 'pac0' => 'Packing', 'inv0' => 'Invoices', 'cre0' => 'Credit'] as $key => $label)
                                                        <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-2">
                                                <button type="button" wire:click="removeRequirement({{ $index }})" class="btn btn-sm btn-outline-danger">
                                                    <i class="fa fa-times"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save"></i> Update <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                    <a href="{{ url('/manage-profile') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
