<div>
    @include('includes.flash')

    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-edit"></i> Edit Note</h5>
            <i class="fa fa-spinner fa-spin float-end" wire:loading></i>
        </div>

        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="mb-3">
                    <label class="form-label">Note Type (read-only)</label>
                    <input type="text" class="form-control" value="{{ $ntype }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Note Title</label>
                    <input type="text" class="form-control" wire:model.defer="ntitle">
                    @error('ntitle') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Note Text</label>
                    <textarea class="form-control" rows="6" wire:model.defer="ntext"></textarea>
                    @error('ntext') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check-circle"></i> Update Note
                    </button>
                    <a href="{{ route('misc.manage-notes') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>