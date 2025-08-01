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
                {{-- Customer / part / rev rows identical to add‑form --}}
                <tr>
                    <th class="w-25 align-middle">
                        <i class="fa fa-user"></i> Customer
                    </th>
                    <td colspan="2">
                        <input wire:model="txtcust" type="text" class="form-control">
                        @error('txtcust') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                </tr>

                <tr>
                    <th class="align-middle">
                        <i class="fa fa-wrench"></i> Part no.
                    </th>
                    <td>
                        <input wire:model="pno" type="text" class="form-control">
                        @error('pno') <span class="text-danger">{{ $message }}</span> @enderror
                    </td>
                    <td class="align-middle">
                        <label class="me-2"><i class="fa fa-refresh"></i> Rev.</label>
                        <input wire:model="rev_inp" type="text" class="form-control d-inline-block"
                            style="width: 100px;">
                    </td>
                </tr>

                {{-- Alerts list --}}
                <tr>
                    <th class="align-top">
                        <i class="fa fa-bell"></i> Alerts
                    </th>
                    <td colspan="2">
                        @foreach ($alerts as $idx => $alert)
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <label><i class="fa fa-comment"></i> Alert #{{ $idx + 1 }}</label>
                                <input wire:model="alerts.{{ $idx }}.text" type="text" class="form-control">
                                @error("alerts.$idx.text") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-5">
                                <label><i class="fa fa-eye"></i> Viewable In</label>
                                <select wire:model="alerts.{{ $idx }}.viewable" multiple class="form-select" size="6">
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
                                    wire:click="removeAlert({{ $idx }})">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach

                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="addAlert">
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
                <a href="#" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>