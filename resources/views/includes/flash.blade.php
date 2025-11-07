@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show auto-dismiss">
        <i class="fa fa-check-square"></i> {{ session('success') }}
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-danger alert-dismissible fade show auto-dismiss">
        <i class="fa fa-exclamation-triangle"></i> {{ session('warning') }}
    </div>
@endif

<style>
.auto-dismiss {
    animation: fadeOut 0.5s ease-in 3s forwards;
}

@keyframes fadeOut {
    from {
        opacity: 1;
    }
    to {
        opacity: 0;
        display: none;
        visibility: hidden;
    }
}
</style>