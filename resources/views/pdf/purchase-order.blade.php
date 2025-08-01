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

    .table,
    .table td,
    .table th {
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
    </style>
</head>

<body>
    <table width="100%">
        <tr>
            <td><img src="{{ $base64Logo }}" width="150" height="150px" /></td>
            <td></td>
            <td class="text-right">
                <h1 class="title">Purchase Order</h1>
                Date: <strong>{{ $porder->podate }}</strong><br>
                PO #: <strong>{{ $poNumber }}</strong>
            </td>
        </tr>
    </table>

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

    <table width="100%">
        <tr>
            <td class="section-title" colspan="2" width="45%">VENDOR</td>
            <td width="10%"></td>
            <td class="section-title" colspan="2" width="45%">SHIP TO</td>
        </tr>
        <tr>
            <td colspan="2">
                {{ $vendor->c_name ?? '' }}<br>
                {{ $vendor->c_address ?? '' }}<br>
                {{ $vendor->c_address2 }} {{ $vendor->c_address3 }}<br>
                Phone: {{ $vendor->c_phone ?? '' }}<br>
                Fax: {{ $vendor->c_fax ?? '' }}<br>
                {{ $vendor->c_website ?? '' }}
            </td>
            <td></td>
            <td colspan="2">
                {{ $shipper->c_name ?? '' }}<br>
                {{ $shipper->c_address ?? '' }}<br>
                {{ $shipper->c_address2 ?? '' }} {{ $shipper->c_address3 ?? '' }}<br>
                Phone: {{ $shipper->c_phone ?? '' }}<br>
                Fax: {{ $shipper->c_fax ?? '' }}<br>
                {{ $shipper->c_website ?? '' }}
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr class="section-title text-center">
            <td>REQUISITIONER</td>
            <td>SHIP VIA</td>
            <td>F.O.B.</td>
            <td>SHIPPING TERMS</td>
        </tr>
        <tr class="text-center">
            <td>{{ $porder->namereq }}</td>
            <td>{{ $porder->svia === 'Other' ? $porder->svia_oth : $porder->svia }}</td>
            <td>{{ $porder->city }}, {{ $porder->state }}</td>
            <td>{{ $porder->sterms }}</td>
        </tr>
    </table>

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
        <tbody>
            @php $total = 0; @endphp
            @foreach ($items as $i => $item)
            @php $lineTotal = $item->qty2 * $item->uprice; $total += $lineTotal; @endphp
            <tr class="text-center">
                <td>{{ $item->item }}</td>
                <td>@if($i == 0) {{ $porder->part_no }} @endif</td>
                <td>@if($i == 0) {{ $porder->rev }} @endif</td>
                <td>{{ explode('Lyrs', $porder->no_layer)[0] }}</td>
                <td>{{ $itemDescriptions[$item->dpval] ?? $item->itemdesc }}</td>
                <td>{{ $item->qty2 }}</td>
                <td>${{ number_format($item->uprice, 2) }}</td>
                <td>${{ number_format($lineTotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td>
                <strong>Total:</strong> ${{ number_format($total, 2) }}
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="font-size: 10pt">
                @if ($porder->iscancel === 'no')
                Customer: {{ $porder->customer }}<br>
                PO #: {{ $porder->po }}<br>
                Boards to dock at destination {{ $porder->date1 }}<br>

                @if ($porder->rohs === 'yes')
                RoHS Certs required<br>
                @endif

                @if (isset($poNote) && $poNote->ntext)
                {!! nl2br(e($poNote->ntext)) !!}<br>
                @endif
                @endif

                @if ($porder->sp_reqs)
                @php $specials = explode('|', $porder->sp_reqs); @endphp
                <strong>Special Requirements:</strong><br>
                <div style="width: 750px; font-size: 9pt; padding-bottom: 0px">
                    @foreach ($specials as $index => $req)
                    {{ $index + 1 }}.) {{ $req }}<br>
                    @endforeach
                </div>
                @endif

                @if($porder->comments)
                <div style="padding-bottom:5px;"><strong>Additional Requirements</strong></div>
                {!! nl2br(e($porder->comments)) !!}<br>
                @endif

                @if ($porder->iscancel === 'no')
                <p>
                    Invoice: armando@pcbsglobal.com and silvia@pcbsglobal.com<br>
                    Email working data to: armando@pcbsglobal.com and isaac@pcbsglobal.com<br>
                    Please refer any questions to: armando@pcbsglobal.com and isaac@pcbsglobal.com<br>
                </p>
                @else
                Please refer any questions to: armando@pcbsglobal.com and isaac@pcbsglobal.com<br>
                @endif
            </td>
        </tr>
    </table>

    <p class="text-center" style="font-size: 14pt;"><strong>THANK YOU FOR YOUR BUSINESS AND TRUST!</strong></p>
    <span style="position:absolute;bottom:0px;font-size:8px;">FM8.4.1</span>
</body>

</html>