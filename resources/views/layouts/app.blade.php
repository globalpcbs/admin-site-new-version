<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter -->
    <meta name="twitter:site" content="@themepixels">
    <meta name="twitter:creator" content="@themepixels">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bracket">
    <meta name="twitter:description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="twitter:image" content="http://themepixels.me/bracket/img/bracket-social.png">

    <!-- Facebook -->
    <meta property="og:url" content="http://themepixels.me/bracket">
    <meta property="og:title" content="Bracket">
    <meta property="og:description" content="Premium Quality and Responsive UI for Dashboard.">

    <meta property="og:image" content="http://themepixels.me/bracket/img/bracket-social.png">
    <meta property="og:image:secure_url" content="http://themepixels.me/bracket/img/bracket-social.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">

    <!-- Meta -->
    <meta name="description" content="Premium Quality and Responsive UI for Dashboard.">
    <meta name="author" content="ThemePixels">

    <title>{{ $title ?? 'Global Pcbs' }} | Pcbs Global</title>
    @vite(['resources/js/app.js'])
    @if(Auth::check())
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

        <!-- vendor css -->
        <link href="{{ asset('lib/Ionicons/css/ionicons.css')}}" rel="stylesheet">
        <link href="{{ asset('lib/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
        <link href="{{ asset('lib/jquery-switchbutton/jquery.switchButton.css')}}" rel="stylesheet">
        <link href="{{ asset('lib/rickshaw/rickshaw.min.css')}}" rel="stylesheet">
        <link href="{{ asset('lib/chartist/chartist.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
            integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Bracket CSS -->
        <link rel="stylesheet" href="{{ asset('css/bracket.css')}}">
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @else
        <!-- vendor css -->
        <link href="{{ asset('lib/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
        <link href="{{ asset('lib/Ionicons/css/ionicons.css')}}" rel="stylesheet">

        <!-- Bracket CSS -->
        <link rel="stylesheet" href="{{ asset('css/bracket.css')}}">
    @endif
    @livewireStyles
</head>
<style>
    .sidebar-divider {
        height: .5px;
        background-color: white;
        width: 100%;
    }

    .btn-xs {
        padding: 2px 6px;
        font-size: 10px;
    }

    .font-xs {
        font-size: 10px;
    }

    .font-xs2 {
        font-size: 6px;
    }

    .font-xs3 {
        font-size: 8px;
    }

    .custom-tooltip {
        position: absolute;
        z-index: 1000;
        width: 260px;
        background: #eef;
        border: 1px solid #369;
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 6px;
        display: none;
        text-align: left;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        white-space: normal;
    }

    .tooltip-wrapper {
        position: relative;
        display: inline-block;
    }

    .tooltip-wrapper:hover .custom-tooltip {
        display: block;
    }

    .tooltip-inner {
        background-color: #333;
        color: #fff;
        font-size: 0.85rem;
        padding: 8px 12px;
        border-radius: 8px;
        text-align: left;
        max-width: 300px;
    }

    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #333;
    }

    .ttip_overlay {
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
        border-radius: 5px;
        font-size: 12px;
    }
</style>

<body>
    @if(Auth::check())
        @include('layouts.includes.sidebar') {{-- Load sidebar from separate Blade file --}}
    @else
        @include('includes.flash')
        {{ $slot }}
    @endif
    @livewireScripts
    @if(Auth::check())
        <script src="{{ asset('lib/jquery/jquery.js')}}"></script>
        <script src="{{ asset('lib/popper.js/popper.js')}}"></script>
        <script src="{{ asset('lib/bootstrap/bootstrap.js')}}"></script>
        <script src="{{ asset('lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js')}}"></script>
        <script src="{{ asset('lib/moment/moment.js')}}"></script>
        <script src="{{ asset('lib/jquery-ui/jquery-ui.js')}}"></script>
        <script src="{{ asset('lib/jquery-switchbutton/jquery.switchButton.js')}}"></script>
        <script src="{{ asset('lib/peity/jquery.peity.js')}}"></script>
        <script src="{{ asset('lib/chartist/chartist.js')}}"></script>
        <script src="{{ asset('lib/jquery.sparkline.bower/jquery.sparkline.min.js')}}"></script>
        <script src="{{ asset('lib/d3/d3.js')}}"></script>
        <script src="{{ asset('lib/rickshaw/rickshaw.min.js')}}"></script>


        <script src="{{ asset('js/bracket.js')}}"></script>
        <script src="{{ asset('js/ResizeSensor.js')}}"></script>
        <script src="{{ asset('js/dashboard.js')}}"></script>
        <script>
            $(function () {
                'use strict'

                // FOR DEMO ONLY
                // menu collapsed by default during first page load or refresh with screen
                // having a size between 992px and 1299px. This is intended on this page only
                // for better viewing of widgets demo.
                $(window).resize(function () {
                    minimizeMenu();
                });

                minimizeMenu();

                function minimizeMenu() {
                    if (window.matchMedia('(min-width: 992px)').matches && window.matchMedia('(max-width: 1299px)')
                        .matches) {
                        // show only the icons and hide left menu label by default
                        $('.menu-item-label,.menu-item-arrow').addClass('op-lg-0-force d-lg-none');
                        $('body').addClass('collapsed-menu');
                        $('.show-sub + .br-menu-sub').slideUp();
                    } else if (window.matchMedia('(min-width: 1300px)').matches && !$('body').hasClass(
                        'collapsed-menu')) {
                        $('.menu-item-label,.menu-item-arrow').removeClass('op-lg-0-force d-lg-none');
                        $('body').removeClass('collapsed-menu');
                        $('.show-sub + .br-menu-sub').slideDown();
                    }
                }
            });
        </script>
    @else
        @vite('resources/js/app.js')
        <script src="{{ asset('lib/jquery/jquery.js')}}"></script>
        <script src="{{ asset('lib/popper.js/popper.js')}}"></script>
        <script src="{{ asset('lib/bootstrap/bootstrap.js')}}"></script>
    @endif
    {{-- Livewire check script --}}
    <script>
        document.addEventListener('livewire:load', function () {
            console.log("Livewire JS test:", window.livewire); // should now be defined
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.ttip_trigger').forEach(function (trigger) {
                const overlay = trigger.nextElementSibling;
                const closeBtn = overlay.querySelector('.ttip_close');

                trigger.addEventListener('mouseenter', () => {
                    overlay.style.display = 'block';
                });

                trigger.addEventListener('mouseleave', () => {
                    setTimeout(() => {
                        if (!overlay.matches(':hover')) overlay.style.display = 'none';
                    }, 300);
                });

                overlay.addEventListener('mouseleave', () => {
                    overlay.style.display = 'none';
                });

                closeBtn.addEventListener('click', () => {
                    overlay.style.display = 'none';
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.allocations').forEach(function (el) {
                el.addEventListener('click', function () {
                    const id = this.id.replace('p', '');
                    const overlay = document.getElementById('aldiv_' + id);
                    if (overlay) {
                        overlay.style.display = (overlay.style.display === 'none' || overlay.style
                            .display === '') ? 'block' : 'none';
                    }
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.dalerts').forEach(function (el) {
                el.addEventListener('click', function () {
                    const id = el.getAttribute('data-stkid');
                    const tooltip = document.getElementById('div_' + id);

                    // Hide all other tooltips
                    document.querySelectorAll('.ttip_overlay').forEach(function (tip) {
                        if (tip !== tooltip) tip.style.display = 'none';
                    });

                    // Toggle the clicked tooltip
                    if (tooltip.style.display === 'none') {
                        tooltip.style.display = 'block';
                    } else {
                        tooltip.style.display = 'none';
                    }
                });
            });
        });

        document.addEventListener('livewire:load', function () {
            // Handle simple quote checkbox
            Livewire.on('simpleQuoteToggled', (isChecked) => {
                const commentsDiv = document.getElementById('comments');
                const complexForm = document.getElementById('complexform');

                if (isChecked) {
                    commentsDiv.style.visibility = "visible";
                    complexForm.style.display = "none";
                } else {
                    commentsDiv.style.visibility = "hidden";
                    complexForm.style.display = "block";
                }
            });

            // Handle FOB other field
            Livewire.on('fobChanged', (value) => {
                const fobOther = document.getElementById('fob_oth');
                if (value === 'Other') {
                    fobOther.style.visibility = 'visible';
                } else {
                    fobOther.style.visibility = 'hidden';
                }
            });

            // Handle vendor other field
            Livewire.on('vendorChanged', (value) => {
                const vendorOther = document.getElementById('vid_oth');
                if (value === '9999') {
                    vendorOther.style.visibility = 'visible';
                } else {
                    vendorOther.style.visibility = 'hidden';
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            // Make both modals draggable
            document.querySelectorAll('.modal-dialog').forEach(modal => {
                const header = modal.querySelector('.modal-header');

                let isDragging = false;
                let offsetX = 0,
                    offsetY = 0;

                header.addEventListener('mousedown', function (e) {
                    isDragging = true;
                    const rect = modal.getBoundingClientRect();
                    offsetX = e.clientX - rect.left;
                    offsetY = e.clientY - rect.top;

                    modal.style.position = 'absolute';
                    modal.style.margin = 0;
                    modal.style.zIndex = 1055;

                    document.body.style.userSelect = 'none';
                });

                document.addEventListener('mousemove', function (e) {
                    if (isDragging) {
                        modal.style.left = `${e.clientX - offsetX}px`;
                        modal.style.top = `${e.clientY - offsetY}px`;
                    }
                });

                document.addEventListener('mouseup', function () {
                    isDragging = false;
                    document.body.style.userSelect = '';
                });
            });
        });
    </script>

</body>

</html>