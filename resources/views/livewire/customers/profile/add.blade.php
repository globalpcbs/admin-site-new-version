<div class="container mt-4">
    @include('includes.flash')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-user-plus"></i> Add Profile
            </div>
            <div>
                <table class="table table-bordered table-hover table-stripped">
                    <tr>
                        <td class="fw-bold" style="width: 180px;"><i class="fa fa-users"></i> Select Customer</td>
                        <td>
                            <select wire:model="cid" class="form-select form-select-sm">
                                <option value="">-- Select Customer --</option>
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->data_id }}">{{ $cust->c_name }}</option>
                                @endforeach
                            </select>
                            @error('cid') <div class="text-danger small">{{ $message }}</div> @enderror
                        </td>
                        <td style="width: 180px;"></td>
                        <td class="text-end">
                            <button type="button" wire:click="addRequirement" class="btn btn-sm btn-outline-primary"> <i class="fa fa-plus-circle"></i> Add Requirement</button>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <i class="fa fa-list"></i> Requirements
                        </th>
                    </tr>
                    <tr>
                        <td colspan="4">
                            @foreach ($requirements as $index => $req)
                                <div wire:key="req-{{ $index }}" class="mb-3 border p-2 bg-light rounded">
                                    <div class="row align-items-center">
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <label class="form-label mb-0 me-2">Req. #{{ $index + 1 }}:</label>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                            <input type="text" wire:model="requirements.{{ $index }}.req" class="form-control form-control-sm">
                                            @error("requirements.$index.req") <div class="text-danger small">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
    
                                            @foreach (['quo1' => 'Quote', 'po1' => 'Purchase', 'con1' => 'Confirmation', 'pac1' => 'Packing', 'inv1' => 'Invoices', 'cre1' => 'Credit'] as $key => $label)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model="requirements.{{ $index }}.checkboxes.{{ $key }}"
                                                        id="chk-{{ $key }}-{{ $index }}">
                                                    <label class="form-check-label" for="chk-{{ $key }}-{{ $index }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <small class="text-danger"><b>Press shift while mulipleselecting</b></small>
                                            <select class="form-select form-select-md" wire:model="requirements.{{ $index }}.viewable" multiple size="9" style="width: 150px;">
                                                @foreach (['quo0' => 'Quote', 'po0' => 'Purchase', 'con0' => 'Confirmation', 'pac0' => 'Packing', 'inv0' => 'Invoices', 'cre0' => 'Credit'] as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <button type="button" wire:click="removeRequirement({{ $index }})"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-times"></i>
                                                Remove
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
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Submit <i class="fa fa-spin fa-spinner" wire:loading></i> </button>
                <button type="reset" class="btn btn-secondary btn-sm">Reset</button>
            </div>
        </div>
    </form>
</div>
