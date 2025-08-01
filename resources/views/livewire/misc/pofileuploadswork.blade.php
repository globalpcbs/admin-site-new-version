<div>
    <div class="mt-4">
        @include('includes.flash')

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                Upload File for <strong>{{ $customer }} | {{ $part_no }} | {{ $rev }}</strong>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="uploadfiler" enctype="multipart/form-data">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fa fa-upload"></i> Select File</label>
                            <input type="file" wire:model="file" class="form-control">
                            @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label"><i class="fa fa-calendar"></i> Date</label>
                            <input type="date" wire:model="date" class="form-control">
                            @error('date') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-4 mt-4 pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload"></i> Upload <i class="fa fa-spinner fa-spin" wire:loading></i>
                            </button>
                            <a href="{{ route('misc.po-upload') }}" class="btn btn-warning">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($uploadedFiles->count())
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Uploaded Files
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>File Name</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uploadedFiles as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file->customer }} | {{ $file->part_no }} | {{ $file->rev }} : {{ $file->name }}</td>
                            <td>{{ $file->date }}</td>
                            <td>
                                <a href="{{ asset($file->path) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <button wire:click="deleteFile({{ $file->id }})" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

</div>