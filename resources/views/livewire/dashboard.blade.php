<div>
    <div class="mt-5">
        <div class="mb-4">
            <h4 class="text-danger"><i class="fa fa-dashboard"></i> Welcome to Admin Panel</h4>
            <p class="fs-5"> <i class="fa fa-info-circle text-primary"></i> To get quick access, please follow the
                instructions below:</p>
        </div>

        <ul class="list-unstyled text-start fs-6" style="max-width: 600px;">
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press Q</strong> to <a
                    href="{{ route('add.qoutes') }}" class="text-primary"><i class="fa fa-plus"></i> Add new quote</a>
            </li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press M</strong> to <a
                    href="{{ route('qoutes.manage') }}" class="text-primary"><i class="fa fa-list"></i> Manage
                    Quotes</a>
            </li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press P</strong> to <a
                    href="{{ route('purchase.orders.add') }}" class="text-primary"><i class="fa fa-cart-plus"></i>
                    Add
                    Purchase</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press O</strong> to <a
                    href="{{ route('purchase.orders.manage') }}" class="text-primary"><i class="fa fa-tasks"></i> Manage
                    Purchase</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press C</strong> to <a
                    href="{{ route('confirmation.add') }}" class="text-primary"><i class="fa fa-check-circle"></i>
                    Add Order Confirmation</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press V</strong> to <a
                    href="{{ route('confirmation.manage') }}" class="text-primary"><i class="fa fa-eye"></i> Manage
                    Order
                    Confirmation</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press S</strong> to <a
                    href="{{ route('packing.add') }}" class="text-primary"><i class="fa fa-file-text-o"></i> Add
                    Packing Slip</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press D</strong> to <a
                    href="{{ route('packing.manage') }}" class="text-primary"><i class="fa fa-pencil-square-o"></i>
                    Manage
                    Packing Slip</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press I</strong> to <a
                    href="{{ route('invoice.add') }}" class="text-primary"><i class="fa fa-file-text-o"></i> Add
                    Invoice</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press U</strong> to <a
                    href="{{ route('invoice.manage') }}" class="text-primary"><i class="fa fa-edit"></i> Manage
                    Invoice</a>
            </li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press R</strong> for <a
                    href="{{ route('reports.status-report') }}" class="text-primary"><i class="fa fa-bar-chart"></i>
                    Status
                    Report</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press T</strong> for <a
                    href="{{ route('misc.add-stock') }}" class="text-primary"><i class="fa fa-plus-square"></i>
                    Add Stock</a></li>
            <li class="mb-2"><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press N</strong> for <a
                    href="{{ route('misc.manage-stock') }}" class="text-primary"><i class="fa fa-cubes"></i> Manage
                    Stock</a></li>
            <li><i class="fa fa-arrow-circle-right me-2 text-primary"></i> <strong>Press <code>~</code> or <span
                        class="text-danger">SHIFT + `</span></strong> to return to this page</li>
        </ul>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create a mapping of keys to URLs
        const keyMap = {
            'q': "{{ route('add.qoutes') }}",
            'm': "{{ route('qoutes.manage') }}",
            'p': "{{ route('purchase.orders.add') }}",
            'o': "{{ route('purchase.orders.manage') }}",
            'c': "{{ route('confirmation.add') }}",
            'v': "{{ route('confirmation.manage') }}",
            's': "{{ route('packing.add') }}",
            'd': "{{ route('packing.manage') }}",
            'i': "{{ route('invoice.add') }}",
            'u': "{{ route('invoice.manage') }}",
            'r': "{{ route('reports.status-report') }}",
            't': "{{ route('misc.add-stock') }}",
            'n': "{{ route('misc.manage-stock') }}",
            '~': "{{ route('dashboard') }}",
            '`': "{{ route('dashboard') }}"
        };

        document.addEventListener('keydown', function(e) {
            // Ignore if typing in an input field
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target
                .isContentEditable) {
                return;
            }

            let key = e.key.toLowerCase();

            // Handle Shift + ` (which produces ~)
            if (e.shiftKey && e.key === '`') {
                key = '~';
            }

            // Check if the pressed key is in our map
            if (key in keyMap) {
                e.preventDefault();
                window.location.href = keyMap[key];
            }
        });
    });
    </script>
</div>