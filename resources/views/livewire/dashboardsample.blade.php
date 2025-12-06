{{-- resources/views/livewire/dashboardsample-option-b.blade.php --}}
<div>
    <div class="dash-wrap container-fluid px-4 py-5">
        <!-- Enhanced Top Hero -->
        <div class="row">
            <div class="col-lg-8 col-sm-8 col-md-8">
                <div class="hero card border-0 rounded-4 overflow-hidden mb-5 position-relative">
                    <!-- Animated gradient background -->
                    <div class="hero-bg-animated"></div>
                    
                    <div class="hero-inner d-flex flex-column flex-lg-row align-items-center position-relative">
                        <div class="hero-text p-4 flex-fill">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="hero-badge rounded-pill px-2 py-1">
                                    <i class="fas fa-star me-1 fs-8"></i>
                                    <span class="fs-8">Live Dashboard</span>
                                </div>
                                <div class="hero-badge rounded-pill px-2 py-1">
                                    <i class="fas fa-bolt me-1 fs-8"></i>
                                    <span class="fs-8">Real-time Data</span>
                                </div>
                            </div>
                            
                            <h4 class="fw-bold mb-1 text-dark">Hello, Administrator 👋</h4>
                            <p class="text-muted mb-3 fs-6">Your manufacturing dashboard — insights, actions, and reports in one place.</p>

                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <div class="glass px-2 py-1 d-flex align-items-center gap-1">
                                    <i class="fas fa-calendar-alt fs-8"></i>
                                    <span id="currentDateTime" class="fs-7">{{ now()->format('l, F j, Y - g:i A') }}</span>
                                </div>
                                <div class="glass px-2 py-1 d-flex align-items-center gap-1">
                                    <i class="fas fa-chart-line fs-8"></i>
                                    <span class="fs-7">Performance: Excellent</span>
                                </div>
                            </div>
                        </div>

                        <div class="hero-stats p-3 d-none d-lg-flex flex-column gap-2">
                            <div class="stat-box p-2 rounded-3 text-center position-relative overflow-hidden">
                                <div class="pulse-dot"></div>
                                <div class="fs-8 text-white-80">Open Orders</div>
                                <div class="h5 fw-bold text-white mb-0">{{ $stats['open_orders']['value'] }}</div>
                                <div class="fs-8 text-success-light">+{{ str_replace('+','',$stats['open_orders']['change'] ?? '+0') }}% this month</div>
                            </div>
                            <div class="stat-box p-2 rounded-3 text-center position-relative overflow-hidden">
                                <div class="pulse-dot"></div>
                                <div class="fs-8 text-white-80">Stock Alerts</div>
                                <div class="h5 fw-bold text-white mb-0">{{ $stats['stock_alerts']['value'] }}</div>
                                <div class="fs-8 text-warning-light">Check inventory</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-4 col-md-4">
                     <!-- Enhanced Activity Feed -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="mb-1 fw-bold">Recent Activity</h6>
                                <small class="text-muted">Live system feed</small>
                            </div>
                            <div class="activity-indicator">
                                <div class="pulse live-indicator"></div>
                                <a href="#" class="small fw-semibold">View all</a>
                            </div>
                        </div>

                        <div class="activity-list mt-3">
                         <div class="activity-list mt-2">
                                @foreach($recentActivities as $index => $a)
                                <div class="activity-item d-flex align-items-start gap-2 p-2 border-bottom border-light">
                                    <div class="activity-icon flex-shrink-0 mt-1">
                                        <i class="fas {{ $a['icon'] }} fs-8 text-{{ $a['color'] ?? 'primary' }}"></i>
                                    </div>
                                    <div class="activity-content flex-grow-1 min-w-0">
                                        <div class="activity-text fs-8 text-dark mb-1 lh-sm">
                                            {{ $a['description'] }}
                                        </div>
                                        <div class="activity-time fs-8 text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $a['time'] }}
                                        </div>
                                    </div>
                                    @if($index === 0)
                                    <div class="activity-badge flex-shrink-0">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fs-8">New</span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Compact cards with individual gradients -->
        <div class="cards-grid row g-3 mb-4">
            @php
                $cardGradients = [
                    'total_quotes' => 'linear-gradient(135deg, var(--g1), var(--g2))',
                    'total_purchases' => 'linear-gradient(135deg, var(--g3), var(--g4))',
                    'pending_confirmations' => 'linear-gradient(135deg, var(--g5), var(--g6))',
                    'total_invoices' => 'linear-gradient(135deg, var(--g1), var(--g3))',
                    'open_orders' => 'linear-gradient(135deg, var(--g4), var(--g5))',
                    'stock_alerts' => 'linear-gradient(135deg, var(--g6), var(--g1))'
                ];
                
                $cardIcons = [
                    'total_quotes'=>'fa-file-alt',
                    'total_purchases'=>'fa-shopping-cart',
                    'pending_confirmations'=>'fa-clipboard-check',
                    'total_invoices'=>'fa-receipt',
                    'open_orders'=>'fa-tasks',
                    'stock_alerts'=>'fa-exclamation-triangle'
                ];
            @endphp
            
            @foreach($stats as $key => $stat)
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="stat-card p-3 rounded-3 h-100" data-gradient="{{ $cardGradients[$key] ?? $cardGradients['total_quotes'] }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon rounded-2 p-2">
                            <i class="fas {{ $cardIcons[$key] ?? 'fa-chart-bar' }} fa-lg"></i>
                        </div>
                        <div class="trend-badge small {{ str_contains($stat['change'] ?? '', '+') ? 'text-success' : 'text-warning' }}">
                            {{ $stat['change'] ?? '' }}
                        </div>
                    </div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ $stat['value'] }}</div>
                    <div class="small text-muted">{{ ucwords(str_replace('_',' ',$key)) }}</div>
                    
                    <!-- Progress bar for visual interest -->
                    <div class="progress mt-2" style="height: 3px;">
                        <div class="progress-bar" style="width: {{ min(100, ($stat['value'] / 300) * 100) }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <!-- Enhanced Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h5 class="mb-1 fw-bold">Quick Actions</h5>
                                <small class="text-muted">Keyboard shortcuts & quick links</small>
                            </div>
                            <div class="shortcut-hint">
                                <i class="fas fa-keyboard me-1"></i>
                                <span class="small">Press keys to activate</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach($quickShortcuts as $s)
                            <div class="col-md-4 col-sm-6">
                                <div class="action-tile d-flex align-items-center gap-3 p-3 rounded-3" 
                                     data-key="{{ $s['key'] }}"
                                     data-color="{{ $s['color'] }}">
                                    <div class="tile-key rounded-2 d-flex align-items-center justify-content-center">
                                        <div class="fw-bold">{{ $s['key'] }}</div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark">{{ $s['label'] }}</div>
                                        <small class="text-muted">Press <kbd>{{ $s['key'] }}</kbd></small>
                                    </div>
                                    <div class="action-icon">
                                        <i class="fas {{ $s['icon'] }} fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Enhanced Management Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <h5 class="mb-1 fw-bold">Management Center</h5>
                                <small class="text-muted">Open modules quickly</small>
                            </div>
                            <div class="management-stats small text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Admin Access
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach($managementShortcuts as $m)
                            <div class="col-md-4 col-sm-6">
                                <a href="{{ $m['route'] }}" class="text-decoration-none">
                                    <div class="manage-card p-3 rounded-3 d-flex align-items-center gap-3">
                                        <div class="manage-icon rounded-2 d-flex align-items-center justify-content-center">
                                            <i class="fas {{ $m['icon'] }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-dark">{{ $m['label'] }}</div>
                                            <small class="text-muted">Manage records</small>
                                        </div>
                                        <div class="manage-arrow">
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Premium Styles -->
    <style>
    :root{
        --muted:#6b7280;
        --g1:#6d28d9; --g2:#4f46e5; --g3:#0ea5e9; 
        --g4:#10b981; --g5:#f59e0b; --g6:#ec4899;
        --card-radius:1rem;
        --shadow-soft:0 8px 25px rgba(0,0,0,0.05);
        --shadow-medium:0 12px 35px rgba(0,0,0,0.1);
    }

    .text-white-80 { color: rgba(255,255,255,0.8); }
    .text-success-light { color: #86efac; }
    .text-warning-light { color: #fde68a; }

    /* Enhanced Hero Section */
    .hero-bg-animated {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            135deg,
            rgba(109,40,217,0.3) 0%,
            rgba(79,70,229,0.3) 25%,
            rgba(14,165,233,0.3) 50%,
            rgba(236,72,153,0.3) 75%,
            rgba(109,40,217,0.3) 100%
        );
        background-size: 400% 400%;
        animation: gradientShift 8s ease infinite;
    }

    .hero-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Pulse animation for live elements */
    .pulse-dot {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .live-indicator {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: #ef4444;
        border-radius: 50%;
        margin-right: 6px;
    }

    /* Enhanced Stat Cards */
    .stat-card {
        background: linear-gradient(160deg, #ffffff, #fafafa);
        border-radius: var(--card-radius);
        padding: 1.25rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .stat-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient, linear-gradient(135deg, var(--g1), var(--g6)));
        opacity: 1;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-medium);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        background: var(--gradient, linear-gradient(135deg, var(--g1), var(--g6)));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .trend-badge {
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
        background: rgba(0,0,0,0.05);
    }

    /* Enhanced Action Tiles */
    .action-tile {
        background: linear-gradient(135deg, #ffffff 20%, #f8fafc 100%);
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-soft);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }

    .action-tile::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .action-tile:hover::before {
        left: 100%;
    }

    .action-tile:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-medium);
    }

    .tile-key {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, var(--g3), var(--g4));
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Enhanced Management Cards */
    .manage-card {
        background: linear-gradient(135deg, #ffffff, #fefefe);
        border-radius: var(--card-radius);
        padding: 1rem;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: var(--shadow-soft);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .manage-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-medium);
    }

    .manage-icon {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, var(--g5), var(--g6));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .manage-arrow {
        opacity: 0;
        transform: translateX(-5px);
        transition: all 0.3s ease;
    }

    .manage-card:hover .manage-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* Enhanced Activity */
    .activity-highlight {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe) !important;
        border-left: 4px solid var(--g3);
    }

    .activity-row {
        background: linear-gradient(135deg, #ffffff, #fafafa);
        border-left: 4px solid var(--g3);
        transition: all 0.2s ease;
    }

    .activity-row:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        transform: translateX(4px);
    }

    .avatar {
        width: 48px;
        height: 48px;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, var(--g6), var(--g1));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Enhanced Module Pills */
    .module-pill {
        background: linear-gradient(135deg, var(--g3), var(--g1));
        padding: 0.5rem 1rem;
        color: #fff !important;
        border-radius: 2rem;
        font-weight: 600;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
    }

    .module-pill:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0 12px 25px rgba(0,0,0,0.2);
        color: #fff !important;
    }

    .module-main-icon {
        font-size: 2rem;
        background: linear-gradient(135deg, var(--g1), var(--g3));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }

    /* Animations */
    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes pulse {
        0% { transform: scale(0.95); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(0.95); opacity: 1; }
    }

    /* Improved responsive design */
    @media (max-width: 768px) {
        .hero-text h1 {
            font-size: 2rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .action-tile, .manage-card {
            padding: 0.875rem;
        }
    }

    .shortcut-hint, .management-stats, .activity-indicator {
        background: rgba(0,0,0,0.03);
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-weight: 500;
    }
/* Custom smaller font sizes */
.fs-6 { font-size: 0.875rem !important; }
.fs-7 { font-size: 0.8rem !important; }
.fs-8 { font-size: 0.75rem !important; }

/* Adjusted hero styles for smaller text */
.hero-text h4 {
    font-size: 1.5rem;
    line-height: 1.3;
}

.hero-badge {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    font-weight: 500;
}

.glass {
    background: rgba(255,255,255,0.16);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 0.5rem;
    backdrop-filter: blur(6px);
    color: #fff;
    font-weight: 500;
}

/* Smaller padding for compact layout */
.hero-text.p-4 {
    padding: 1.5rem !important;
}

.hero-stats.p-3 {
    padding: 1rem !important;
}

.stat-box.p-2 {
    padding: 0.75rem !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-text h4 {
        font-size: 1.25rem;
    }
    
    .hero-text.p-4 {
        padding: 1rem !important;
    }
    
    .fs-6 { font-size: 0.8rem !important; }
    .fs-7 { font-size: 0.75rem !important; }
    .fs-8 { font-size: 0.7rem !important; }
}

/* Keep existing animations */
.hero-bg-animated {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(109,40,217,0.3) 0%,
        rgba(79,70,229,0.3) 25%,
        rgba(14,165,233,0.3) 50%,
        rgba(236,72,153,0.3) 75%,
        rgba(109,40,217,0.3) 100%
    );
    background-size: 400% 400%;
    animation: gradientShift 8s ease infinite;
}

.pulse-dot {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 6px;
    height: 6px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes pulse {
    0% { transform: scale(0.95); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.7; }
    100% { transform: scale(0.95); opacity: 1; }
}
</style>

    <!-- Enhanced JavaScript -->
    <script>
        // Enhanced real-time clock
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            
            const dateString = now.toLocaleDateString('en-US', dateOptions);
            const timeString = now.toLocaleTimeString('en-US', timeOptions);
            
            document.getElementById('currentDateTime').textContent = `${dateString} - ${timeString}`;
        }
        
        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Enhanced keyboard shortcuts with visual feedback
        document.addEventListener('keydown', function(e) {
            const key = e.key.toUpperCase();
            const tile = document.querySelector('[data-key="' + key + '"]');
            
            if (!tile || e.ctrlKey || e.metaKey || e.altKey) return;
            
            e.preventDefault();
            
            // Enhanced visual feedback
            tile.style.transform = 'scale(0.98)';
            tile.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            
            setTimeout(() => {
                tile.style.transform = '';
                tile.style.boxShadow = '';
            }, 150);
            
            // Enhanced toast notification
            showEnhancedToast(key, tile);
        });

        function showEnhancedToast(key, element) {
            const existingToast = document.querySelector('.shortcut-toast');
            if (existingToast) existingToast.remove();
            
            const toast = document.createElement('div');
            toast.className = 'shortcut-toast position-fixed top-0 start-50 translate-middle-x mt-4';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow" 
                     style="background: linear-gradient(135deg, var(--g3), var(--g1)); color: white; font-weight: 600;">
                    <i class="fas fa-bolt"></i>
                    <span>Shortcut Activated: ${key}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove with fade out
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 300);
            }, 1500);
        }

        // Add hover effects for cards
        document.querySelectorAll('.stat-card').forEach(card => {
            const gradient = card.getAttribute('data-gradient');
            if (gradient) {
                card.style.setProperty('--gradient', gradient);
            }
        });
    </script>
</div>