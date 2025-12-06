<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboardsample extends Component
{
    public $stats = [
        'total_quotes' => ['value' => 156, 'change' => '+12%', 'trend' => 'up'],
        'total_purchases' => ['value' => 89, 'change' => '+5%', 'trend' => 'up'],
        'pending_confirmations' => ['value' => 23, 'change' => '-3%', 'trend' => 'down'],
        'total_invoices' => ['value' => 267, 'change' => '+18%', 'trend' => 'up'],
        'open_orders' => ['value' => 45, 'change' => '+8%', 'trend' => 'up'],
        'stock_alerts' => ['value' => 12, 'change' => '+2', 'trend' => 'up']
    ];

    public $recentActivities = [
        ['type' => 'quote', 'description' => 'New quote #Q-2345 created for ABC Electronics', 'time' => '2 mins ago', 'icon' => 'fa-file-alt', 'color' => 'primary'],
        ['type' => 'purchase', 'description' => 'Purchase order #PO-6789 approved by manager', 'time' => '15 mins ago', 'icon' => 'fa-shopping-cart', 'color' => 'success']
    ];

    public $quickShortcuts = [
        ['key' => 'Q', 'label' => 'Add New Quote', 'icon' => 'fa-file-alt', 'color' => 'primary', 'route' => '#'],
        ['key' => 'P', 'label' => 'Add Purchase', 'icon' => 'fa-shopping-cart', 'color' => 'success', 'route' => '#'],
        ['key' => 'C', 'label' => 'Add Order Confirmation', 'icon' => 'fa-check-circle', 'color' => 'info', 'route' => '#'],
        ['key' => 'S', 'label' => 'Add Packing Slip', 'icon' => 'fa-box', 'color' => 'warning', 'route' => '#'],
        ['key' => 'I', 'label' => 'Add Invoice', 'icon' => 'fa-receipt', 'color' => 'danger', 'route' => '#'],
        ['key' => 'R', 'label' => 'Status Report', 'icon' => 'fa-chart-bar', 'color' => 'secondary', 'route' => '#'],
    ];

    public $managementShortcuts = [
        ['key' => 'M', 'label' => 'Manage Quotes', 'icon' => 'fa-tasks', 'color' => 'primary', 'route' => '#'],
        ['key' => 'O', 'label' => 'Manage Purchase', 'icon' => 'fa-clipboard-list', 'color' => 'success', 'route' => '#'],
        ['key' => 'V', 'label' => 'Manage Order Confirmation', 'icon' => 'fa-clipboard-check', 'color' => 'info', 'route' => '#'],
        ['key' => 'D', 'label' => 'Manage Packing Slip', 'icon' => 'fa-boxes', 'color' => 'warning', 'route' => '#'],
        ['key' => 'U', 'label' => 'Manage Invoice', 'icon' => 'fa-file-invoice', 'color' => 'danger', 'route' => '#'],
        ['key' => 'N', 'label' => 'Manage Stock', 'icon' => 'fa-pallet', 'color' => 'dark', 'route' => '#']
    ];

    public function render()
    {
        return view('livewire.dashboardsample')->layout('layouts.app', ['title' => 'Dashboard']);
    }
}