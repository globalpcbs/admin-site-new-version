<div class="br-logo"><a href="#"><span>[</span>Pcbsglobals<span>]</span></a></div>
<div class="br-sideleft overflow-y-auto">
    <label class="sidebar-label pd-x-15 mg-t-10">Navigation</label>
    <div class="br-sideleft-menu">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="br-menu-link">
            <div class="br-menu-item">
                <i class="menu-item-icon icon ion-ios-home-outline tx-22"></i>
                <span class="menu-item-label">Dashboard</span>
            </div>
        </a>

        {{-- Quotes --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-quote-left"></i>
                <span class="menu-item-label">Quotes</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('add.qoutes') }}" class="nav-link">Add Quote</a></li>
            <li class="nav-item"><a href="{{ route('qoutes.manage') }}" class="nav-link">Manage Quote</a></li>
            <div class="sidebar-divider my-2"></div>
            <li class="nav-item"><a href="{{ route('quotes.project.add') }}" class="nav-link">Add Project</a></li>
            <li class="nav-item"><a href="{{ route('quotes.project.manage') }}" class="nav-link">Manage Project</a>
            </li>
            <div class="sidebar-divider my-2"></div>
            <li class="nav-item"><a href="{{ route('qoute.reminder') }}" class="nav-link">Manage Reminders</a></li>
        </ul>

        {{-- Purchase Orders --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-tags"></i>
                <span class="menu-item-label">Purchase Orders</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('purchase.orders.add') }}" class="nav-link">Add Purchase
                    Order</a></li>
            <li class="nav-item"><a href="{{ route('purchase.orders.manage') }}" class="nav-link">Manage Purchase
                    Order</a></li>
            <li class="nav-item"><a href="{{ route('purchase.orders.cancelled') }}" class="nav-link">Cancelled
                    Orders</a></li>
            <!-- <li class="nav-item"><a href="{{ route('purchase.orders.duplicates-remark') }}" class="nav-link">Duplicate
                    As Remake</a></li> -->
        </ul>

        {{-- Confirmation Orders --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-clipboard"></i>
                <span class="menu-item-label">Confirmation Orders</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('confirmation.add') }}" class="nav-link">Add Order
                    Confirmation</a></li>
            <li class="nav-item"><a href="{{ route('confirmation.manage') }}" class="nav-link">Manage Order Conf</a>
            </li>
        </ul>

        {{-- Packing Slips --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-archive"></i>
                <span class="menu-item-label">Packing Slips</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('packing.add') }}" class="nav-link">Add Packing Slips</a></li>
            <li class="nav-item"><a href="{{ route('packing.manage') }}" class="nav-link">Manage Packing Slips</a>
            </li>
        </ul>

        {{-- Invoices --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-file-text"></i>
                <span class="menu-item-label">Invoices</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('invoice.add') }}" class="nav-link">Add Invoices</a></li>
            <li class="nav-item"><a href="{{ route('invoice.manage') }}" class="nav-link">Manage Invoices</a></li>
        </ul>
        {{-- Credit --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-credit-card"></i>
                <span class="menu-item-label">Credit</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('credit.add') }}" class="nav-link">Add Credit Offset</a></li>
            <li class="nav-item"><a href="{{ route('credit.manage') }}" class="nav-link">Manage Credit Offset</a>
            </li>
        </ul>

        {{-- Reports --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-bar-chart"></i>
                <span class="menu-item-label">Reports</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('reports.status-report') }}" class="nav-link">Status Report</a>
            </li>
            <li class="nav-item"><a href="{{ route('reports.commissions') }}" class="nav-link">Commissions</a></li>
        </ul>

        {{-- Customers --}}
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-users"></i>
                <span class="menu-item-label">Customers</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('add-customers') }}" class="nav-link">Add Customers</a></li>
            <li class="nav-item"><a href="{{ route('manage-customers') }}" class="nav-link">Manage Customers</a>
            </li>
            <div class="sidebar-divider my-2"></div>
            {{-- Engineering Contacts --}}
            <li class="nav-item"><a href="{{ route('customers.eng.add') }}" class="nav-link">Add Eng Contacts</a>
            </li>
            <li class="nav-item"><a href="{{ route('customers.eng.manage') }}" class="nav-link">Manage Eng
                    Contacts</a></li>
            <div class="sidebar-divider my-2"></div>
            {{-- Main Contacts --}}
            <li class="nav-item"><a href="{{ route('customers.main.add') }}" class="nav-link">Add Main Contact</a>
            </li>
            <li class="nav-item"><a href="{{ route('customers.main.manage') }}" class="nav-link">Manage Main
                    Contact</a></li>
            <div class="sidebar-divider my-2"></div>
            {{-- Profile --}}
            <li class="nav-item"><a href="{{ route('customers.profile.add') }}" class="nav-link">Add Profile</a>
            </li>
            <li class="nav-item"><a href="{{ route('customers.profile.manage') }}" class="nav-link">Manage
                    Profile</a></li>
            <div class="sidebar-divider my-2"></div>
            {{-- Alerts --}}
            <li class="nav-item"><a href="{{ route('customers.alerts.add-part') }}" class="nav-link">Add Part Number
                    Alerts</a></li>
            <li class="nav-item"><a href="{{ route('customers.alerts.manage-part') }}" class="nav-link">Manage Part
                    Number Alerts</a></li>
            <div class="sidebar-divider my-2"></div>
            {{-- Sales --}}
            <li class="nav-item"><a href="{{ route('customers.sales.add') }}" class="nav-link">Add Sales Rep</a>
            </li>
            <li class="nav-item"><a href="{{ route('customers.sales.manage-rep') }}" class="nav-link">Manage Sales
                    Rep</a></li>
        </ul>
        <!-- Vendors -->
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-truck"></i>
                <span class="menu-item-label">Vendors</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('add.vendor') }}" class="nav-link">Add Vendor</a></li>
            <li class="nav-item"><a href="{{ route('manage.vendor') }}" class="nav-link">Mng Vendor</a></li>
            <div class="sidebar-divider my-2"></div>
            <li class="nav-item"><a href="{{ route('vendors.eng.add') }}" class="nav-link">Add Vendor Eng
                    Contact</a></li>
            <li class="nav-item"><a href="{{ route('vendors.eng.manage') }}" class="nav-link">Manage Vendor Eng
                    Contact</a></li>
            <div class="sidebar-divider my-2"></div>
            <li class="nav-item"><a href="{{ route('vendors.main.add') }}" class="nav-link">Add Main Contact</a>
            </li>
            <li class="nav-item"><a href="{{ route('vendors.main.manage') }}" class="nav-link">Manage Main
                    Contact</a></li>
            <div class="sidebar-divider my-2"></div>
            <li class="nav-item"><a href="{{ route('vendors.profile.add') }}" class="nav-link">Add Vendor
                    Profile</a></li>
            <li class="nav-item"><a href="{{ route('vendors.profile.manage') }}" class="nav-link">Manage Vendor
                    Profile</a></li>
        </ul>

        <!-- Shippers -->
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-ship"></i>
                <span class="menu-item-label">Shippers</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('shippers.add') }}" class="nav-link">Add Shippers</a></li>
            <li class="nav-item"><a href="{{ route('shippers.manage') }}" class="nav-link">Manage Shippers</a></li>
        </ul>

        <!-- User Management -->
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-user"></i>
                <span class="menu-item-label">User Management</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('users.add') }}" class="nav-link">Add User</a></li>
            <li class="nav-item"><a href="{{ route('users.manage') }}" class="nav-link">Manage Users</a></li>
            <li class="nav-item"><a href="{{ route('users.change-password') }}" class="nav-link">Change Password</a>
            </li>
        </ul>

        <!-- Misc -->
        <a href="#" class="br-menu-link">
            <div class="br-menu-item">
                <i class="fa fa-cogs"></i>
                <span class="menu-item-label">Misc</span>
                <i class="fa fa-angle-down menu-item-arrow"></i>
            </div>
        </a>
        <ul class="br-menu-sub nav flex-column">
            <li class="nav-item"><a href="{{ route('misc.add-stock') }}" class="nav-link">Add Stock</a></li>
            <li class="nav-item"><a href="{{ route('misc.manage-stock') }}" class="nav-link">Manage Stock</a></li>
            <li class="nav-item"><a href="{{ route('misc.stock-report') }}" class="nav-link">Stock Report</a></li>
            <li class="nav-item"><a href="{{ route('misc.manage-notes') }}" class="nav-link">Manage Notes</a></li>
            <div class="sidebar-divider my-2"></div>
            <li class="nav-item"><a href="{{ route('misc.po-upload') }}" class="nav-link">PO File Upload</a></li>
            <li class="nav-item"><a href="{{ route('misc.receiving-log') }}" class="nav-link">Receiving Log</a></li>
        </ul>

        <a href="{{ route('logout') }}" class="text-white">
            <div class="br-menu-item text-danger">
                <i class="fa fa-sign-out"></i>
                <span class="menu-item-label">Log Out</span>
            </div>
        </a>
    </div>
</div>

<label class="sidebar-label pd-x-15 mg-t-25 mg-b-20 tx-info op-9">Information Summary</label>
<div class="br-header">
    <div class="br-header-left">
        <div class="navicon-left hidden-md-down"><a id="btnLeftMenu" href=""><i class="icon ion-navicon-round"></i></a>
        </div>
        <div class="navicon-left hidden-lg-up"><a id="btnLeftMenuMobile" href=""><i
                    class="icon ion-navicon-round"></i></a></div>

    </div><!-- br-header-left -->
    <div class="br-header-right">
        <nav class="nav">

            <div class="dropdown">
                <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
                    <span class="logged-name hidden-md-down">Asad Mukhtar</span>
                    <img src="http://via.placeholder.com/64x64" class="wd-32 rounded-circle" alt="">
                    <span class="square-10 bg-success"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-header wd-200">
                    <ul class="list-unstyled user-profile-nav">
                        <li><a href=""><i class="icon ion-ios-person"></i> Change Password </a></li>
                        <li><a href="{{ route('logout') }}"><i class="icon ion-power"></i> Sign Out</a></li>
                    </ul>
                </div><!-- dropdown-menu -->
            </div><!-- dropdown -->
        </nav>
    </div><!-- br-header-right -->
</div><!-- br-header -->
<!-- br-sideright -->
<div class="br-mainpanel">
    <div class="br-content">
        <div class="content-wrapper">
            {{-- Menu --}}
            <div class="container-fluid ">
                {{$slot}}
            </div>
        </div>
    </div>
</div>