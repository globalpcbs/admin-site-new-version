<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
    <style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 8pt;
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
            <td><img src="{{ public_path('images/logo.png') }}" width="120"></td>
            <td></td>
            <td class="text-right">
                <h1 class="title">Order Confirmation</h1>
                Date: <strong>{{ $corder->podate }}</strong><br>
                SO #: <strong>{{ $corder->our_ord_num }}</strong><br>
                Conf #: <strong>{{ $invoiceNo }}</strong>
            </td>
        </tr>
    </table>

    <br>

    <table width="100%">
        <tr>
            <td><strong>PCBs Global Incorporated</strong><br>
                2500 E. La Palma Ave.<br>
                Anaheim Ca. 92806<br>
                Phone: (855) 722-7456<br>
                Fax: (855) 262-5305
            </td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <br><br>

    {{-- Bill To / Ship To --}}
    <table width="100%">
        <tr>
            <td class="section-title" colspan="2" width="45%">BILL TO</td>
            <td width="10%"></td>
            <td class="section-title" colspan="2" width="45%">SHIP TO</td>
        </tr>
        <tr>
            <td colspan="2">
                {{ $vendor->c_name ?? '' }}<br>
                (Accounts Payable)<br>
                {{ $vendor->c_address ?? '' }}<br>
                {{ $vendor->c_address2 ?? '' }} {{ $vendor->c_address3 ?? '' }}<br>
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
                @if($corder->delto)
                Delivered to: {{ $corder->delto }}
                @endif
            </td>
        </tr>
    </table>

    <br>

    {{-- Order Info --}}
    <table width="100%">
        <tr class="section-title text-center">
            <td width="15%">CUSTOMER PO</td>
            <td width="15%">SHIP VIA</td>
            <td width="15%">F.O.B.</td>
            <td width="15%">TERMS</td>
            <td width="20%">CUSTOMER CONTACT</td>
            <td width="20%">DELIVER TO</td>
        </tr>
        <tr class="text-center">
            <td>{{ $corder->po }}</td>
            <td>{{ $corder->svia === 'Other' ? $corder->svia_oth : $corder->svia }}</td>
            <td>{{ $corder->city }}, {{ $corder->state }}</td>
            <td>{{ $vendor->e_payment ?? '' }}</td>
            <td>{{ $corder->namereq }}</td>
            <td>{{ $corder->delto }}</td>
        </tr>
    </table>

    <br>

    {{-- Items Table --}}
    <table width="100%" class="table">
        <thead class="text-center section-title">
            <tr>
                <th>ITEM #</th>
                <th>DESCRIPTION</th>
                <th>TOTAL QTY</th>
                <th>UNIT PRICE</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach ($corder->items as $i => $item)
            @php
            $lineTotal = $item->qty2 * $item->uprice;
            $subtotal += $lineTotal;
            @endphp
            <tr class="text-center">
                <td>{{ $item->item }}</td>
                <td>
                    @if($i == 0)
                    {{ $corder->part_no }} Rev {{ $corder->rev }}<br>
                    @endif
                    {{ $item->itemdesc }}
                </td>
                <td>{{ $item->qty2 }}</td>
                <td>${{ number_format($item->uprice, 2) }}</td>
                <td>${{ number_format($lineTotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    {{-- Deliveries --}}
    <table width="100%">
        <thead class="section-title text-center">
            <tr>
                <td width="50%">Scheduled Qty</td>
                <td width="50%">Dock Date</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($deliveries as $delivery)
            <tr class="text-center">
                <td>{{ $delivery->qty }}</td>
                <td>{{ $delivery->date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <hr>

    {{-- Totals --}}
    @php
    $taxRate = floatval($corder->stax ?? 0);
    $salesTax = $subtotal * $taxRate;
    $total = $subtotal + $salesTax;
    @endphp

    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td>
                <strong>Sub Total:</strong> ${{ number_format($subtotal, 2) }}<br>
                <strong>Sale Tax:</strong> ${{ number_format($salesTax, 2) }}<br>
                <strong>Total:</strong> ${{ number_format($total, 2) }}
            </td>
        </tr>
    </table>

    <br><br>

    {{-- Comments --}}
    @if($corder->comments)
    <strong>Comments:</strong><br>
    {{ $corder->comments }}<br><br>
    @endif

    <p>
        If any errors are found in this Order Confirmation, please contact:<br>
        Armando Torres<br>
        (855) 722-7456 x 102 or (714) 553-7047
    </p>

    <br><br>
    <p class="text-center" style="font-size: 14pt;"><strong>THANK YOU FOR YOUR BUSINESS AND TRUST!</strong></p>

    <span style="position:absolute;bottom:0px;font-size:8px;">FM8.5.2</span>

</body>

</html>