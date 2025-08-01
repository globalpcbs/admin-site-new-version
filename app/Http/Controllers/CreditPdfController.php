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
use App\Models\Order_tb as Order;
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
        $shipper = shipper_tb::find($temp2);
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
        $shipper = shipper_tb::find($temp2);
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
        $shipper = shipper_tb::find($temp2);
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
        $shipper = shipper_tb::find($temp2);
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
    
        $phpWord = new PhpWord();
    
        $section = $phpWord->addSection([
            'marginTop' => 300,
            'marginBottom' => 300,
            'marginLeft' => 300,
            'marginRight' => 300,
        ]);
    
        // Style definitions
        $style = ['name' => 'Arial', 'size' => 10];
        $styleBold = ['name' => 'Arial', 'size' => 10, 'bold' => true];
        $styleBigTitle = ['name' => 'Arial', 'size' => 40, 'bold' => true, 'color' => '5660B1'];
        $paragraphTight = ['spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0, 'lineHeight' => 1.0];
    
        // Header
        $table = $section->addTable(['width' => 100 * 50]);
        $table->addRow();
        $table->addCell(1500)->addImage(public_path('images/logo.png'), ['width' => 40, 'height' => 30]);
        $table->addCell(2500); // Spacer
        $metaCell = $table->addCell(6500);
        $metaCell->addText('Invoice', $styleBigTitle, $paragraphTight);
    
        $meta = [
            'INVOICE NUMBER' => $invoice->invoice_id + 9976,
            'INVOICE DATE' => \Carbon\Carbon::parse($invoice->podate)->format('m/d/Y'),
            'OUR ORDER NO' => $invoice->our_ord_num,
            'YOUR ORDER NO' => $invoice->po,
            'TERMS' => $invoice->sterm,
            'SALES REP' => $invoice->namereq,
            'SHIPPED VIA' => $invoice->svia === 'Other' ? $invoice->svia_oth : $invoice->svia,
            'F.O.B' => 'Anaheim CA',
        ];
    
        foreach ($meta as $label => $value) {
            $metaCell->addText("$label: $value", $style, $paragraphTight);
        }
    
        // Company Info
        $section->addTextBreak(1);
        $infoTable = $section->addTable(['width' => 100 * 50]);
        $infoTable->addRow();
        $left = $infoTable->addCell(7500);
        $left->addText("PCBs Global Incorporated", $styleBold, $paragraphTight);
        $left->addText("2500 E. La Palma Ave.", $style, $paragraphTight);
        $left->addText("Anaheim Ca. 92806", $style, $paragraphTight);
        $left->addText("Phone: (855) 722-7456", $style, $paragraphTight);
        $left->addText("Fax: (855) 262-5305", $style, $paragraphTight);
    
        // SOLD TO / SHIPPED TO
        $section->addTextBreak(1);
        $addrTable = $section->addTable(['width' => 100 * 50]);
        $addrTable->addRow();
        $addrTable->addCell(3000, ['bgColor' => '656BBC'])->addText("SOLD TO", ['color' => 'FFFFFF'] + $styleBold, $paragraphTight);
        $addrTable->addCell(500);
        $addrTable->addCell(3000, ['bgColor' => '656BBC'])->addText("SHIPPED TO", ['color' => 'FFFFFF'] + $styleBold, $paragraphTight);
    
        $addrTable->addRow();
        $custo = $invoice->custo;
        $soldCell = $addrTable->addCell(3000);
        if ($custo) {
            $soldCell->addText($custo->c_name, $style, $paragraphTight);
            $soldCell->addText($custo->c_address, $style, $paragraphTight);
            $soldCell->addText($custo->c_address2, $style, $paragraphTight);
            $soldCell->addText($custo->c_address3, $style, $paragraphTight);
            $soldCell->addText("Phone: {$custo->c_phone}", $style, $paragraphTight);
            $soldCell->addText("Fax: {$custo->c_fax}", $style, $paragraphTight);
            $soldCell->addText($custo->c_website, $style, $paragraphTight);
        }
    
        $addrTable->addCell(500);
        $shipCell = $addrTable->addCell(3000);
        if ($invoice->ord_by) $shipCell->addText("Ordered by: " . $invoice->ord_by, $style, $paragraphTight);
        if ($invoice->delto) $shipCell->addText("Delivered to: " . $invoice->delto, $style, $paragraphTight);
        if ($invoice->date1) $shipCell->addText("Delivered On: " . $invoice->date1, $style, $paragraphTight);
    
        // Item Table - Full Width
        $section->addTextBreak(1);
        $itemsTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'width' => 100 * 50]);
        $itemsTable->addRow();
        $itemsTable->addCell(800, ['bgColor' => '656BBC'])->addText("ITEM #", ['bold' => true, 'color' => 'FFFFFF'], $paragraphTight);
        $itemsTable->addCell(2400, ['bgColor' => '656BBC'])->addText("DESCRIPTION", ['bold' => true, 'color' => 'FFFFFF'], $paragraphTight);
        $itemsTable->addCell(600, ['bgColor' => '656BBC'])->addText("QTY", ['bold' => true, 'color' => 'FFFFFF'], $paragraphTight);
        $itemsTable->addCell(600, ['bgColor' => '656BBC'])->addText("UNIT PRICE", ['bold' => true, 'color' => 'FFFFFF'], $paragraphTight);
        $itemsTable->addCell(600, ['bgColor' => '656BBC'])->addText("TOTAL", ['bold' => true, 'color' => 'FFFFFF'], $paragraphTight);
    
        foreach ($invoice->items as $item) {
            $itemsTable->addRow();
            $itemsTable->addCell(800)->addText($item->item, $style, $paragraphTight);
            $desc = "P/N {$invoice->part_no} Rev {$invoice->rev} {$item->itemdesc}";
            $itemsTable->addCell(2400)->addText($desc, $style, $paragraphTight);
            $itemsTable->addCell(600)->addText($item->qty2, $style, $paragraphTight);
            $itemsTable->addCell(600)->addText('$' . number_format($item->uprice, 2), $style, $paragraphTight);
            $itemsTable->addCell(600)->addText('$' . number_format($item->tprice, 2), $style, $paragraphTight);
        }
    
        // Totals
        $section->addTextBreak(1);
        $totalTable = $section->addTable(['width' => 100 * 50]);
        $totalTable->addRow();
        $totalTable->addCell(7000);
        $totalsCell = $totalTable->addCell(3000);
        $totalsCell->addText("SUB TOTAL: $" . number_format($subtotal, 2), $style, $paragraphTight);
        $totalsCell->addText("TAX: $" . number_format($tax, 2), $style, $paragraphTight);
        $totalsCell->addText("FREIGHT: $" . number_format($freight, 2), $style, $paragraphTight);
        $totalsCell->addText("TOTAL: $" . number_format($total, 2), $styleBold, $paragraphTight);
    
        // Footer Section
        $section->addTextBreak(1);
        $footer = $section->addTable(['width' => 100 * 50]);
        $footer->addRow();
        $contact = $footer->addCell(5000);
        $contact->addText("Comments", $styleBold, $paragraphTight);
        $contact->addText($invoice->comments ?? '', $style, $paragraphTight);
        $contact->addText("Direct All Inquiries To:", $style, $paragraphTight);
        $contact->addText("Armando Torres", $style, $paragraphTight);
        $contact->addText("714-553-7047", $style, $paragraphTight);
        $contact->addText("armando@pcbsglobal.com", $style, $paragraphTight);
    
        $payee = $footer->addCell(5000);
        $payee->addText("MAKE ALL CHECKS PAYABLE TO:", $styleBold, $paragraphTight);
        $payee->addText("Torres Developments", $style, $paragraphTight);
        $payee->addText("2500 E. La Palma Ave.", $style, $paragraphTight);
        $payee->addText("Anaheim CA 92806", $style, $paragraphTight);
    
        $section->addTextBreak(1);
        $section->addText("THANK YOU FOR YOUR BUSINESS AND TRUST!", ['bold' => true, 'size' => 12], ['alignment' => 'center']);
    
        // Save file
        $filename = 'Invoice-' . $invoice->invoice_id . '-' . date('m-d-Y') . '.docx';
        $path = storage_path($filename);
        IOFactory::createWriter($phpWord, 'Word2007')->save($path);
    
        return response()->download($path)->deleteFileAfterSend(true);
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

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Styles
        $style = ['name' => 'Arial', 'size' => 10];
        $bold = ['name' => 'Arial', 'size' => 10, 'bold' => true];
        $header = ['name' => 'Arial', 'size' => 26, 'bold' => true, 'color' => '5660B1'];

        // Header
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addImage(public_path('images/logo.png'), ['width' => 40, 'height' => 30]);
        $meta = $table->addCell(8000);
        $meta->addText("Packing Slip", $header);
        $meta->addText("Ordered Date: " . \Carbon\Carbon::parse($packing->odate)->format('l-m-d-Y'), $style);
        $meta->addText("Date: " . \Carbon\Carbon::parse($packing->podate)->format('m/d/Y'), $style);
        $meta->addText("Our Order No: {$packing->our_ord_num}", $style);
        $meta->addText("Packing Slip No: {$invoiceNo}", $style);
        $meta->addText("Purchase Order No: {$packing->po}", $style);
        $meta->addText("Acct No: {$customer->e_other}", $style);
        $meta->addText("Cust ID: {$customer->e_cid}", $style);
        $meta->addText("Shipped Via: " . ($packing->svia == 'Other' ? ($packing->svia_oth ?: 'Other') : $packing->svia), $style);
        $meta->addText("Customer Contacts: ", $bold);
        foreach ($contacts as $c) {
            $meta->addText("{$c->name} {$c->lastname} {$c->phone}", $style);
        }

        // Part Number/Rev Table
        $section->addTextBreak(1);
        $revTable = $section->addTable();
        $revTable->addRow();
        $revTable->addCell(4000, ['bgColor' => '656BBC'])->addText("PART NUMBER", ['bold' => true, 'color' => 'FFFFFF']);
        $revTable->addCell(2000, ['bgColor' => '656BBC'])->addText("REV", ['bold' => true, 'color' => 'FFFFFF']);
        $revTable->addRow();
        $revTable->addCell(4000)->addText($packing->part_no, $style);
        $revTable->addCell(2000)->addText($packing->rev, $style);

        // Addresses
        $section->addTextBreak(1);
        $addrTable = $section->addTable();
        $addrTable->addRow();
        $addrTable->addCell(4000, ['bgColor' => '656BBC'])->addText("BILL TO", ['bold' => true, 'color' => 'FFFFFF']);
        $addrTable->addCell(4000, ['bgColor' => '656BBC'])->addText("SHIP TO", ['bold' => true, 'color' => 'FFFFFF']);
        $addrTable->addRow();
        $bill = $addrTable->addCell(4000);
        $ship = $addrTable->addCell(4000);
        if ($vendor) {
            $bill->addText($vendor->c_name, $style);
            $bill->addText("(Accounts Payable)", $style);
            $bill->addText($vendor->c_address, $style);
            $bill->addText($vendor->c_address2, $style);
            $bill->addText($vendor->c_address3, $style);
            $bill->addText("Phone: {$vendor->c_phone}", $style);
            $bill->addText("Fax: {$vendor->c_fax}", $style);
            $bill->addText($vendor->c_website, $style);
        }
        if ($shipper) {
            $ship->addText($shipper->c_name, $style);
            $ship->addText($shipper->c_address, $style);
            $ship->addText($shipper->c_address2, $style);
            $ship->addText($shipper->c_address3, $style);
            $ship->addText("Phone: {$shipper->c_phone}", $style);
            $ship->addText("Fax: {$shipper->c_fax}", $style);
        }
        if ($packing->delto) $ship->addText("Delivered To: {$packing->delto}", $style);
        if ($packing->date1) $ship->addText("Delivered On: {$packing->date1}", $style);

        // Item Table
        $section->addTextBreak(1);
        $itemTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $itemTable->addRow();
        $itemTable->addCell(1000)->addText("ITEM #", $bold);
        $itemTable->addCell(2000)->addText("PART NUMBER", $bold);
        $itemTable->addCell(1000)->addText("REV", $bold);
        $itemTable->addCell(1000)->addText("LYRS", $bold);
        $itemTable->addCell(4000)->addText("DESCRIPTION", $bold);
        $itemTable->addCell(1000)->addText("QTY ORDERED", $bold);
        $itemTable->addCell(1000)->addText("QTY DELIVERED", $bold);

        $qtot = $totq = 0;
        foreach ($packing->items as $index => $item) {
            $itemTable->addRow();
            $itemTable->addCell(1000)->addText($item->item, $style);
            $itemTable->addCell(2000)->addText($index === 0 ? $packing->part_no : '', $style);
            $itemTable->addCell(1000)->addText($index === 0 ? $packing->rev : '', $style);
            $itemTable->addCell(1000)->addText($index === 0 ? explode('Lyrs', $packing->no_layer)[0] : '', $style);
            $itemTable->addCell(4000)->addText($item->itemdesc, $style);
            $itemTable->addCell(1000)->addText($item->qty2, $style);
            $itemTable->addCell(1000)->addText($item->shipqty, $style);
            $qtot += (int) $item->qty2;
            $totq += (int) $item->shipqty;
        }

        // Totals
        $section->addTextBreak(1);
        $totalTable = $section->addTable();
        $totalTable->addRow();
        $totalTable->addCell(6000);
        $totals = $totalTable->addCell(4000);
        $totals->addText("Total Ordered: $qtot", $bold);
        $totals->addText("Total Delivered: $totq", $bold);

        // Footer
        $section->addTextBreak(1);
        $section->addText("If you have any issues with your order, please contact:", $style);
        $section->addText("Armando Torres", $style);
        $section->addText("714-553-7047", $style);
        $section->addText("armando@pcbsglobal.com", $style);
        $section->addTextBreak(2);
        $section->addText("THANK YOU FOR YOUR BUSINESS AND TRUST!", ['bold' => true, 'size' => 12], ['alignment' => 'center']);

        // File name & download
        $filename = "PS-$invoiceNo-$shortname-{$packing->part_no}-{$packing->rev}-$today.docx";
        $path = storage_path($filename);

        IOFactory::createWriter($phpWord, 'Word2007')->save($path);
        return response()->download($path)->deleteFileAfterSend(true);
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

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Styles
        $style = ['name' => 'Arial', 'size' => 10];
        $bold = ['name' => 'Arial', 'size' => 10, 'bold' => true];
        $header = ['name' => 'Arial', 'size' => 26, 'bold' => true, 'color' => '5660B1'];

        // Header
        $table = $section->addTable();
        $table->addRow();
        $table->addCell(2000)->addImage(public_path('images/logo.png'), ['width' => 40, 'height' => 30]);
        $meta = $table->addCell(8000);
        $meta->addText("Order Confirmation", $header);
        $meta->addText("Date: " . $corder->podate, $style);
        $meta->addText("SO #: {$corder->our_ord_num}", $style);
        $meta->addText("Conf #: {$invoiceNo}", $style);

        // Vendor Info
        $section->addTextBreak();
        $infoTable = $section->addTable();
        $infoTable->addRow();
        $infoTable->addCell(4000)->addText("PCBs Global Incorporated", $bold);
        $infoTable->addCell(4000);
        $section->addText("2500 E. La Palma Ave.\nAnaheim Ca. 92806\nPhone: (855) 722-7456\nFax: (855) 262-5305", $style);

        // Billing / Shipping
        $section->addTextBreak(1);
        $addrTable = $section->addTable();
        $addrTable->addRow();
        $addrTable->addCell(4000, ['bgColor' => '656BBC'])->addText("BILL TO", ['bold' => true, 'color' => 'FFFFFF']);
        $addrTable->addCell(4000, ['bgColor' => '656BBC'])->addText("SHIP TO", ['bold' => true, 'color' => 'FFFFFF']);
        $addrTable->addRow();
        $bill = $addrTable->addCell(4000);
        $ship = $addrTable->addCell(4000);
        if ($vendor) {
            $bill->addText($vendor->c_name, $style);
            $bill->addText("(Accounts Payable)", $style);
            $bill->addText($vendor->c_address, $style);
            $bill->addText($vendor->c_address2, $style);
            $bill->addText($vendor->c_address3, $style);
            $bill->addText("Phone: {$vendor->c_phone}", $style);
            $bill->addText("Fax: {$vendor->c_fax}", $style);
            $bill->addText($vendor->c_website, $style);
        }
        if ($shipper) {
            $ship->addText($shipper->c_name, $style);
            $ship->addText($shipper->c_address, $style);
            $ship->addText($shipper->c_address2, $style);
            $ship->addText($shipper->c_address3, $style);
            $ship->addText("Phone: {$shipper->c_phone}", $style);
            $ship->addText("Fax: {$shipper->c_fax}", $style);
        }
        if ($corder->delto) $ship->addText("Delivered To: {$corder->delto}", $style);

        // Order Info Row
        $section->addTextBreak(1);
        $metaTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $metaTable->addRow();
        $metaTable->addCell(1200)->addText("CUSTOMER PO", $bold);
        $metaTable->addCell(800)->addText("SHIP VIA", $bold);
        $metaTable->addCell(1000)->addText("F.O.B.", $bold);
        $metaTable->addCell(800)->addText("TERMS", $bold);
        $metaTable->addCell(1000)->addText("CONTACT", $bold);
        $metaTable->addCell(1000)->addText("DELIVER TO", $bold);

        $metaTable->addRow();
        $metaTable->addCell(1200)->addText($corder->po, $style);
        $metaTable->addCell(800)->addText($corder->svia === 'Other' ? $corder->svia_oth : $corder->svia, $style);
        $metaTable->addCell(1000)->addText("{$corder->city}, {$corder->state}", $style);
        $metaTable->addCell(800)->addText($vendor->e_payment ?? '', $style);
        $metaTable->addCell(1000)->addText($corder->namereq, $style);
        $metaTable->addCell(1000)->addText($corder->delto, $style);

        // Items Table
        $section->addTextBreak(1);
        $itemTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $itemTable->addRow();
        $itemTable->addCell(800)->addText("ITEM #", $bold);
        $itemTable->addCell(3200)->addText("DESCRIPTION", $bold);
        $itemTable->addCell(800)->addText("TOTAL QTY", $bold);
        $itemTable->addCell(1000)->addText("UNIT PRICE", $bold);
        $itemTable->addCell(1000)->addText("TOTAL", $bold);

        $subtotal = 0;
        foreach ($corder->items as $i => $item) {
            $lineTotal = $item->qty2 * $item->uprice;
            $subtotal += $lineTotal;
            $itemTable->addRow();
            $desc = ($i == 0 ? "{$corder->part_no} Rev {$corder->rev} " : '') . $item->itemdesc;
            $itemTable->addCell(800)->addText($item->item, $style);
            $itemTable->addCell(3200)->addText($desc, $style);
            $itemTable->addCell(800)->addText($item->qty2, $style);
            $itemTable->addCell(1000)->addText('$' . number_format($item->uprice, 2), $style);
            $itemTable->addCell(1000)->addText('$' . number_format($lineTotal, 2), $style);
        }

        // Deliveries
        $section->addTextBreak(1);
        $deliveryTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        $deliveryTable->addRow();
        $deliveryTable->addCell(2000)->addText("Scheduled Qty", $bold);
        $deliveryTable->addCell(2000)->addText("Dock Date", $bold);

        foreach ($corder->deliveries as $delivery) {
            $deliveryTable->addRow();
            $deliveryTable->addCell(2000)->addText($delivery->qty, $style);
            $deliveryTable->addCell(2000)->addText($delivery->date, $style);
        }

        // Totals
        $section->addTextBreak();
        $st = floatval($corder->stax);
        $tax = $subtotal * $st;
        $total = $subtotal + $tax;

        $totalsTable = $section->addTable();
        $totalsTable->addRow();
        $totalsTable->addCell(6000);
        $right = $totalsTable->addCell(4000);
        $right->addText("Sub Total: $" . number_format($subtotal, 2), $bold);
        $right->addText("Sale Tax: $" . number_format($tax, 2), $bold);
        $right->addText("Total: $" . number_format($total, 2), $bold);

        // Comments + Footer
        $section->addTextBreak(1);
        if ($corder->comments) {
            $section->addText("Comments:\n" . $corder->comments, $style);
        }

        $section->addTextBreak(1);
        $section->addText("If any errors are found in this Order Confirmation, please contact:", $style);
        $section->addText("Armando Torres", $style);
        $section->addText("(855) 722-7456 x 102 or (714) 553-7047", $style);

        $section->addTextBreak(2);
        $section->addText("THANK YOU FOR YOUR BUSINESS AND TRUST!", ['bold' => true, 'size' => 12], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Output
        $filename = "OC-$invoiceNo-{$corder->customer}-{$corder->part_no}-{$corder->rev}-$shortname-$today.docx";
        $filepath = storage_path($filename);
        IOFactory::createWriter($phpWord, 'Word2007')->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
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
    public function downloadpurchaseorder($id){
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

            return $pdf->download("PO-{$poNumber}-{$porder->customer}.pdf");

    }
    // download doc purchase order ...
    public function downloaddocpurchaseorder($id){
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

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Header Row
        $table = $section->addTable();
        $table->addRow();

        // Logo Cell
        $cell = $table->addCell(4000);
        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $cell->addImage($logoPath, ['width' => 120, 'height' => 100]);
        }

        // PO Info Cell
        $cell = $table->addCell(5000);
        $cell->addText('Purchase Order', ['bold' => true, 'size' => 14, 'color' => '5660B1']);
        $cell->addText("Date: " . $porder->podate);
        $cell->addText("PO #: " . $poNumber);

        // Address
        $section->addTextBreak(1);
        $section->addText("PCBs Global Incorporated\n2500 E. La Palma Ave.\nAnaheim Ca. 92806\nPhone: (855) 722-7456\nFax: (855) 262-5305", ['size' => 10]);

        $section->addTextBreak(1);

        // Vendor & Ship To
        $table = $section->addTable();
        $table->addRow();
        $vendorCell = $table->addCell(4500);
        $shipperCell = $table->addCell(4500);

        $vendorCell->addText("VENDOR", ['bold' => true, 'bgColor' => '656BBC', 'color' => 'FFFFFF']);
        $vendorCell->addText($vendor->c_name ?? '');
        $vendorCell->addText($vendor->c_address ?? '');
        $vendorCell->addText(trim($vendor->c_address2 . ' ' . $vendor->c_address3));
        $vendorCell->addText("Phone: {$vendor->c_phone}");
        $vendorCell->addText("Fax: {$vendor->c_fax}");
        $vendorCell->addText($vendor->c_website);

        $shipperCell->addText("SHIP TO", ['bold' => true, 'bgColor' => '656BBC', 'color' => 'FFFFFF']);
        $shipperCell->addText($shipper->c_name ?? '');
        $shipperCell->addText($shipper->c_address ?? '');
        $shipperCell->addText(trim($shipper->c_address2 . ' ' . $shipper->c_address3));
        $shipperCell->addText("Phone: {$shipper->c_phone}");
        $shipperCell->addText("Fax: {$shipper->c_fax}");
        $shipperCell->addText($shipper->c_website);

        $section->addTextBreak(1);

        // Shipment Info Table
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell(2000)->addText('REQUISITIONER', ['bold' => true]);
        $table->addCell(2000)->addText('SHIP VIA', ['bold' => true]);
        $table->addCell(2000)->addText('F.O.B.', ['bold' => true]);
        $table->addCell(2000)->addText('SHIPPING TERMS', ['bold' => true]);

        $table->addRow();
        $table->addCell(2000)->addText($porder->namereq);
        $table->addCell(2000)->addText($porder->svia === 'Other' ? $porder->svia_oth : $porder->svia);
        $table->addCell(2000)->addText($porder->city . ', ' . $porder->state);
        $table->addCell(2000)->addText($porder->sterms);

        $section->addTextBreak(1);

        // Items Table
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '999999']);
        $table->addRow();
        $table->addCell()->addText('ITEM #', ['bold' => true]);
        $table->addCell()->addText('PART NUMBER', ['bold' => true]);
        $table->addCell()->addText('REV', ['bold' => true]);
        $table->addCell()->addText('LYRS', ['bold' => true]);
        $table->addCell()->addText('DESCRIPTION', ['bold' => true]);
        $table->addCell()->addText('QTY', ['bold' => true]);
        $table->addCell()->addText('UNIT PRICE', ['bold' => true]);
        $table->addCell()->addText('TOTAL', ['bold' => true]);

        $total = 0;
        foreach ($items as $i => $item) {
            $lineTotal = $item->qty2 * $item->uprice;
            $total += $lineTotal;
            $table->addRow();
            $table->addCell()->addText($item->item);
            $table->addCell()->addText($i === 0 ? $porder->part_no : '');
            $table->addCell()->addText($i === 0 ? $porder->rev : '');
            $table->addCell()->addText(explode('Lyrs', $porder->no_layer)[0]);
            $table->addCell()->addText($item->desc);
            $table->addCell()->addText($item->qty2);
            $table->addCell()->addText('$' . number_format($item->uprice, 2));
            $table->addCell()->addText('$' . number_format($lineTotal, 2));
        }

        $section->addText("Total: $" . number_format($total, 2), ['bold' => true]);

        // Customer Info
        $section->addText("Customer: {$porder->customer}");
        $section->addText("PO #: {$porder->po}");
        $section->addText("Boards to dock at destination {$porder->date1}");

        // Notes and special instructions
        if ($order?->special_instadmin) {
            $section->addText("Special Instructions:", ['bold' => true]);
            $instructions = explode('|', $order->special_instadmin);
            foreach ($instructions as $index => $inst) {
                $section->addText(($index + 1) . ') ' . $inst);
            }
        }

        if ($poNote?->note) {
            $section->addText("Additional Notes:", ['bold' => true]);
            Html::addHtml($section, nl2br(e($poNote->note)));
        }

        // Footer
        $section->addTextBreak();
        $section->addText("THANK YOU FOR YOUR BUSINESS AND TRUST!", ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addText("FM8.4.1", ['size' => 8], ['alignment' => 'right']);

        $filename = "PO-{$poNumber}-" . preg_replace('/[^A-Za-z0-9\-]/', '_', $porder->customer) . ".docx";
        $tempFile = storage_path("app/{$filename}");

        IOFactory::createWriter($phpWord, 'Word2007')->save($tempFile);

        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
    // for qoute section pdf ..
    public function viewPdfqoute($id)
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
                    <strong>Quote To:</strong><br>'.$order->customer.'
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
}