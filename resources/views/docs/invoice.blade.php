<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;
    }

    h1 {
        color: #5660B1;
        font-size: 40pt;
    }

    .title {
        margin-bottom: 10px;
    }

    .label {
        font-weight: bold;
    }

    .table th,
    .table td {
        padding: 4px;
        border: 1px solid #999;
    }

    .table {
        border-collapse: collapse;
        width: 100%;
        font-size: 9pt;
    }

    .blue-header {
        background-color: #656BBC;
        color: white;
        padding: 4px;
        font-weight: bold;
    }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td><img src="{{ public_path('images/logo.jpg') }}" width="40" height="30"></td>
            <td></td>
            <td align="right">
                <h1>Invoice</h1>
                <div class="title">
                    <div><span class="label">INVOICE NUMBER:</span> {{ $invoice->invoice_id + 9976 }}</div>
                    <div><span class="label">INVOICE DATE:</span>
                        {{ \Carbon\Carbon::parse($invoice->podate)->format('m/d/Y') }}</div>
                    <div><span class="label">OUR ORDER NO:</span> {{ $invoice->our_ord_num }}</div>
                    <div><span class="label">YOUR ORDER NO:</span> {{ $invoice->po }}</div>
                    <div><span class="label">TERMS:</span> {{ $invoice->sterm }}</div>
                    <div><span class="label">SALES REP:</span> {{ $invoice->namereq }}</div>
                    <div><span class="label">SHIPPED VIA:</span>
                        {{ $invoice->svia === 'Other' ? $invoice->svia_oth : $invoice->svia }}</div>
                    <div><span class="label">F.O.B:</span> Anaheim CA</div>
                </div>
            </td>
        </tr>
    </table>

    <hr>

    <table width="100%">
        <tr>
            <td class="blue-header">SOLD TO</td>
            <td></td>
            <td class="blue-header">SHIPPED TO</td>
        </tr>
        <tr>
            <td>
                {{ $invoice->custo->c_name ?? '' }}<br>
                {{ $invoice->custo->c_address ?? '' }}<br>
                {{ $invoice->custo->c_address2 ?? '' }}<br>
                {{ $invoice->custo->c_address3 ?? '' }}<br>
                Phone: {{ $invoice->custo->c_phone ?? '' }}<br>
                Fax: {{ $invoice->custo->c_fax ?? '' }}<br>
                {{ $invoice->custo->c_website ?? '' }}
            </td>
            <td></td>
            <td>
                @if ($invoice->ord_by)<strong>Ordered by:</strong> {{ $invoice->ord_by }}<br>@endif
                @if ($invoice->delto)<strong>Delivered to:</strong> {{ $invoice->delto }}<br>@endif
                @if ($invoice->date1)<strong>Delivered On:</strong> {{ $invoice->date1 }}@endif
            </td>
        </tr>
    </table>

    <br>

    <table class="table">
        <thead>
            <tr>
                <th>ITEM #</th>
                <th>DESCRIPTION</th>
                <th>QTY</th>
                <th>UNIT PRICE</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
            <tr>
                <td>{{ $item->item }}</td>
                <td><strong>P/N</strong> {{ $invoice->part_no }} <strong>Rev</strong> {{ $invoice->rev }}
                    {{ $item->itemdesc }}</td>
                <td>{{ $item->qty2 }}</td>
                <td>${{ number_format((float)$item->uprice, 2) }}</td>
                <td>${{ number_format((float)$item->tprice, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td>
                <table>
                    <tr>
                        <td><strong>SUB TOTAL:</strong></td>
                        <td>${{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>TAX:</strong></td>
                        <td>${{ number_format($tax, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>FREIGHT:</strong></td>
                        <td>${{ number_format((float)$invoice->fcharge, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL:</strong></td>
                        <td><strong>${{ number_format($total, 2) }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>
    <strong>Comments:</strong><br>
    {{ $invoice->comments ?? '' }}

    <br><br>
    <strong>Direct All Inquiries To:</strong><br>
    Armando Torres<br>
    714-553-7047<br>
    armando@pcbsglobal.com<br>

    <br><br>
    <strong>Make All Checks Payable To:</strong><br>
    Torres Developments<br>
    2500 E. La Palma Ave.<br>
    Anaheim CA 92806<br>

    <br><br>
    <p style="text-align:center;font-size:12pt;"><strong>THANK YOU FOR YOUR BUSINESS AND TRUST!</strong></p>

</body>

</html>