<div>
    @include('includes.flash')

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fa fa-pencil"></i> Edit Main Contact
        </div>
        <div>
            <form wire:submit.prevent="update">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td><label for="cid"><i class="fa fa-users"></i> Select Customer</label></td>
                            <td>
                                <select wire:model="cid" class="form-control">
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $cust)
                                        <option value="{{ $cust->data_id }}">{{ $cust->c_name }}</option>
                                    @endforeach
                                </select>
                                @error('cid') <span class="text-danger">{{ $message }}</span> @enderror
                            </td>
                        </tr>

                        <tr>
                            <td><label><i class="fa fa-user"></i> First Name</label></td>
                            <td>
                                <input type="text" wire:model="txtename" class="form-control" />
                                @error('txtename') <span class="text-danger">{{ $message }}</span> @enderror
                            </td>
                        </tr>

                        <tr>
                            <td><label><i class="fa fa-user"></i> Last Name</label></td>
                            <td>
                                <input type="text" wire:model="txtelname" class="form-control" />
                                @error('txtelname') <span class="text-danger">{{ $message }}</span> @enderror
                            </td>
                        </tr>

                        <tr>
                            <td><label><i class="fa fa-phone"></i> Phone</label></td>
                            <td>
                                <input type="text" wire:model="txtephone" class="form-control" />
                            </td>
                        </tr>

                        <tr>
                            <td><label><i class="fa fa-envelope"></i> Email</label></td>
                            <td>
                                <input type="email" wire:model="txteemail" class="form-control" />
                            </td>
                        </tr>

                        <tr>
                            <td><label><i class="fa fa-mobile"></i> Mobile</label></td>
                            <td>
                                <input type="text" wire:model="txteemob" class="form-control" />
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fa fa-save"></i> Update Contact
                                    <i class="fa fa-spinner fa-spin" wire:loading></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
