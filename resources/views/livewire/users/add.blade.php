<div>
    <div class="mt-5">
        @include('includes.flash')
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fa fa-user-plus"></i> Add New User
            </div>
            <div class="card-body">
                <form wire:submit.prevent="addUser">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fa fa-user-circle"></i> Username
                        </label>
                        <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" id="username" placeholder="Enter username">
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fa fa-lock"></i> Password
                        </label>
                        <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-sm btn-danger float-end">
                        <i class="fa fa-plus-circle"></i> Add New User <i class="fa fa-spinner fa-spin" wire:loading></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
<div>