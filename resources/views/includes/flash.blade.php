<div class="mt-1 mb-1">
  <div
    x-data="{ show: false, message: '', type: 'success' }"
    x-on:alert.window="
        type = $event.detail.type;
        message = $event.detail.message;
        show = true;
        setTimeout(() => show = false, 3000);
    "
    class="mt-1 mb-1"
>
    <template x-if="show">
        <div
            class="alert"
            :class="type === 'success' ? 'alert-success' : 'alert-danger'"
            x-transition
        >
            <i :class="type === 'success' ? 'fa fa-check-circle' : 'fa fa-times-circle'"></i>
            <span x-text="message"></span>
        </div>
    </template>
</div>

  @if (session()->has('success'))
    <div class="alert alert-success auto-hide">
      <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  @if (session()->has('warning'))
    <div class="alert alert-danger auto-hide">
      <i class="fa fa-times-circle"></i> {{ session('warning') }}
    </div>
  @endif
</div>

<style>
.auto-hide {
  animation: hideAlert 0.5s ease forwards;
  animation-delay: 3s; /* wait 3 seconds, then run 0.5s animation */
  animation-fill-mode: forwards;
  overflow: hidden;
}
@keyframes hideAlert {
  from { opacity: 1; max-height: 200px; padding: .75rem 1.25rem; margin-bottom: 1rem; }
  to   { opacity: 0; max-height: 0; padding-top: 0; padding-bottom: 0; margin-bottom: 0; }
}
</style>
