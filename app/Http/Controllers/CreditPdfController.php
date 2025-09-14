<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Http\Request;
use App\Models\porder_tb;
use App\Models\vendor_tb;
use App\Models\notes_tb;
use App\Models\items_tb;
use App\Models\corder_tb;
use App\Models\order_tb;
use App\Models\citems_tb;
use App\Models\mdlitems_tb;
use App\Models\shipper_tb;
use App\Models\credit_tb;
use App\Models\packing_tb;
use App\Models\maincont_tb;
use App\Models\data_tb;
use App\Models\invoice_tb as Invoice;
use App\Models\order_tb as Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response; // Add this import
use Carbon\Carbon;
use DB;

class CreditPdfController extends Controller
{
    public function view($id)
    {
        // Load credit record with related items
        $credit = credit_tb::with('items','custo')->findOrFail($id);
        $temp = $credit->sid;
        $temp1 = substr($temp, 0, 1);  
        $temp2 = substr($temp,1, strlen($temp));
        $temp2 = intval($temp2);
    //    dd($temp1);
        if($temp1 == "c"){
            $shipper = data_tb::find($temp2);
        } else if ($temp1 == "s"){
            $shipper = shipper_tb::find($temp2);
        }
        // Calculate subtotal, tax, and total
        $subtotal = $credit->items->sum('tprice');
        $tax = $subtotal * (float) $credit->saletax;
        $total = $subtotal + $tax + (float) $credit->fcharge;
        $filename = "credit-" . ($credit->custo?->c_name ?? 'unknown')
        . '-' . $credit->part_no
        . '-' . $credit->rev
        . '-' . date("m-d-y") . ".pdf";
        // Load PDF view
        $pdf = Pdf::loadView('pdf.credit', [
            'credit'   => $credit,
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
            'shipper' => $shipper,
            'title' => $filename
        ]);

        // Stream the PDF in browser
        return $pdf->stream($filename);
       // return $pdf->download("Credit-{$credit->credit_id}.pdf");
    }
    public function download($id)
    {
        // Load credit record with related items
        $credit = credit_tb::with('items','custo')->findOrFail($id);
        $temp = $credit->sid;
        $temp1 = substr($temp, 0, 1);  
        $temp2 = substr($temp,1, strlen($temp));
        $temp2 = intval($temp2);
        if($temp1 == "c"){
            $shipper = data_tb::find($temp2);
        } else if ($temp1 == "s"){
            $shipper = shipper_tb::find($temp2);
        }
        // Calculate subtotal, tax, and total
        $subtotal = $credit->items->sum('tprice');
        $tax = $subtotal * (float) $credit->saletax;
        $total = $subtotal + $tax + (float) $credit->fcharge;
        $filename = "credit-".$credit->custo->c_name.'-'.$credit->part_no.'-'.$credit->rev.'-'.date("m-d-y").".pdf";
        
        // Load PDF view
        $pdf = Pdf::loadView('pdf.credit', [
            'credit'   => $credit,
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $total,
            'shipper' => $shipper,
            'title' => $filename
        ]);

        // Stream the PDF in browser
        //return $pdf->stream("Credit-{$credit->credit_id}.pdf");
        //$filename = "credit-$inv-$rws[customer]-$rws[part_no]-$rws[rev] ".date("m-d-Y") . ".pdf";
        return $pdf->download($filename);
    }
    // for view invoice pdf ..
    public function viewinvoicepdf($id){
        $invoice = Invoice::with(['items', 'custo'])->findOrFail($id);
        $temp = $invoice->sid;
        $temp1 = substr($temp, 0, 1);  
        $temp2 = substr($temp,1, strlen($temp));
        $temp2 = intval($temp2);
        if($temp1 == "c"){
            $shipper = data_tb::find($temp2);
        } else if ($temp1 == "s"){
            $shipper = shipper_tb::find($temp2);
        }
       // return $shipper;

        $subtotal = $invoice->items->sum('tprice');
        $tax = (float)$invoice->saletax;
        $freight = (float)$invoice->fcharge;
        $total = $subtotal + $tax + $freight;

         $pdf = Pdf::loadView('pdf.invoice', [
            'title' => 'Invoice View',
            'invoice' => $invoice,
            'shipper' => $shipper,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
        $filename = "invoice-".$invoice->custo->c_name.'-'.$invoice->part_no.'-'.$invoice->rev.'-'.date("m-d-y").".pdf";
        return $pdf->stream($filename);
    }
    // for download invoice pdf ..
    public function downloadpdfinvoice($id){
        $invoice = Invoice::with(['items', 'custo'])->findOrFail($id);
        $temp = $invoice->sid;
        $temp1 = substr($temp, 0, 1);  
        $temp2 = substr($temp,1, strlen($temp));
        $temp2 = intval($temp2);
        if($temp1 == "c"){
            $shipper = data_tb::find($temp2);
        } else if ($temp1 == "s"){
            $shipper = shipper_tb::find($temp2);
        }
       // return $shipper;

        $subtotal = $invoice->items->sum('tprice');
        $tax = (float)$invoice->saletax;
        $freight = (float)$invoice->fcharge;
        $total = $subtotal + $tax + $freight;

         $pdf = Pdf::loadView('pdf.invoice', [
            'title' => 'Invoice View',
            'invoice' => $invoice,
            'shipper' => $shipper,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
        $filename = "invoice-".$invoice->custo->c_name.'-'.$invoice->part_no.'-'.$invoice->rev.'-'.date("m-d-y").".pdf";
        return $pdf->download($filename);
    }
    // download doc file ..

public function downloadDoc($id)
{
    $invoice = Invoice::with(['items', 'custo'])->findOrFail($id);
    $shipper = shipper_tb::find($invoice->sid);

    $subtotal = $invoice->items->sum('tprice');
    $tax = (float)$invoice->saletax;
    $freight = (float)$invoice->fcharge;
    $total = $subtotal + $tax + $freight;
    $invoiceNumber = $invoice->invoice_id + 9976;
    $invoiceDate = Carbon::parse($invoice->podate)->format('m/d/Y');

    // Generate HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Invoice</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 10pt;
                line-height: 1.0;
                margin: 0;
                padding: 0;
            }
            .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
            .header-table td { vertical-align: top; padding: 2px; }
            .logo { width: 40px; height: 30px; }
            .doc-title { font-size: 40pt; font-weight: bold; color: #5660B1; margin-bottom: 5px; }
            .company-info { font-size: 10pt; }
            .section-bar { background: #656BBC; color: #fff; font-weight: bold; padding: 4px; }
            .info-table { width: 100%; border-collapse: collapse; }
            .address-table { width: 100%; margin: 10px 0; }
            .address-table td { vertical-align: top; padding: 2px; }
            .items-table { width: 100%; border-collapse: collapse; border: 2px solid #000; margin: 10px 0; }
            .items-table th { background: #656BBC; color: #fff; padding: 4px; }
            .items-table td { border: 1px solid #000; padding: 4px; }
            .totals-table { width: 100%; margin-top: 10px; }
            .bold { font-weight: bold; }
            .text-right { text-align: right; }
            .footer { margin-top: 20px; font-size: 9pt; }
            .footer-table { width: 100%; }
            .footer-table td { vertical-align: top; width: 50%; padding: 5px; }
        </style>
    </head>
    <body>
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td style="width:40px;">
                    <img src="'.public_path('images/logo.png').'" class="logo">
                </td>
                <td style="width:20%;"></td>
                <td>
                    <div class="doc-title">Invoice</div>
                    <div class="company-info">
                        INVOICE NUMBER: '.$invoiceNumber.'<br>
                        INVOICE DATE: '.$invoiceDate.'<br>
                        OUR ORDER NO: '.$invoice->our_ord_num.'<br>
                        YOUR ORDER NO: '.$invoice->po.'<br>
                        TERMS: '.$invoice->sterm.'<br>
                        SALES REP: '.$invoice->namereq.'<br>
                        SHIPPED VIA: '.($invoice->svia === 'Other' ? $invoice->svia_oth : $invoice->svia).'<br>
                        F.O.B: Anaheim CA
                    </div>
                </td>
            </tr>
        </table>

        <!-- COMPANY INFO -->
        <div class="company-info">
            <div class="bold">PCBs Global Incorporated</div>
            <div>2500 E. La Palma Ave.</div>
            <div>Anaheim Ca. 92806</div>
            <div>Phone: (855) 722-7456</div>
            <div>Fax: (855) 262-5305</div>
        </div>

        <!-- SOLD TO / SHIPPED TO -->
        <table class="address-table">
            <tr>
                <td style="width:45%;">
                    <div class="section-bar">SOLD TO</div>
                    <div>'.($invoice->custo->c_name ?? '').'</div>
                    <div>'.($invoice->custo->c_address ?? '').'</div>
                    <div>'.($invoice->custo->c_address2 ?? '').'</div>
                    <div>'.($invoice->custo->c_address3 ?? '').'</div>
                    <div>Phone: '.($invoice->custo->c_phone ?? '').'</div>
                    <div>Fax: '.($invoice->custo->c_fax ?? '').'</div>
                    <div>'.($invoice->custo->c_website ?? '').'</div>
                </td>
                <td style="width:10%;"></td>
                <td style="width:45%;">
                    <div class="section-bar">SHIPPED TO</div>
                    '.($invoice->ord_by ? '<div>Ordered by: '.$invoice->ord_by.'</div>' : '').'
                    '.($invoice->delto ? '<div>Delivered to: '.$invoice->delto.'</div>' : '').'
                    '.($invoice->date1 ? '<div>Delivered On: '.$invoice->date1.'</div>' : '').'
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <tr>
                <th style="width:10%;">ITEM #</th>
                <th style="width:35%;">DESCRIPTION</th>
                <th style="width:10%;">QTY</th>
                <th style="width:10%;">UNIT PRICE</th>
                <th style="width:10%;">TOTAL</th>
            </tr>';

    foreach ($invoice->items as $item) {
        $desc = "P/N ".$invoice->part_no." Rev ".$invoice->rev." ".$item->itemdesc;
        $html .= '
            <tr>
                <td>'.$item->item.'</td>
                <td>'.$desc.'</td>
                <td>'.$item->qty2.'</td>
                <td>$'.number_format($item->uprice, 2).'</td>
                <td>$'.number_format($item->tprice, 2).'</td>
            </tr>';
    }

    $html .= '
        </table>

        <!-- TOTALS -->
        <table class="totals-table">
            <tr>
                <td style="width:70%;"></td>
                <td style="width:30%;">
                    <div>SUB TOTAL: $'.number_format($subtotal, 2).'</div>
                    <div>TAX: $'.number_format($tax, 2).'</div>
                    <div>FREIGHT: $'.number_format($freight, 2).'</div>
                    <div class="bold">TOTAL: $'.number_format($total, 2).'</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <table class="footer-table">
            <tr>
                <td>
                    <div class="bold">Comments</div>
                    <div>'.($invoice->comments ?? '').'</div>
                    <div>Direct All Inquiries To:</div>
                    <div>Armando Torres</div>
                    <div>714-553-7047</div>
                    <div>armando@pcbsglobal.com</div>
                </td>
                <td>
                    <div class="bold">MAKE ALL CHECKS PAYABLE TO:</div>
                    <div>Torres Developments</div>
                    <div>2500 E. La Palma Ave.</div>
                    <div>Anaheim CA 92806</div>
                </td>
            </tr>
        </table>

        <div style="text-align:center; margin-top:20px; font-weight:bold; font-size:12pt;">
            THANK YOU FOR YOUR BUSINESS AND TRUST!
        </div>
    </body>
    </html>';

    // Generate filename
    $filename = 'Invoice-'.$invoice->invoice_id.'-'.date('m-d-Y').'.doc';

    // Return as downloadable Word document
    return Response::make($html, 200, [
        'Content-Type' => 'application/msword',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"'
    ]);
}
    // for packing slip pdf and docs ..
    public function viewpackingpdf($id){
        $packing = packing_tb::with(['items'])->findOrFail($id);
        $customer = data_tb::find($packing->customer);
        $vendor = data_tb::find($packing->vid);
        
        $shipperId = substr($packing->sid, 1);
        $shipper = $packing->sid[0] == 'c' 
                    ? data_tb::find($shipperId)
                    : shipper_tb::find($shipperId);
        
        $contacts = maincont_tb::join('maincont_packing', 'maincont_tb.enggcont_id', '=', 'maincont_packing.maincontid')
                    ->where('maincont_packing.packingid', $id)
                    ->get();

        $invoiceNo = $packing->invoice_id + 9987;
        // Handle short name (fallback if null)
        $shortName = $customer->c_shortname ?? 'Customer';

        // Build the filename
        $filename = "PS-$invoiceNo-$shortName-{$packing->part_no}-{$packing->rev}-" . date('m-d-Y') . ".pdf";

        return Pdf::loadView('pdf.packing-slip', [
            'title'     => 'Packing Slip',
            'packing'   => $packing,
            'customer'  => $customer,
            'vendor'    => $vendor,
            'shipper'   => $shipper,
            'contacts'  => $contacts,
            'invoiceNo' => $invoiceNo,
        ])->stream($filename);

    }
    public function downloadpackingpdf($id){
        $packing = packing_tb::with(['items'])->findOrFail($id);
        $customer = data_tb::find($packing->customer);
        $vendor = data_tb::find($packing->vid);
        
        $shipperId = substr($packing->sid, 1);
        $shipper = $packing->sid[0] == 'c' 
                    ? data_tb::find($shipperId)
                    : shipper_tb::find($shipperId);
        
        $contacts = maincont_tb::join('maincont_packing', 'maincont_tb.enggcont_id', '=', 'maincont_packing.maincontid')
                    ->where('maincont_packing.packingid', $id)
                    ->get();

        $invoiceNo = $packing->invoice_id + 9987;
        // Handle short name (fallback if null)
        $shortName = $customer->c_shortname ?? 'Customer';

        // Build the filename
        $filename = "PS-$invoiceNo-$shortName-{$packing->part_no}-{$packing->rev}-" . date('m-d-Y') . ".pdf";

        return Pdf::loadView('pdf.packing-slip', [
            'title'     => 'Packing Slip',
            'packing'   => $packing,
            'customer'  => $customer,
            'vendor'    => $vendor,
            'shipper'   => $shipper,
            'contacts'  => $contacts,
            'invoiceNo' => $invoiceNo,
        ])->download($filename);

    }
public function downloadPackingDoc($id)
{
    $packing = packing_tb::with('items')->findOrFail($id);
    $customer = data_tb::find($packing->customer);
    $vendor = data_tb::find($packing->vid);

    $shipperId = substr($packing->sid, 1);
    $shipper = $packing->sid[0] == 'c'
        ? data_tb::find($shipperId)
        : shipper_tb::find($shipperId);

    $contacts = maincont_tb::join('maincont_packing', 'maincont_tb.enggcont_id', '=', 'maincont_packing.maincontid')
        ->where('maincont_packing.packingid', $id)
        ->get();

    $invoiceNo = $packing->invoice_id + 9987;
    $shortname = $customer?->c_shortname ?? 'Unknown';
    $today = date('m-d-Y');

    // Custom function to parse the non-standard date format
    $parseCustomDate = function($dateString) {
        if (empty($dateString)) {
            return $dateString;
        }
        
        // Handle "Wednesday-10-10-2018" format
        if (preg_match('/^[A-Za-z]+-\d{1,2}-\d{1,2}-\d{4}$/', $dateString)) {
            $parts = explode('-', $dateString);
            $month = $parts[1];
            $day = $parts[2];
            $year = $parts[3];
            
            try {
                return Carbon::createFromDate($year, $month, $day)->format('l, m/d/Y');
            } catch (\Exception $e) {
                return $dateString;
            }
        }
        
        // Try standard parsing as fallback
        try {
            return Carbon::parse($dateString)->format('m/d/Y');
        } catch (\Exception $e) {
            return $dateString;
        }
    };

    $orderedDate = $parseCustomDate($packing->odate);
    $packingDate = $packing->podate ? $parseCustomDate($packing->podate) : '';

    // Calculate totals
    $qtot = $totq = 0;
    foreach ($packing->items as $item) {
        $qtot += (int) $item->qty2;
        $totq += (int) $item->shipqty;
    }

    // Generate HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Packing Slip</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 10pt;
                line-height: 1.3;
                margin: 0;
                padding: 0;
            }
            .header-table { width: 100%; border-collapse: collapse; }
            .header-table td { vertical-align: top; padding: 4px; }
            .logo { width: 40px; height: 30px; }
            .doc-title { font-size: 26pt; font-weight: bold; color: #5660B1; margin-bottom: 5px; }
            .company-info { font-size: 10pt; }
            .section-bar { background: #656BBC; color: #fff; font-weight: bold; padding: 6px; margin: 10px 0; }
            .info-table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
            .info-table th { background: #656BBC; color: #fff; }
            .info-table th, .info-table td { border: 1px solid #000; padding: 4px; }
            .items-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
            .items-table th { background: #656BBC; color: #fff; }
            .items-table th, .items-table td { border: 1px solid #000; padding: 4px; }
            .totals-table { width: 100%; margin-top: 10px; }
            .bold { font-weight: bold; }
            .text-right { text-align: right; }
            .footer { margin-top: 20px; font-size: 9pt; text-align: center; }
        </style>
    </head>
    <body>
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td>
                    <img src="'.public_path('images/logo.png').'" class="logo">
                </td>
                <td>
                    <div class="doc-title">Packing Slip</div>
                    <div class="company-info">
                        Ordered Date: '.$orderedDate.'<br>
                        Date: '.$packingDate.'<br>
                        Our Order No: '.$packing->our_ord_num.'<br>
                        Packing Slip No: '.$invoiceNo.'<br>
                        Purchase Order No: '.$packing->po.'<br>
                        Acct No: '.($customer->e_other ?? '').'<br>
                        Cust ID: '.($customer->e_cid ?? '').'<br>
                        Shipped Via: '.($packing->svia == 'Other' ? ($packing->svia_oth ?: 'Other') : $packing->svia).'<br>
                        <span class="bold">Customer Contacts:</span><br>';

    foreach ($contacts as $c) {
        $html .= '        '.$c->name.' '.$c->lastname.' '.$c->phone.'<br>';
    }

    $html .= '
                    </div>
                </td>
            </tr>
        </table>

        <!-- PART NUMBER/REV -->
        <div class="section-bar">PART INFORMATION</div>
        <table class="info-table">
            <tr>
                <th>PART NUMBER</th>
                <th>REV</th>
            </tr>
            <tr>
                <td>'.$packing->part_no.'</td>
                <td>'.$packing->rev.'</td>
            </tr>
        </table>

        <!-- BILL TO / SHIP TO -->
        <table style="width:100%; margin-top:10px;">
            <tr>
                <td style="width:50%; vertical-align:top;">
                    <div class="section-bar">BILL TO</div>
                    <div>'.($vendor->c_name ?? '').'</div>
                    <div>(Accounts Payable)</div>
                    <div>'.($vendor->c_address ?? '').'</div>
                    <div>'.($vendor->c_address2 ?? '').'</div>
                    <div>'.($vendor->c_address3 ?? '').'</div>
                    <div>Phone: '.($vendor->c_phone ?? '').'</div>
                    <div>Fax: '.($vendor->c_fax ?? '').'</div>
                    <div>'.($vendor->c_website ?? '').'</div>
                </td>
                <td style="width:50%; vertical-align:top;">
                    <div class="section-bar">SHIP TO</div>
                    <div>'.($shipper->c_name ?? '').'</div>
                    <div>'.($shipper->c_address ?? '').'</div>
                    <div>'.($shipper->c_address2 ?? '').'</div>
                    <div>'.($shipper->c_address3 ?? '').'</div>
                    <div>Phone: '.($shipper->c_phone ?? '').'</div>
                    <div>Fax: '.($shipper->c_fax ?? '').'</div>
                    '.($packing->delto ? '<div>Delivered To: '.$packing->delto.'</div>' : '').'
                    '.($packing->date1 ? '<div>Delivered On: '.$parseCustomDate($packing->date1).'</div>' : '').'
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <div class="section-bar">ITEMS</div>
        <table class="items-table">
            <tr>
                <th>ITEM #</th>
                <th>PART NUMBER</th>
                <th>REV</th>
                <th>LYRS</th>
                <th>DESCRIPTION</th>
                <th>QTY ORDERED</th>
                <th>QTY DELIVERED</th>
            </tr>';

    foreach ($packing->items as $index => $item) {
        $html .= '
            <tr>
                <td>'.$item->item.'</td>
                <td>'.($index === 0 ? $packing->part_no : '').'</td>
                <td>'.($index === 0 ? $packing->rev : '').'</td>
                <td>'.($index === 0 ? explode('Lyrs', $packing->no_layer)[0] : '').'</td>
                <td>'.$item->itemdesc.'</td>
                <td>'.$item->qty2.'</td>
                <td>'.$item->shipqty.'</td>
            </tr>';
    }

    $html .= '
        </table>

        <!-- TOTALS -->
        <table class="totals-table">
            <tr>
                <td style="width:60%;"></td>
                <td style="width:40%;">
                    <div class="bold">Total Ordered: '.$qtot.'</div>
                    <div class="bold">Total Delivered: '.$totq.'</div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            <div>If you have any issues with your order, please contact:</div>
            <div>Armando Torres</div>
            <div>714-553-7047</div>
            <div>armando@pcbsglobal.com</div>
            <div style="margin-top:20px; font-weight:bold;">THANK YOU FOR YOUR BUSINESS AND TRUST!</div>
        </div>
    </body>
    </html>';

    // Generate filename
    $filename = "PS-".$invoiceNo."-".$shortname."-".$packing->part_no."-".$packing->rev."-".$today.".doc";

    // Return as downloadable Word document
    return Response::make($html, 200, [
        'Content-Type' => 'application/msword',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"'
    ]);
}
    // order confirmation view pdf ...
    // for view pdf  ..
    public function vieworderconfirmationpdf($id)
    {
            // Load main confirmation order
        $corder = corder_tb::with(['items'])->findOrFail($id);

        // Vendor (customer placing order)
        $vendor = data_tb::find($corder->vid);

        // Customer (ordering party)
        $customer = data_tb::find($corder->customer);

        // Determine shipper (from sid with prefix 'c' or 's')
        $shipperId = substr($corder->sid, 1);
        $shipper = $corder->sid[0] === 'c'
            ? data_tb::find($shipperId)
            : shipper_tb::find($shipperId);

        // Contacts (if any linked via maincont_corder)
        // $contacts = maincont_tb::join('maincont_corder', 'maincont_tb.enggcont_id', '=', 'maincont_corder.maincontid')
        //     ->where('maincont_corder.corderid', $id)
        //     ->get();

        // Deliveries (optional)
        $deliveries = mdlitems_tb::where('pid', $id)->get();

        // Generate unique confirmation number
        $invoiceNo = $corder->poid + 9992;

        // Customer shortname fallback
        $shortName = $vendor->c_shortname ?? 'Customer';

        // File name format: OC-<number>-<customer>-<part>-<rev>-<date>.pdf
        $filename = "OC-$invoiceNo-$shortName-{$corder->part_no}-{$corder->rev}-" . date('m-d-Y') . ".pdf";

        return Pdf::loadView('pdf.confirmation-order', [
            'title'     => 'Confirmation Order',
            'corder'    => $corder,
            'vendor'    => $vendor,
            'customer'  => $customer,
            'shipper'   => $shipper,
           // 'contacts'  => $contacts,
            'deliveries'=> $deliveries,
            'invoiceNo' => $invoiceNo,
        ])->stream($filename);
    }
    // for download pdf ..
    public function downloadorderconfirmationpdf($id){
                    // Load main confirmation order
                    $corder = corder_tb::with(['items'])->findOrFail($id);

                    // Vendor (customer placing order)
                    $vendor = data_tb::find($corder->vid);
            
                    // Customer (ordering party)
                    $customer = data_tb::find($corder->customer);
            
                    // Determine shipper (from sid with prefix 'c' or 's')
                    $shipperId = substr($corder->sid, 1);
                    $shipper = $corder->sid[0] === 'c'
                        ? data_tb::find($shipperId)
                        : shipper_tb::find($shipperId);
            
                    // Contacts (if any linked via maincont_corder)
                    // $contacts = maincont_tb::join('maincont_corder', 'maincont_tb.enggcont_id', '=', 'maincont_corder.maincontid')
                    //     ->where('maincont_corder.corderid', $id)
                    //     ->get();
            
                    // Deliveries (optional)
                    $deliveries = mdlitems_tb::where('pid', $id)->get();
            
                    // Generate unique confirmation number
                    $invoiceNo = $corder->poid + 9992;
            
                    // Customer shortname fallback
                    $shortName = $vendor->c_shortname ?? 'Customer';
            
                    // File name format: OC-<number>-<customer>-<part>-<rev>-<date>.pdf
                    $filename = "OC-$invoiceNo-$shortName-{$corder->part_no}-{$corder->rev}-" . date('m-d-Y') . ".pdf";
            
                    return Pdf::loadView('pdf.confirmation-order', [
                        'title'     => 'Confirmation Order',
                        'corder'    => $corder,
                        'vendor'    => $vendor,
                        'customer'  => $customer,
                        'shipper'   => $shipper,
                       // 'contacts'  => $contacts,
                        'deliveries'=> $deliveries,
                        'invoiceNo' => $invoiceNo,
                    ])->download($filename);
    }
    // for download docs file ..

   public function downloadorderconfirmationdoc($id)
{
    $corder = corder_tb::with(['items', 'deliveries'])->findOrFail($id);
    $vendor = data_tb::find($corder->vid);

    $shipperId = substr($corder->sid, 1);
    $shipper = $corder->sid[0] === 'c'
        ? data_tb::find($shipperId)
        : shipper_tb::find($shipperId);

    $invoiceNo = $corder->poid + 9992;
    $shortname = $vendor->c_shortname ?? 'Customer';
    $today = date('m-d-Y');

    // Calculate totals
    $subtotal = 0;
    foreach ($corder->items as $item) {
        $subtotal += $item->qty2 * $item->uprice;
    }
    $st = floatval($corder->stax);
    $tax = $subtotal * $st;
    $total = $subtotal + $tax;

    // Generate HTML content
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Order Confirmation</title>
        <style>
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 10pt;
                line-height: 1.3;
                margin: 0;
                padding: 0;
            }
            .header-table { width: 100%; border-collapse: collapse; }
            .header-table td { vertical-align: top; padding: 4px; }
            .logo { width: 40px; height: 30px; }
            .doc-title { font-size: 26pt; font-weight: bold; color: #5660B1; margin-bottom: 5px; }
            .company-info { font-size: 10pt; }
            .section-bar { background: #656BBC; color: #fff; font-weight: bold; padding: 6px; margin: 10px 0; }
            .info-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
            .info-table th, .info-table td { border: 1px solid #000; padding: 4px; }
            .items-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
            .items-table th { background: #656BBC; color: #fff; }
            .items-table th, .items-table td { border: 1px solid #000; padding: 4px; }
            .delivery-table { width: 100%; border-collapse: collapse; border: 2px solid #000; }
            .delivery-table th { background: #656BBC; color: #fff; }
            .delivery-table th, .delivery-table td { border: 1px solid #000; padding: 4px; }
            .totals-table { width: 100%; margin-top: 10px; }
            .bold { font-weight: bold; }
            .text-right { text-align: right; }
            .footer { margin-top: 20px; font-size: 9pt; text-align: center; }
        </style>
    </head>
    <body>
        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td>
                    <img src="'.public_path('images/logo.png').'" class="logo">
                </td>
                <td>
                    <div class="doc-title">Order Confirmation</div>
                    <div class="company-info">
                        Date: '.$corder->podate.'<br>
                        SO #: '.$corder->our_ord_num.'<br>
                        Conf #: '.$invoiceNo.'
                    </div>
                </td>
            </tr>
        </table>

        <!-- COMPANY INFO -->
        <div style="margin-top:10px;">
            <div class="bold">PCBs Global Incorporated</div>
            <div class="company-info">
                2500 E. La Palma Ave.<br>
                Anaheim Ca. 92806<br>
                Phone: (855) 722-7456<br>
                Fax: (855) 262-5305
            </div>
        </div>

        <!-- BILL TO / SHIP TO -->
        <table style="width:100%; margin-top:10px;">
            <tr>
                <td style="width:50%; vertical-align:top;">
                    <div class="section-bar">BILL TO</div>
                    <div>'.($vendor->c_name ?? '').'</div>
                    <div>(Accounts Payable)</div>
                    <div>'.($vendor->c_address ?? '').'</div>
                    <div>'.($vendor->c_address2 ?? '').'</div>
                    <div>'.($vendor->c_address3 ?? '').'</div>
                    <div>Phone: '.($vendor->c_phone ?? '').'</div>
                    <div>Fax: '.($vendor->c_fax ?? '').'</div>
                    <div>'.($vendor->c_website ?? '').'</div>
                </td>
                <td style="width:50%; vertical-align:top;">
                    <div class="section-bar">SHIP TO</div>
                    <div>'.($shipper->c_name ?? '').'</div>
                    <div>'.($shipper->c_address ?? '').'</div>
                    <div>'.($shipper->c_address2 ?? '').'</div>
                    <div>'.($shipper->c_address3 ?? '').'</div>
                    <div>Phone: '.($shipper->c_phone ?? '').'</div>
                    <div>Fax: '.($shipper->c_fax ?? '').'</div>
                    '.($corder->delto ? '<div>Delivered To: '.$corder->delto.'</div>' : '').'
                </td>
            </tr>
        </table>

        <!-- ORDER INFO -->
        <div class="section-bar" style="margin-top:10px;">ORDER INFORMATION</div>
        <table class="info-table">
            <tr>
                <th>CUSTOMER PO</th>
                <th>SHIP VIA</th>
                <th>F.O.B.</th>
                <th>TERMS</th>
                <th>CONTACT</th>
                <th>DELIVER TO</th>
            </tr>
            <tr>
                <td>'.$corder->po.'</td>
                <td>'.($corder->svia === 'Other' ? $corder->svia_oth : $corder->svia).'</td>
                <td>'.$corder->city.', '.$corder->state.'</td>
                <td>'.($vendor->e_payment ?? '').'</td>
                <td>'.$corder->namereq.'</td>
                <td>'.$corder->delto.'</td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <div class="section-bar" style="margin-top:10px;">ITEMS</div>
        <table class="items-table">
            <tr>
                <th>ITEM #</th>
                <th>DESCRIPTION</th>
                <th>TOTAL QTY</th>
                <th>UNIT PRICE</th>
                <th>TOTAL</th>
            </tr>';

    foreach ($corder->items as $i => $item) {
        $lineTotal = $item->qty2 * $item->uprice;
        $desc = ($i == 0 ? $corder->part_no.' Rev '.$corder->rev.' ' : '').$item->itemdesc;
        $html .= '
            <tr>
                <td>'.$item->item.'</td>
                <td>'.$desc.'</td>
                <td>'.$item->qty2.'</td>
                <td>$'.number_format($item->uprice, 2).'</td>
                <td>$'.number_format($lineTotal, 2).'</td>
            </tr>';
    }

    $html .= '
        </table>

        <!-- DELIVERIES -->
        <div class="section-bar" style="margin-top:10px;">SCHEDULED DELIVERIES</div>
        <table class="delivery-table">
            <tr>
                <th>Scheduled Qty</th>
                <th>Dock Date</th>
            </tr>';

    foreach ($corder->deliveries as $delivery) {
        $html .= '
            <tr>
                <td>'.$delivery->qty.'</td>
                <td>'.$delivery->date.'</td>
            </tr>';
    }

    $html .= '
        </table>

        <!-- TOTALS -->
        <table class="totals-table">
            <tr>
                <td style="width:60%;"></td>
                <td style="width:40%;">
                    <div class="text-right bold">Sub Total: $'.number_format($subtotal, 2).'</div>
                    <div class="text-right bold">Sale Tax: $'.number_format($tax, 2).'</div>
                    <div class="text-right bold">Total: $'.number_format($total, 2).'</div>
                </td>
            </tr>
        </table>';

    // COMMENTS
    if ($corder->comments) {
        $html .= '
        <div style="margin-top:10px;">
            <div class="bold">Comments:</div>
            <div>'.nl2br(e($corder->comments)).'</div>
        </div>';
    }

    // FOOTER
    $html .= '
        <div class="footer">
            <div>If any errors are found in this Order Confirmation, please contact:</div>
            <div>Armando Torres</div>
            <div>(855) 722-7456 x 102 or (714) 553-7047</div>
            <div style="margin-top:20px; font-weight:bold;">THANK YOU FOR YOUR BUSINESS AND TRUST!</div>
        </div>
    </body>
    </html>';

    // Generate filename
    $filename = "OC-".$invoiceNo."-".$corder->customer."-".$corder->part_no."-".$corder->rev."-".$shortname."-".$today.".doc";

    // Return as downloadable Word document
    return Response::make($html, 200, [
        'Content-Type' => 'application/msword',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"'
    ]);
}
    // view pdf purchase order ..
    public function viewpurchaseorder($id)
{
    $porder = porder_tb::findOrFail($id);
    $items = items_tb::where('pid', $porder->poid)->orderBy('item')->get();

    $vendor = vendor_tb::find($porder->vid);

    $shipper = (str_starts_with($porder->sid, 'c'))
        ? data_tb::find(substr($porder->sid, 1))
        : shipper_tb::find($porder->sid);

    $order = order_tb::where('cust_name', $porder->customer)
        ->where('part_no', $porder->part_no)
        ->first();

    $poNote = notes_tb::where('ntype', 'po')->first();

    $specialInstructions = $order?->special_instadmin;
    $poNumber = $porder->poid + 9933;

    $itemDescriptions = [
        'pcbp' => 'PCB Fabrication (Production)',
        'pcbeo' => 'PCB Fabrication (Expedited Order)',
        'nre' => 'NRE',
        'exf' => 'Expedite fee',
        'suc' => 'Set-up charge',
        'frt' => 'Freight',
        'etst' => 'E-Test',
        'fpb' => 'Flying Probe',
        'etstf' => 'E-Test Fixture',
        'sf' => 'Surface Finish',
        'oth' => 'Other',
    ];
    $path = public_path('images/logo.png'); // Make sure the image is actually here
    $type = pathinfo($path, PATHINFO_EXTENSION); // png
    $data = file_get_contents($path);
    $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
    
    $pdf = Pdf::loadView('pdf.purchase-order', compact(
        'porder', 'items', 'vendor', 'shipper', 'order', 'specialInstructions','base64Logo',
        'itemDescriptions', 'poNumber', 'poNote'
    ))->setPaper('a4', 'portrait')->setOptions([
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'DejaVu Sans',
    ]);

    return $pdf->stream("PO-{$poNumber}-{$porder->customer}.pdf");
}
    // download pdf purchase order ..
    public function downloaddocpurchaseorder($id) {
    $porder = porder_tb::findOrFail($id);
    $items = items_tb::where('pid', $porder->poid)->orderBy('item')->get();
    $vendor = vendor_tb::find($porder->vid);
    $shipper = str_starts_with($porder->sid, 'c') 
        ? data_tb::find(substr($porder->sid, 1)) 
        : shipper_tb::find($porder->sid);
    $order = order_tb::where('cust_name', $porder->customer)
        ->where('part_no', $porder->part_no)
        ->first();
    $poNote = notes_tb::where('ntype', 'po')->first();
    $poNumber = $porder->poid + 9933;

    $itemDescriptions = [
        'pcbp' => 'PCB Fabrication (Production)',
        'pcbeo' => 'PCB Fabrication (Expedited Order)',
        'nre' => 'NRE',
        'exf' => 'Expedite fee',
        'suc' => 'Set-up charge',
        'frt' => 'Freight',
        'etst' => 'E-Test',
        'fpb' => 'Flying Probe',
        'etstf' => 'E-Test Fixture',
        'sf' => 'Surface Finish',
        'oth' => 'Other',
    ];

    // Calculate totals
    $total = 0;
    foreach ($items as $item) {
        $total += $item->qty2 * $item->uprice;
    }

    // Generate HTML content matching PDF structure
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Purchase Order</title>
        <style>
            @page {
                margin: 10px 15px;
            }
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 8pt;
                margin: 0;
                padding: 0;
            }
            table {
                page-break-inside: avoid;
            }
            .title {
                font-size: 15pt;
                color: #5660B1;
            }
            .section-title {
                background: #656BBC;
                color: #FFF;
                padding: 2px;
                font-weight: bold;
            }
            .table, .table td, .table th {
                border-collapse: collapse;
                border: 1px solid #999;
                padding: 2px;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .currency {
                text-align: right;
                padding-right: 5px;
            }
        </style>
    </head>
    <body>
        <!-- Header matching PDF structure -->
        <table width="100%">
            <tr>
                <td><img src="'.public_path('images/logo.png').'" width="150" height="100px" /></td>
                <td></td>
                <td class="text-right">
                    <h1 class="title">Purchase Order</h1>
                    Date: <strong>'.$porder->podate.'</strong><br>
                    PO #: <strong>'.$poNumber.'</strong>
                </td>
            </tr>
        </table>

        <!-- Company Info -->
        <table width="100%">
            <tr>
                <td><strong>PCBs Global Incorporated</strong><br>
                    2500 E. La Palma Ave.<br>
                    Anaheim Ca. 92806<br>
                    Phone: (855) 722-7456<br>
                    Fax: (855) 262-5305
                </td>
            </tr>
        </table>

        <!-- Vendor/Ship To -->
        <table width="100%">
            <tr>
                <td class="section-title" colspan="2" width="45%">VENDOR</td>
                <td width="10%"></td>
                <td class="section-title" colspan="2" width="45%">SHIP TO</td>
            </tr>
            <tr>
                <td colspan="2">
                    '.($vendor->c_name ?? '').'<br>
                    '.($vendor->c_address ?? '').'<br>
                    '.$vendor->c_address2.' '.$vendor->c_address3.'<br>
                    Phone: '.($vendor->c_phone ?? '').'<br>
                    Fax: '.($vendor->c_fax ?? '').'<br>
                    '.($vendor->c_website ?? '').'
                </td>
                <td></td>
                <td colspan="2">
                    '.($shipper->c_name ?? '').'<br>
                    '.($shipper->c_address ?? '').'<br>
                    '.($shipper->c_address2 ?? '').' '.($shipper->c_address3 ?? '').'<br>
                    Phone: '.($shipper->c_phone ?? '').'<br>
                    Fax: '.($shipper->c_fax ?? '').'<br>
                    '.($shipper->c_website ?? '').'
                </td>
            </tr>
        </table>

        <!-- Shipment Info -->
        <table width="100%">
            <tr class="section-title text-center">
                <td>REQUISITIONER</td>
                <td>SHIP VIA</td>
                <td>F.O.B.</td>
                <td>SHIPPING TERMS</td>
            </tr>
            <tr class="text-center">
                <td>'.$porder->namereq.'</td>
                <td>'.($porder->svia === 'Other' ? $porder->svia_oth : $porder->svia).'</td>
                <td>'.$porder->city.', '.$porder->state.'</td>
                <td>'.$porder->sterms.'</td>
            </tr>
        </table>

        <!-- Items Table -->
        <table width="100%" class="table">
            <thead class="text-center section-title">
                <tr>
                    <th>ITEM #</th>
                    <th>PART NUMBER</th>
                    <th>REV</th>
                    <th>LYRS</th>
                    <th>DESCRIPTION</th>
                    <th>QTY</th>
                    <th>UNIT PRICE</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($items as $i => $item) {
        $lineTotal = $item->qty2 * $item->uprice;
        $html .= '
                <tr class="text-center">
                    <td>'.$item->item.'</td>
                    <td>'.($i == 0 ? $porder->part_no : '').'</td>
                    <td>'.($i == 0 ? $porder->rev : '').'</td>
                    <td>'.explode('Lyrs', $porder->no_layer)[0].'</td>
                    <td>'.($itemDescriptions[$item->dpval] ?? $item->desc).'</td>
                    <td>'.$item->qty2.'</td>
                    <td class="currency">$'.number_format($item->uprice, 2).'</td>
                    <td class="currency">$'.number_format($lineTotal, 2).'</td>
                </tr>';
    }

    $html .= '
            </tbody>
        </table>

        <!-- Total -->
        <table width="100%">
            <tr>
                <td width="70%"></td>
                <td>
                    <strong>Total:</strong> $'.number_format($total, 2).'
                </td>
            </tr>
        </table>

        <!-- Footer Notes -->
        <table width="100%">
            <tr>
                <td style="font-size: 10pt">
                    '.($porder->iscancel === 'no' ? '
                    Customer: '.$porder->customer.'<br>
                    PO #: '.$porder->po.'<br>
                    Boards to dock at destination '.$porder->date1.'<br>
                    '.($porder->rohs === 'yes' ? 'RoHS Certs required<br>' : '').'
                    '.($poNote && $poNote->ntext ? nl2br(e($poNote->ntext)).'<br>' : '').'
                    ' : '').'

                    '.($porder->sp_reqs ? '
                    <strong>Special Requirements:</strong><br>
                    <div style="width: 750px; font-size: 9pt; padding-bottom: 0px">
                        '.implode('<br>', array_map(function($req, $index) {
                            return ($index + 1).'.) '.$req;
                        }, explode('|', $porder->sp_reqs), array_keys(explode('|', $porder->sp_reqs)))).'
                    </div>
                    ' : '').'

                    '.($porder->comments ? '
                    <div style="padding-bottom:5px;"><strong>Additional Requirements</strong></div>
                    '.nl2br(e($porder->comments)).'<br>
                    ' : '').'

                    '.($porder->iscancel === 'no' ? '
                    <p>
                        Invoice: armando@pcbsglobal.com and silvia@pcbsglobal.com<br>
                        Email working data to: armando@pcbsglobal.com and isaac@pcbsglobal.com<br>
                        Please refer any questions to: armando@pcbsglobal.com and isaac@pcbsglobal.com<br>
                    </p>
                    ' : '
                    Please refer any questions to: armando@pcbsglobal.com and isaac@pcbsglobal.com<br>
                    ').'
                </td>
            </tr>
        </table>

        <p class="text-center" style="font-size: 14pt;"><strong>THANK YOU FOR YOUR BUSINESS AND TRUST!</strong></p>
        <span style="position:absolute;bottom:0px;font-size:8px;">FM8.4.1</span>
    </body>
    </html>';

    // Generate filename
    $filename = "PO-".$poNumber."-".preg_replace('/[^A-Za-z0-9\-]/', '_', $porder->customer).".doc";

    // Return as downloadable Word document
    return Response::make($html, 200, [
        'Content-Type' => 'application/msword',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"'
    ]);
}
// for quote section pdf ..
public function viewPdfqoute($id)
{
    $quote = Order::findOrFail($id);
    
    // Prepare price matrix data for ALL options (10 quantities × 5 days)
    $prices = [];
    $dayOptions = [];
    
    // Collect available day options
    for ($d = 1; $d <= 5; $d++) {
        if (!empty($quote->{'day'.$d}) && $quote->{'day'.$d} > 0) {
            $dayOptions[] = [
                'day' => $d,
                'value' => $quote->{'day'.$d}
            ];
        }
    }
    
    // Collect available quantity options with their prices
    for ($q = 1; $q <= 10; $q++) {
        if (!empty($quote->{'qty'.$q}) && $quote->{'qty'.$q} > 0) {
            $priceRow = ['qty' => $quote->{'qty'.$q}];
            
            foreach ($dayOptions as $dayOpt) {
                $dayNum = $dayOpt['day'];
                $priceValue = $quote->{'pr'.$q.$dayNum} ?? 0;
                
                // Ensure the price is numeric (handle string values with commas)
                if (is_string($priceValue)) {
                    $priceValue = (float) str_replace(',', '', $priceValue);
                }
                
                $priceRow['day'.$dayNum] = $priceValue;
            }
            
            $prices[] = $priceRow;
        }
    }
    
    // Calculate totals with misc charges - FIXED VARIABLE NAMES
    $misccharge = $quote->necharge ?? 0;
    $nre = $quote->descharge ?? 0;
    $descharge1 = $quote->descharge1 ?? 0;
    $descharge2 = $quote->descharge2 ?? 0;
    
    // Convert all to numbers to avoid string concatenation issues
    $misccharge = (float) $misccharge;
    $nre = (float) $nre;
    $descharge1 = (float) $descharge1;
    $descharge2 = (float) $descharge2;
    
    $totalMisc = $misccharge + $nre + $descharge1 + $descharge2;
    
    $filename = "Quotation-".$quote->ord_id."-".str_replace(' ', '-', $quote->cust_name)."-".$quote->part_no."-".$quote->rev."_".date("m-d-Y").".pdf";
    
    $pdf = Pdf::loadView('pdf.qoute', [
        'quote' => $quote,
        'prices' => $prices,
        'dayOptions' => $dayOptions,
        'totalMisc' => $totalMisc,
        'title' => $filename
    ]);
    
    return $pdf->stream($filename);
}
    public function downloadPdfqoute($id)
    {
        $quote = Order::findOrFail($id);
        
        // Prepare price matrix data
        $prices = [];
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($quote->{'qty'.$i})) {
                $prices[] = [
                    'qty' => $quote->{'qty'.$i},
                    'day1' => $quote->{'pr'.$i.'1'} ?? 0,
                    'day2' => $quote->{'pr'.$i.'2'} ?? 0,
                    'day3' => $quote->{'pr'.$i.'3'} ?? 0,
                ];
            }
        }
        
        $filename = "Quotation-".$quote->ord_id."-".str_replace(' ', '-', $quote->cust_name)."-".$quote->part_no."-".$quote->rev."_".date("m-d-Y").".pdf";
        
        $pdf = Pdf::loadView('pdf.qoute', [
            'quote' => $quote,
            'prices' => $prices,
            'title' => $filename
        ]);
        
        return $pdf->download($filename);
    }
public function viewdocqoute($id)
{
    $order = Order::findOrFail($id);
    
    // Prepare price matrix data
    $prices = [];
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($order->{'qty'.$i})) {
            $prices[] = [
                'qty' => $order->{'qty'.$i},
                'day1' => $order->{'pr'.$i.'1'} ?? 0,
                'day2' => $order->{'pr'.$i.'2'} ?? 0,
                'day3' => $order->{'pr'.$i.'3'} ?? 0,
            ];
        }
    }

    // Generate the HTML content using your exact template
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Quotation</title>
        <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
            padding: 4px;
        }

        .logo {
            width: 120px;
        }

        .quote-title {
            font-size: 28pt;
            font-weight: bold;
            color: #3A4FA5;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 9pt;
            font-weight: normal;
        }

        .quote-details {
            text-align: right;
            font-size: 9pt;
            line-height: 1.4;
        }

        .quote-details strong {
            color: #000;
        }

        /* SECTION TITLE */
        .section-bar {
            background: #3A4FA5;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            font-size: 11pt;
            margin: 10px 0 0 0;
        }

        /* ORDER DETAILS */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .info-table td {
            border: 1px solid #000;
            padding: 4px;
        }

        .notes-row td {
            border: 1px solid #000;
            padding: 4px;
        }

        .notes-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .notes-table td {
            vertical-align: top;
            width: 33%;
        }

        /* PRICE TABLE WITH DOUBLE BORDER */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            border: 3px double #000;
        }

        .price-table th,
        .price-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 9pt;
        }

        /* FOOTER */
        .footer {
            font-size: 7.5pt;
            margin-top: 15px;
            line-height: 1.2;
        }
        </style>
    </head>
    <body>

        <!-- HEADER -->
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="'.public_path('images/logo.png').'" style="width:120px;">
                </td>
                <td>
                    <div class="quote-title">Quotation</div>
                    <div class="company-info">
                        <strong>PCBs Global Incorporated.</strong><br>
                        Phone (855) 722-7456<br>
                        Fax: (855) 262-5305<br>
                        sales@pcbsglobal.com<br>
                        Quote Prepared By: '.($order->prepared_by ?? 'Isaac').'
                    </div>
                </td>
                <td class="quote-details">
                    <strong>Quote No :</strong> '.$order->id.'<br>
                    <strong>Quotation Date :</strong> '.Carbon::parse($order->created_at)->format('m/d/Y').'<br>
                    <strong>Quote Valid for :</strong> 30 Days<br><br>
                    <strong>Quote To:</strong><br>'.$order->cust_name.'<br>'.$order->email.'<br />
                </td>
            </tr>
        </table>

        <!-- SECTION BAR -->
        <div class="section-bar">Order Information</div>

        <!-- ORDER DETAILS -->
        <table class="info-table">
            <tr>
                <td><strong>Part Number:</strong> '.$order->part_no.'</td>
                <td><strong>Revision:</strong> '.$order->rev.'</td>
                <td><strong>PCB Type:</strong> '.str_replace('Lyrs', 'Lyr', $order->no_layer).'</td>
                <td><strong>Material:</strong> '.$order->m_require.'</td>
                <td><strong>Thick:</strong> '.$order->thickness.' '.$order->thickness_tole.'</td>
                <td><strong>FOB:</strong> '.$order->fob.'</td>
                <td><strong>IPC Class:</strong> '.$order->ipc_class.'</td>
            </tr>
            <tr>
                <td><strong>Array Info:</strong> '.($order->array ? 'Yes' : 'No').'</td>
                <td colspan="2"><strong>Bd size:</strong> '.$order->board_size1.' X '.$order->board_size2.'</td>
                <td><strong>Imp:</strong>
                    '.($order->con_impe_sing ? 'Single' : '').'
                    '.($order->con_impe_diff ? 'Differential' : '').'
                </td>
                <td colspan="3"><strong>Finish:</strong> '.$order->finish.'</td>
            </tr>
            <tr class="notes-row">
                <td colspan="7">
                    <strong>Special Requirements / Notes:</strong>
                    <table class="notes-table">
                        <tr>
                            <td>
                                <ol>';

    // First column of notes
    if($order->inner_copper) $html .= '<li>'.str_replace('Oz', 'Oz.', $order->inner_copper).' Cu Internal</li>';
    if($order->start_cu) $html .= '<li>'.str_replace('Oz', 'Oz.', $order->start_cu).' Cu External</li>';
    if($order->plated_cu) $html .= '<li>Other Plated Cu in Holes (Min.) '.$order->plated_cu.'</li>';
    if($order->trace_min) $html .= '<li>Trace Min. = '.$order->trace_min.'</li>';
    if($order->space_min) $html .= '<li>Space Min. = '.$order->space_min.'</li>';

    $html .= '
                                </ol>
                            </td>
                            <td>
                                <ol start="6">';

    // Second column of notes
    if($order->design_array) $html .= '<li>Factory to Design Array</li>';
    if($order->array_type2) $html .= '<li>V Score Array Type</li>';
    if($order->array_require1) $html .= '<li>Array Requires Tooling Holes</li>';
    if($order->counter_sink) $html .= '<li>Countersink Required</li>';
    if($order->cut_outs) $html .= '<li>Control Depth Required</li>';

    $html .= '
                                </ol>
                            </td>
                            <td>
                                <ol start="11">';

    // Third column of notes
    if($order->logo === 'Factory') $html .= '<li>Factory Logo</li>';
    if($order->date_code) $html .= '<li>'.$order->date_code.' Date Code Format</li>';
    if($order->array_rail) $html .= '<li>In Array Rail Electrical Test Stamp</li>';
    if($order->xouts) $html .= '<li>X-Out Allowed per Array</li>';
    if($order->rosh_cert) $html .= '<li>RoHS Cert Required</li>';

    $html .= '
                                </ol>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- PRICE TABLE WITH DOUBLE BORDER -->
        <table class="price-table">
            <tr>
                <th></th>
                <th>1 Days</th>
                <th>2 Days</th>
                <th>3 Days</th>
            </tr>';

    foreach ($prices as $index => $price) {
        $html .= '
            <tr>
                <td><strong>Option '.($index+1).'</strong> '.$price['qty'].' Pcs</td>
                <td>$'.number_format($price['day1'], 2).' ea</td>
                <td>$'.number_format($price['day2'], 2).' ea</td>
                <td>$'.number_format($price['day3'], 2).' ea</td>
            </tr>
            <tr>
                <td><strong>Shipping to FOB Included</strong></td>
                <td>$'.number_format($price['day1'] * $price['qty'], 2).'</td>
                <td>$'.number_format($price['day2'] * $price['qty'], 2).'</td>
                <td>$'.number_format($price['day3'] * $price['qty'], 2).'</td>
            </tr>';
    }

    $html .= '
        </table>

        <p>
            When placing your purchase order, please refer to the Quote Number listed at the top of this page.
            Please feel free to call us should any requirements change.<br>
            Thank you for the opportunity to quote your printed circuit board requirements.<br><br>
            Sincerely,<br>
            PCBsGlobal Inc. Sales Team.
        </p>

        <div class="footer">
            Quoted Lead times are based on material availability and shop capacity at time of order placement.
            Quoted Lead Times are based on business days (Monday through Friday) not calendar days. Holiday or
            Plant closures affecting lead-time will be noted during time of quote.<br>
            Quoted Lead times five business days or less are valid for 24 hours from time of issuance of quote.<br>
            Price and delivery are subject to change pending final review of complete data package, including but
            not limited to, artwork, drawings, and applicable specifications. Unless otherwise stated in the RFQ,
            price is based on a 20% X-out allowance on jobs being built in an array form.<br><br>

            Please visit www.pcbsglobal.com/PCBsGlobal_Inc_Terms_of_Sale.pdf for our Terms of Sale<br><br>

            FMB.1.0
        </div>
    </body>
    </html>';

    // Generate filename
    $filename = "Quotation-".$order->id."-".str_replace(' ', '-', $order->cust_name)."-".$order->part_no."-".$order->rev."_".date("m-d-Y").".doc";

    // Return as downloadable Word document
    return Response::make($html, 200, [
        'Content-Type' => 'application/msword',
        'Content-Disposition' => 'attachment; filename="'.$filename.'"'
    ]);
}
function format_num($number, $decimals = 2) {
    if (is_numeric($number)) {
        return number_format((float)$number, $decimals);
    }
    return $number;
}
}