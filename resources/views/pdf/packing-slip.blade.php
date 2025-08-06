<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10pt;
    }

    .header {
        text-align: right;
    }

    .title {
        font-size: 24pt;
        color: #5660B1;
    }

    .section-title {
        background: #656BBC;
        color: #fff;
        padding: 5px;
    }

    .table,
    .table td,
    .table th {
        padding: 2px;
    }

    .table th {
        background: #656BBC;
        color: #fff;
    }

    .right {
        text-align: right;
    }
    
    .currency {
        text-align: right;
        padding-right: 10px;
    }
    </style>
</head>

<body>

    @php
    // Custom date parsing function
    function formatCustomDate($dateString) {
        if (empty($dateString)) {
            return '';
        }
        
        // Handle "Wednesday-10-10-2018" format
        if (preg_match('/^[A-Za-z]+-\d{1,2}-\d{1,2}-\d{4}$/', $dateString)) {
            $parts = explode('-', $dateString);
            $month = $parts[1];
            $day = $parts[2];
            $year = $parts[3];
            
            try {
                return \Carbon\Carbon::createFromDate($year, $month, $day)->format('l, m/d/Y');
            } catch (\Exception $e) {
                return $dateString;
            }
        }
        
        // Try standard parsing as fallback
        try {
            return \Carbon\Carbon::parse($dateString)->format('m/d/Y');
        } catch (\Exception $e) {
            return $dateString;
        }
    }

    // Format currency with $ sign
    function formatCurrency($value) {
        return '$' . number_format($value, 2);
    }
    @endphp

    <table width="100%">
        <tr>
            <td><img src="{{ public_path('images/logo.png') }}" width="150"></td>
            <td class="header">
                <span class="title"><b>Packing Slip</b></span><br>
                Ordered Date: <strong>{{ formatCustomDate($packing->odate) }}</strong><br>
                Date: <strong>{{ formatCustomDate($packing->podate) }}</strong><br>
                Our Order No: <strong>{{ $packing->our_ord_num }}</strong><br>
                Packing Slip No: <strong>{{ $invoiceNo }}</strong><br>
                Purchase Order No: <strong>{{ $packing->po }}</strong><br>
                Acct No: <strong>{{ $customer->e_other ?? '' }}</strong><br>
                Cust ID: <strong>{{ $customer->e_cid ?? '' }}</strong><br>
                SHIPPED VIA:
                <strong>{{ $packing->svia === 'Other' ? ($packing->svia_oth ?: 'Other') : $packing->svia }}</strong><br>
                <strong>Customer contacts:</strong><br>
                @foreach ($contacts as $c)
                {{ $c->name }} {{ $c->lastname }} - {{ $c->phone }}<br>
                @endforeach
            </td>
        </tr>
    </table>

    <hr>
    <!-- Part Number / Rev Table -->
    <table width="50%" border="0" style="margin-top: 15px;">
        <tr style="background-color:#656BBC; color:#FFF;">
            <td width="50%"></td>
            <td width="150" style="padding: 5px;"><strong>PART NUMBER</strong></td>
            <td width="100" style="padding: 5px;"><strong>REV</strong></td>
        </tr>
        <tr>
            <td width="50%"></td>
            <td style="padding: 5px;">{{ $packing->part_no }}</td>
            <td style="padding: 5px;">{{ $packing->rev }}</td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td class="section-title" colspan="2">BILL TO</td>
            <td></td>
            <td class="section-title" colspan="2">SHIP TO</td>
        </tr>
        <tr>
            <td colspan="2">
                {{ $vendor->c_name ?? '' }}<br>
                (Accounts Payable)<br>
                {{ $vendor->c_address ?? '' }}<br>
                {{ $vendor->c_address2 ?? '' }}<br>
                {{ $vendor->c_address3 ?? '' }}<br>
                Phone: {{ $vendor->c_phone ?? '' }}<br>
                Fax: {{ $vendor->c_fax ?? '' }}<br>
                {{ $vendor->c_website ?? '' }}
            </td>
            <td></td>
            <td colspan="2">
                {{ $shipper->c_name ?? '' }}<br>
                {{ $shipper->c_address ?? '' }}<br>
                {{ $shipper->c_address2 ?? '' }}<br>
                {{ $shipper->c_address3 ?? '' }}<br>
                Phone: {{ $shipper->c_phone ?? '' }}<br>
                Fax: {{ $shipper->c_fax ?? '' }}<br>
                @if ($packing->delto) Delivered to: {{ $packing->delto }}<br> @endif
                @if ($packing->date1) Delivered On: {{ formatCustomDate($packing->date1) }} @endif
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" border="1" class="table">
        <thead>
            <tr>
                <th>ITEM #</th>
                <th>PART NUMBER</th>
                <th>REV</th>
                <th>LYRS</th>
                <th>DESCRIPTION</th>
                <th class="currency">UNIT PRICE</th>
                <th class="currency">QTY ORDERED</th>
                <th class="currency">QTY DELIVERED</th>
                <th class="currency">LINE TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php 
            $qtot = 0; 
            $totq = 0;
            $grandTotal = 0;
            @endphp
            @foreach ($packing->items as $index => $item)
            @php
            $lineTotal = $item->uprice * $item->shipqty;
            $grandTotal += $lineTotal;
            @endphp
            <tr>
                <td>{{ $item->item }}</td>
                <td>{{ $index === 0 ? $packing->part_no : '' }}</td>
                <td>{{ $index === 0 ? $packing->rev : '' }}</td>
                <td>{{ $index === 0 ? explode('Lyrs', $packing->no_layer)[0] : '' }}</td>
                <td>{{ $item->itemdesc }}</td>
                <td class="currency">{{ formatCurrency($item->uprice) }}</td>
                <td class="currency">{{ $item->qty2 }}</td>
                <td class="currency">{{ $item->shipqty }}</td>
                <td class="currency">{{ formatCurrency($lineTotal) }}</td>
            </tr>
            @php
            $qtot += (int)$item->qty2;
            $totq += (int)$item->shipqty;
            @endphp
            @endforeach
        </tbody>
    </table>

    <br><br>

    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td>
                <strong>Total Ordered:</strong> {{ $qtot }}<br>
                <strong>Total Delivered:</strong> {{ $totq }}<br>
                <strong>Grand Total:</strong> {{ formatCurrency($grandTotal) }}
            </td>
        </tr>
    </table>

    <br><br>

    <p>
        If you have any issues with your order, please contact:<br>
        Armando Torres<br>
        714-553-7047<br>
        armando@pcbsglobal.com<br><br>
        {{ $packing->comments }}
    </p>

    <p style="text-align: center; font-size: 12pt;"><strong>THANK YOU FOR YOUR BUSINESS AND TRUST!</strong></p>

    <span style="position:absolute;bottom:0px;font-size:8px;">FM8.5.1</span>

</body>
</html>