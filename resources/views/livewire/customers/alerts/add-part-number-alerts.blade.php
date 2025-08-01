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
                            <i class="fa fa-cogs"></i> Select Part No./Rev
                        </th>
                        <td colspan="2">
                            <select wire:model="aid" wire:change="loadExistingAlerts($event.target.value)"
                                class="form-select">
                                <option value="">Select</option>
                                @foreach ($aidOptions as $option)
                                <option value="{{ $option->ord_id }}">
                                    {{ $option->cust_name }}_{{$option->part_no }}_{{$option->rev }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="align-middle">
                            <i class="fa fa-user"></i> Customer {{ $this->txtcust  }}
                        </th>
                        <td colspan="2">
                            <input type="text" wire:model="txtcust" class="form-control" value="{{ $txtcust }}">
                            @error('txtcust')
                            <span class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <tr>
                        <th class="align-middle">
                            <i class="fa fa-wrench"></i> Part no.
                        </th>
                        <td>
                            <input wire:model="pno" value="{{ $pno }}" type="text" class="form-control">
                            @error('pno')
                            <span class="text-danger"><i class="fa fa-exclamation-circle"></i> {{ $message }}</span>
                            @enderror
                        </td>
                        <td class="align-middle">
                            <label class="me-2"><i class="fa fa-refresh"></i> Rev.</label>
                            <input wire:model="rev" value="{{ $rev }}" type="text" class="form-control d-inline-block"
                                style="width: 100px;">
                            <button type="button" class="btn btn-outline-primary btn-sm ms-2" wire:click="addAlert">
                                <i class="fa fa-plus"></i> Add Alert
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <th class="align-top">
                            <i class="fa fa-bell"></i> Alerts
                        </th>
                        <td colspan="2">
                            @foreach ($alerts as $index => $alert)
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <label><i class="fa fa-comment"></i> Alert #{{ $index + 1 }}</label>
                                    <input wire:model="alerts.{{ $index }}.text" type="text" class="form-control">
                                    @error("alerts.$index.text")
                                    <span class="text-danger"><i class="fa fa-exclamation-circle"></i>
                                        {{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label><i class="fa fa-eye"></i> Viewable In</label>
                                    <select wire:model="alerts.{{ $index }}.viewable" multiple class="form-select"
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
                                    <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="removeAlert({{ $index }})">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </td>
                    </tr>
                </table>

                <div class="card-body gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Submit
                        <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>