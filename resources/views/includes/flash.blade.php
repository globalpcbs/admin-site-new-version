<div class="mt-1 mb-1">
    @if (session()->has('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="alert alert-success"
        >
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session()->has('warning'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition
            class="alert alert-danger"
        >
            <i class="fa fa-times-circle"></i> {{ session('warning') }}
        </div>
    @endif
</div>
