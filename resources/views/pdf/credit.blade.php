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
            <td><img src="{{ public_path('images/logo.png') }}" width="150"></td>
            <td class="header">
                <span class="title"><b>CREDIT</b></span><br>
                CREDIT OFFSET #: <strong>{{ $credit->credit_id + 10098 }}</strong><br>
                CREDIT DATE: <strong>{{ \Carbon\Carbon::parse($credit->credit_date)->format('m/d/Y') }}</strong><br>
                OUR ORDER NO: <strong>{{ $credit->our_ord_num }}</strong><br>
                YOUR ORDER NO: <strong>{{ $credit->po }}</strong><br>
                INVOICE NO: <strong>{{ $credit->inv_id }}</strong><br>
                SALES REP: <strong>{{ $credit->namereq }}</strong><br>
                SHIPPED VIA:
                <strong>{{ $credit->svia === 'Other' ? ($credit->svia_oth ?: 'Other') : $credit->svia }}</strong><br>
            </td>
        </tr>
        <tr>
            <td>
                <strong>PCBs Global Incorporated</strong>
                <br>
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
                {{ $credit->custo->c_name ?? '' }}<br>
                {{ $credit->custo->c_address ?? '' }}<br>
                {{ $credit->custo->c_address2 ?? '' }}<br>
                {{ $credit->custo->c_address3 ?? '' }}<br>
                Phone: {{ $credit->custo->c_phone ?? '' }}<br>
                Fax: {{ $credit->custo->c_fax ?? '' }}<br>
                {{ $credit->custo->c_website ?? '' }}
            </td>
            <td></td>
            <td colspan="2">
                {{ $shipper->c_name ?? '' }}<br>
                {{ $shipper->c_address ?? '' }}<br>
                {{ $shipper->c_address2 ?? '' }}<br>
                {{ $shipper->c_address3 ?? '' }}<br>
                Phone: {{ $shipper->c_phone ?? '' }}<br>
                Fax: {{ $shipper->c_fax ?? '' }}<br>
                @if ($credit->ord_by)
                Ordered by: {{ $credit->ord_by }}<br>
                @endif
                @if ($credit->delto)
                Delivered to: {{ $credit->delto }}<br>
                @endif
                @if ($credit->date1)
                Delivered On: {{ $credit->date1 }}
                @endif
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" class="table">
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
            @foreach ($credit->items as $index => $item)
            <tr>
                <td>{{ $item->item }}</td>
                <td>{{ $index === 0 ? $credit->part_no : '' }}</td>
                <td>{{ $index === 0 ? $credit->rev : '' }}</td>
                <td>{{ $index === 0 ? explode('Lyrs', $credit->no_layer)[0] : '' }}</td>
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
                        <td class="right">${{ number_format((float)$credit->fcharge, 2) }}</td>
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
    {{ $credit->comments }}<br><br>

    Direct All Inquiries To:<br>
    Armando Torres<br>
    714-553-7047<br>
    armando@pcbsglobal.com

    <br><br><br>
    <p style="text-align: center; font-size: 12pt;"><strong>THIS IS A CREDIT. DO NOT PAY!</strong></p>

</body>

</html>