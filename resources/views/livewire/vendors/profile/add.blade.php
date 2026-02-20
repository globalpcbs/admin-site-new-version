<div>
    @include('includes.flash')
    <form wire:submit.prevent="save" onkeydown="if(event.key === 'Enter') event.preventDefault();">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-user-plus"></i> Add Vendor Profile
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="cid" class="form-label">Select Vendor</label>
                    <select wire:model="cid" class="form-select" id="cid">
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->data_id }}">{{ $vendor->c_name }}</option>
                        @endforeach
                    </select>
                    @error('cid') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Requirements</label>
                     <button type="button" class="btn btn-sm btn-outline-primary mt-2 mb-2 float-end" wire:click="addRequirement"> <i class="fa fa-plus-circle"></i> Add Requirement <i class="fa fa-spinner fa-spin" wire:loading></i> </button>
                    @foreach($requirements as $index => $req)
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" wire:model="requirements.{{ $index }}" placeholder="Requirement #{{ $index + 1 }}">
                            @if(count($requirements) > 1)
                                <button type="button" class="btn btn-danger" wire:click="removeRequirement({{ $index }})"> <i class="fa fa-remove"></i> Remove</button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                <div class="text-end">
                    <button type="submit" class="btn btn-sm btn-primary"> <i class="fa fa-save"></i> Save <i class="fa fa-spin fa-spinner" wire:loading></i> </button>
                    <a href="#" class="btn btn-sm btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </form>
</div>
