<?php

//use \Livewire\Livewire::routes();
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditPdfController;
use App\Http\Controllers\invoicepdfController as invoicepdf;
use App\Livewire\Dashboard;
use App\Livewire\Login;

use App\Livewire\Qoutes\Add as QuoteAdd;
use App\Livewire\Qoutes\Manage as QuoteManage;
use App\Livewire\Qoutes\Edit as Edit;
use App\Livewire\Qoutes\Project\Add as ProjectQuoteAdd;
use App\Livewire\Qoutes\Project\Manage as ProjectQuoteManage;
use App\Livewire\Qoutes\Reminders as Reminder;

use App\Livewire\PurchaseOrder\Add as POAdd;
use App\Livewire\PurchaseOrder\Manage as POManage;
use App\Livewire\PurchaseOrder\Edit as POEdit;
use App\Livewire\PurchaseOrder\Cancelled as POCancelled;
use App\Livewire\PurchaseOrder\Duplicateasremark as PODuplicatesRemark;

use App\Livewire\ConfirmationOrders\Add as ConfirmationAdd;
use App\Livewire\ConfirmationOrders\Manage as ConfirmationManage;
use App\Livewire\ConfirmationOrders\Edit as ConfirmationEdit;

use App\Livewire\PackingSlips\Add as PackingAdd;
use App\Livewire\PackingSlips\Manage as PackingManage;
use App\Livewire\PackingSlips\Edit as Editpacking;
use App\Livewire\PackingSlips\Loged as packingloged;



use App\Livewire\Invoice\Add as InvoiceAdd;
use App\Livewire\Invoice\Manage as InvoiceManage;
use App\Livewire\Invoice\Edit as InvoiceEdit;

use App\Livewire\Credit\Add as CreditAdd;
use App\Livewire\Credit\Manage as CreditManage;
use App\Livewire\Credit\Editcredit as Editcredit;

use App\Livewire\Reports\FinancialReport;
use App\Livewire\Reports\StatusReport;
use App\Livewire\Reports\OldStatus;
use App\Livewire\Reports\PosaSearch;
use App\Livewire\Reports\ManagePosa;
use App\Livewire\Reports\PastDueInvoice;
use App\Livewire\Reports\Comissions;

use App\Livewire\Customers\AddCustomers;
use App\Livewire\Customers\ManageCustomers;
use App\Livewire\Customers\EditCustomers;

use App\Livewire\Customers\Eng\AddEngContacts;
use App\Livewire\Customers\Eng\ManageEngContacts;
use App\Livewire\Customers\Eng\Editengcontact;

use App\Livewire\Customers\Main\AddContact;
use App\Livewire\Customers\Main\ManageMainContact;
use App\Livewire\Customers\Main\Editmaincustomer as Emc;

use App\Livewire\Customers\Profile\Add as ProfileAdd;
use App\Livewire\Customers\Profile\Manage as ProfileManage;
use App\Livewire\Customers\Profile\Edit as ProfileEdit;

use App\Livewire\Customers\Sales\Add as SalesAdd;
use App\Livewire\Customers\Sales\ManagSalesRep;
use App\Livewire\Customers\Sales\Editmanagersales as editmanagersales;

use App\Livewire\Customers\Alerts\AddPartNumberAlerts;
use App\Livewire\Customers\Alerts\ManagePartNumberAlerts;
use App\Livewire\Customers\Alerts\Editpartnumber;


use App\Livewire\Vendors\Add as VendorAdd;
use App\Livewire\Vendors\Manage as VendorManage;
use App\Livewire\Vendors\Edit as EditVendor;

use App\Livewire\Vendors\Eng\AddContact as VendorEngAdd;
use App\Livewire\Vendors\Eng\ManageContact as VendorEngManage;
use App\Livewire\Vendors\Eng\Editcontact as editVendorEngManage;

use App\Livewire\Vendors\Main\Add as VendorMainAdd;
use App\Livewire\Vendors\Main\Manage as VendorMainManage;
use App\Livewire\Vendors\Main\Edit as VendorsMainEdit;

use App\Livewire\Vendors\Profile\Add as VendorProfileAdd;
use App\Livewire\Vendors\Profile\Manage as VendorProfileManage;
use App\Livewire\Vendors\Profile\Edit as Vendorprofileedit;

use App\Livewire\Shippers\Add as ShipperAdd;
use App\Livewire\Shippers\Manage as ShipperManage;
use App\Livewire\Shippers\Edit as Shipperedit;

use App\Livewire\Users\Add as UserAdd;
use App\Livewire\Users\ChangePassword;
use App\Livewire\Users\Manage as UserManage;

use App\Livewire\Misc\Labels;
use App\Livewire\Misc\DownloadQa;
use App\Livewire\Misc\DownloadMaterialCert;
use App\Livewire\Misc\AddStock;
use App\Livewire\Misc\ManageStock;
use App\Livewire\Misc\Editstock;
use App\Livewire\Misc\StockReport;
use App\Livewire\Misc\ManageNotes;
use App\Livewire\Misc\Editnote as Editnote;
use App\Livewire\Misc\PoFileUpload;
use App\Livewire\Misc\Pofileuploadswork as pofileuploadswork;
use App\Livewire\Misc\OrderPlacedReport;
use App\Livewire\Misc\PackingSlipsReport;
use App\Livewire\Misc\RecevingLog;
use App\Livewire\Misc\Editlogged as Editlogged;

Route::get('/', Login::class)->name('login');

Route::middleware('auth')->group(function(){
        // Dashboard
        Route::get('/dashboard', Dashboard::class)->name('dashboard');

        // Quotes
        Route::prefix('qoute')->group(function(){
            Route::get('/add', QuoteAdd::class)->name('add.qoutes');
            Route::get('/manage', QuoteManage::class)->name('qoutes.manage');
            Route::get('/edit/{id}', Edit::class)->name('qoutes.edit');
            Route::prefix('project')->name('quotes.project.')->group(function () {
                Route::get('add', ProjectQuoteAdd::class)->name('add');
                Route::get('manage', ProjectQuoteManage::class)->name('manage');
            });
            Route::get('/reminders',Reminder::class)->name('qoute.reminder');
        });

        // Purchase - Orders
        Route::prefix('purchase-orders')->name('purchase.orders.')->group(function () {
            Route::get('add', POAdd::class)->name('add');
            Route::get('manage', POManage::class)->name('manage');
            Route::get('/edit/{id}',POEdit::class)->name('edit');
            Route::get('cancelled', POCancelled::class)->name('cancelled');
            Route::get('duplicates-remark', PODuplicatesRemark::class)->name('duplicates-remark');
        });

        // Confirmation Orders
        Route::prefix('confirmation-orders')->name('confirmation.')->group(function () {
            Route::get('add', ConfirmationAdd::class)->name('add');
            Route::get('manage', ConfirmationManage::class)->name('manage');
            Route::get('/edit/{id}',ConfirmationEdit::class)->name('edit');
        });

        // Packing Slips
        Route::prefix('packing-slips')->name('packing.')->group(function () {
            Route::get('add', PackingAdd::class)->name('add');
            Route::get('manage', PackingManage::class)->name('manage');
            Route::get('edit/{id}',Editpacking::class)->name('edit');
            Route::get('logged-in/{invoice_id}',packingloged::class)->name('loggedin');
        });

        // Invoice
        Route::prefix('invoice')->name('invoice.')->group(function () {
            Route::get('add', InvoiceAdd::class)->name('add');
            Route::get('manage', InvoiceManage::class)->name('manage');
            Route::get('edit/{id}', InvoiceEdit::class)->name('edit');
        });

        // Credit
        Route::prefix('credit')->name('credit.')->group(function () {
            Route::get('add', CreditAdd::class)->name('add');
            Route::get('manage', CreditManage::class)->name('manage');
            Route::get('edit/{credit}',Editcredit::class)->name('edit');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('financial', FinancialReport::class)->name('financial');
            Route::get('status-report', StatusReport::class)->name('status-report');
            Route::get('old-status', OldStatus::class)->name('old-status');
            Route::get('posa-search', PosaSearch::class)->name('posa-status');
            Route::get('manage-posa', ManagePosa::class)->name('posa');
            Route::get('past-due-voice', PastDueInvoice::class)->name('past-deliv');
            Route::get('commissions', Comissions::class)->name('commissions');
        });

        Route::prefix('customers')->group(function(){
            Route::get('add', AddCustomers::class)->name('add-customers');
            Route::get('manage', ManageCustomers::class)->name('manage-customers');
            Route::get('/edit/{id}', EditCustomers::class)->name('customers.edit');

            Route::prefix('eng')->name('customers.eng.')->group(function () {
                Route::get('add-contacts', AddEngContacts::class)->name('add');
                Route::get('/edit-contact/{id}',Editengcontact::class)->name('edit');
                Route::get('manage-contacts', ManageEngContacts::class)->name('manage');
            });
            Route::prefix('main')->name('customers.main.')->group(function () {
                Route::get('add-contact', AddContact::class)->name('add');
                Route::get('/edit-contact/{id}',Emc::class)->name('edit');
                Route::get('manage-main-contact', ManageMainContact::class)->name('manage');
            });
            Route::prefix('profile')->name('customers.profile.')->group(function () {
                Route::get('add', ProfileAdd::class)->name('add');
                Route::get('manage', ProfileManage::class)->name('manage');
                Route::get('/edit/{id}',ProfileEdit::class)->name('edit');
            });
            Route::prefix('sales')->name('customers.sales.')->group(function () {
                Route::get('add', SalesAdd::class)->name('add');
                Route::get('manage-sales-rep', ManagSalesRep::class)->name('manage-rep');
                Route::get('edit/{id}', editmanagersales::class)->name('manage.edit'); 
            });
            Route::prefix('alerts')->name('customers.alerts.')->group(function () {
                Route::get('add-part-number-alerts', AddPartNumberAlerts::class)->name('add-part');
                Route::get('manage-part-number-alerts', ManagePartNumberAlerts::class)->name('manage-part');
               // Route::get('edit/{id}', Editpartnumber::class)->name('edit');                
                Route::get('/edit/{customer?}/{part?}/{rev?}',
                    Editpartnumber::class
                )->name('edit');
            });
        });

        // Vendors
        Route::prefix('vendor')->group(function(){
            Route::get('/add', VendorAdd::class)->name('add.vendor');
            Route::get('/Manage', VendorManage::class)->name('manage.vendor');
            Route::get('/edit/{id}',EditVendor::class)->name('vendor.edit');
            Route::prefix('eng')->name('vendors.eng.')->group(function () {
                Route::get('add', VendorEngAdd::class)->name('add');
                Route::get('manage', VendorEngManage::class)->name('manage');
            });
            Route::prefix('main')->name('vendors.main.')->group(function () {
                Route::get('add', VendorMainAdd::class)->name('add');
                Route::get('manage', VendorMainManage::class)->name('manage');
                Route::get('edit/{id}',VendorsMainEdit::class)->name('edit');
            });
            Route::prefix('profile')->name('vendors.profile.')->group(function () {
                Route::get('add', VendorProfileAdd::class)->name('add');
                Route::get('manage', VendorProfileManage::class)->name('manage');
                 Route::get('/edit/{profid}',Vendorprofileedit::class)->name('edit');
            });
        });

        // Shippers
        Route::prefix('shippers')->name('shippers.')->group(function () {
            Route::get('add', ShipperAdd::class)->name('add');
            Route::get('manage', ShipperManage::class)->name('manage');
            Route::get('/edit/{id}',Shipperedit::class)->name('edit');
        });

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('add', UserAdd::class)->name('add');
            Route::get('change-password', ChangePassword::class)->name('change-password');
            Route::get('manage', UserManage::class)->name('manage');
        });

        // Misc
        Route::prefix('misc')->name('misc.')->group(function () {
            Route::get('labels', Labels::class)->name('labels');
            Route::get('download-qa', DownloadQa::class)->name('download-qa');
            Route::get('download-material-cert', DownloadMaterialCert::class)->name('download-cert');
            Route::get('add-stock', AddStock::class)->name('add-stock');
            Route::get('manage-stock', ManageStock::class)->name('manage-stock');
            Route::get('/edit-stock/{id}',Editstock::class)->name('edit.stock');
            Route::get('stock-report', StockReport::class)->name('stock-report');
            Route::get('manage-notes', ManageNotes::class)->name('manage-notes');
            Route::get('manage-notes/edit/{ntype}', Editnote::class)->name('manage-notes.edit');
            Route::get('po-file-upload', PoFileUpload::class)->name('po-upload');
            Route::get('/po-upload/{customer?}/{part_no?}/{rev?}', Pofileuploadswork::class)
    ->name('po-file-upload-work');
            Route::get('order-placed-report', OrderPlacedReport::class)->name('order-report');
            Route::get('packing-slips-report', PackingSlipsReport::class)->name('packing-report');
            Route::get('receiving-log', RecevingLog::class)->name('receiving-log');
            Route::get('/logged-packing-slip/{log_id}',Editlogged::class)->name('edit.logged');
        });

});
// for view pdf for credit ..  
Route::get('/credit/pdf/{id}', [CreditPdfController::class, 'view'])->name('credit.pdf');
Route::get('/credit/pdf/download/{id}', [CreditPdfController::class, 'download'])->name('credit.pdf.download');
// for view and download invoice ..
Route::get('/invoice/pdf/{id}',[CreditPdfController::class,'viewinvoicepdf'])->name('invoice.pdf');
Route::get('/invoice/pdf/download/{id}',[CreditPdfController::class,'downloadpdfinvoice'])->name('invoice.pdf.download');
Route::get('/invoice/docs/download/{id}',[CreditPdfController::class,'downloaddoc'])->name('invoice.docs.download');
// pdf and docs view and download for packing slip
Route::get('/packing-slip/{id}',[CreditPdfController::class,'viewpackingpdf'])->name('view.packingpdf');
Route::get('/download/{id}',[CreditPdfController::class,'downloadpackingpdf'])->name('download.packingpdf');
Route::get('/download/docs/{id}',[CreditPdfController::class,'downloadPackingDoc'])->name('download.packingdocs');

// pdf and docs view and download for confirmation order ..
Route::get('/confirmation-order/pdf/{id}',[CreditPdfController::class,'vieworderconfirmationpdf'])->name('view.confirmationorder');
Route::get('/confirmation-order/pdf/download/{id}',[CreditPdfController::class,'downloadorderconfirmationpdf'])->name('download.confirmationorder');
Route::get('/confirmation-order/download/docs/{id}',[CreditPdfController::class,'downloadorderconfirmationdoc'])->name('download.confirmationorderdoc');
// pdf and docs view and download for purchase order ..
Route::get('/purchase-order/pdf/{id}',[CreditPdfController::class,'viewpurchaseorder'])->name('view.purchaseorder'); // purchase order ..
Route::get('/purchase-order/pdf/download/{id}',[CreditPdfController::class,'downloadpurchaseorder'])->name('download.purchaseorder'); // purchaser order downloa pdf ..
Route::get('/purchase-order/download/docs/{id}',[CreditPdfController::class,'downloaddocpurchaseorder'])->name('downloaddoc.purchaseorder'); // download doc purchase order ..
// pdf and docs view and download for qoute section ..
Route::get('/qoute/pdf/{id}',[CreditPdfController::class,'viewPdfqoute'])->name('view.viewPdfqoute'); // purchase order ..
Route::get('/qoute/pdf/download/{id}',[CreditPdfController::class,'downloadPdfqoute'])->name('download.downloadPdfqoute'); // purchaser order downloa pdf ..
Route::get('/qoute/download/docs/{id}',[CreditPdfController::class,'viewdocqoute'])->name('downloaddoc.viewdocqoute'); // download doc purchase order ..
// Legacy PDF quote route
// pdf and docs view and download for qoute section ..
Route::get('/logout',function(){
    Auth::logout();
    return redirect(route('login'))->with('error','Thanks For Using Globalpcbs');
})->name('logout');