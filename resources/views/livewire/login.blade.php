<div>
    <div class="d-flex align-items-center justify-content-center bg-br-primary ht-100v">
        <div class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-40 bg-white rounded shadow-base">
            <div class="signin-logo tx-center tx-28 tx-bold tx-inverse"><span class="tx-normal">[</span> Pcbs Global
                <span class="tx-normal">]</span>
            </div>
            <div class="tx-center mg-b-30">Sign in to continue</div>
            @if (session()->has('error'))
            <div class="alert alert-danger mt-1 mb-1">
                <i class="fa fa-check-circle"></i> {{ session('error') }}
            </div>
            @endif
            <form wire:submit.prevent="login">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Enter your username"
                        wire:model.live="username">
                    @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Enter your password" wire:model="password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <button type="submit" class="btn btn-info btn-block"> <i class="fa fa-spinner fa-spin" wire:loading></i>
                    Sign In</button>
            </form>

        </div>
    </div>

</div>