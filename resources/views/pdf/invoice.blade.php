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
        padding: 1px;
    }

    .table th {
        background: #656BBC;
        color: #fff;
    }

    .right {
        text-align: right;
    }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td><img src="{{ public_path('images/logo.png') }}" width="150" height="150px" /></td>
            <td class="header">
                <span class="title"><b>INVOICE</b></span><br>
                INVOICE NUMBER: <strong>{{ $invoice->invoice_id + 9976 }}</strong><br>
                INVOICE DATE: <strong>{{ \Carbon\Carbon::parse($invoice->podate)->format('m/d/Y') }}</strong><br>
                OUR ORDER NO: <strong>{{ $invoice->our_ord_num }}</strong><br>
                YOUR ORDER NO: <strong>{{ $invoice->po }}</strong><br>
                TERMS: <strong>{{ $invoice->sterm }}</strong><br>
                SALES REP: <strong>{{ $invoice->namereq }}</strong><br>
                SHIPPED VIA:
                <strong>{{ $invoice->svia === 'Other' ? ($invoice->svia_oth ?: 'Other') : $invoice->svia }}</strong><br>
                F.O.B: Anaheim CA
            </td>
        </tr>
        <tr>
            <td>
                <strong>PCBs Global Incorporated</strong><br>
                2500 E. La Palma Ave.<br>
                Anaheim Ca. 92806<br>
                Phone: (855) 722-7456<br>
                Fax: (855) 262-5305<br>
            </td>
            <td width="250"></td>
            <td></td>
        </tr>
    </table>

    <hr>

    <table width="100%">
        <tr>
            <td class="section-title" colspan="2">SOLD TO</td>
            <td></td>
            <td class="section-title" colspan="2">SHIPPED TO</td>
        </tr>
        <tr>
            <td colspan="2">
                {{ $invoice->custo->c_name ?? $invoice->customer }}<br>
                {{ $invoice->custo->c_address ?? '' }}<br>
                {{ $invoice->custo->c_address2 ?? '' }}<br>
                {{ $invoice->custo->c_address3 ?? '' }}<br>
                Phone: {{ $invoice->custo->c_phone ?? '' }}<br>
                Fax: {{ $invoice->custo->c_fax ?? '' }}<br>
                {{ $invoice->custo->c_website ?? '' }}
            </td>
            <td></td>
            <td colspan="2">
                {{ $shipper->c_name ?? '' }}<br>
                {{ $shipper->c_address ?? '' }}<br>
                {{ $shipper->c_address2 ?? '' }}<br>
                {{ $shipper->c_address3 ?? '' }}<br>
                Phone: {{ $shipper->c_phone ?? '' }}<br>
                Fax: {{ $shipper->c_fax ?? '' }}<br>
                @if ($invoice->ord_by)
                Ordered by: {{ $invoice->ord_by }}<br>
                @endif
                @if ($invoice->delto)
                Delivered to: {{ $invoice->delto }}<br>
                @endif
                @if ($invoice->date1)
                Delivered On: {{ $invoice->date1 }}
                @endif
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" class="table" border="1" cellspacing="0" cellpadding="2">
        <thead>
            <tr>
                <th>ITEM #</th>
                <th>PART NUMBER</th>
                <th>REV</th>
                <th>LYRS</th>
                <th>DESCRIPTION</th>
                <th>QTY</th>
                <th class="right">UNIT PRICE</th>
                <th class="right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
            <tr>
                <td>{{ $item->item }}</td>
                <td>{{ $index === 0 ? $invoice->part_no : '' }}</td>
                <td>{{ $index === 0 ? $invoice->rev : '' }}</td>
                <td>{{ $index === 0 ? explode('Lyrs', $invoice->no_layer)[0] : '' }}</td>
                <td>{{ $item->itemdesc }}</td>
                <td>{{ $item->qty2 }}</td>
                <td class="right">${{ number_format((float)$item->uprice, 2) }}</td>
                <td class="right">${{ number_format((float)$item->tprice, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td>
                <table>
                    <tr>
                        <td>SUB TOTAL:</td>
                        <td class="right">${{ number_format((float)$subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>TAX:</td>
                        <td class="right">${{ number_format((float)$tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td>FREIGHT:</td>
                        <td class="right">${{ number_format((float)$invoice->fcharge, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL:</strong></td>
                        <td class="right"><strong>${{ number_format((float)$total, 2) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>

    <strong>Comments</strong><br>
    {{ $invoice->comments }}<br><br>

    Direct All Inquiries To:<br>
    Armando Torres<br>
    714-553-7047<br>
    armando@pcbsglobal.com

    <br><br><br>
    <p style="text-align: center; font-size: 12pt;"><strong>THANK YOU FOR YOUR BUSINESS AND TRUST!</strong></p>

</body>

</html>