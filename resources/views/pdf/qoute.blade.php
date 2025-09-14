<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
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
                padding: 0px;
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
            /* PRICE TABLE STYLES */
        .price-table {
            width: 100%;
            border-collapse: collapse;
            border: 3px double #000;
            margin-top: 8px;
            table-layout: fixed; /* This is key for consistent column widths */
        }

        .price-table th, 
        .price-table td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            font-size: 7.5pt; /* Smaller font */
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap; /* Prevent text wrapping */
        }

        /* Specific column widths */
        .price-table th:nth-child(1),
        .price-table td:nth-child(1) {
            width: 12%; /* Option column */
            text-align: left;
            padding-left: 5px;
        }

        .price-table th:nth-child(2),
        .price-table td:nth-child(2) {
            width: 10%; /* Qty column */
        }

        /* Price columns - dynamically calculated width */
        .price-table th:nth-child(n+3),
        .price-table td:nth-child(n+3) {
            width: calc(78% / {{ count($dayOptions) }}); /* Distribute remaining space */
            min-width: 50px; /* Minimum width for price columns */
        }

        .price-table .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
            font-size: 7.2pt; /* Slightly smaller for totals */
        }

        .price-table .total-label {
            text-align: right;
            padding-right: 8px;
        }
        /* Specific column widths */
        .price-table th:nth-child(1),
        .price-table td:nth-child(1) {
            width: 12%; /* Option column */
            text-align: left;
            padding-left: 5px;
        }

        .price-table th:nth-child(2),
        .price-table td:nth-child(2) {
            width: 10%; /* Qty column */
        }

        /* Price columns - dynamically calculated width */
        .price-table th:nth-child(n+3),
        .price-table td:nth-child(n+3) {
            width: calc(78% / {{ count($dayOptions) }}); /* Distribute remaining space */
            min-width: 50px; /* Minimum width for price columns */
        }

        .price-table .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
            font-size: 7.2pt; /* Slightly smaller for totals */
        }

        .price-table .total-label {
            text-align: right;
            padding-right: 8px;
        }

        /* Make the text more compact */
        .compact-text {
            font-size: 7.2pt;
            line-height: 1;
            margin: 0;
            padding: 0;
        }

        /* Make the text more compact */
        .compact-text {
            font-size: 7.2pt;
            line-height: 1;
            margin: 0;
            padding: 0;
        }
            /* FOOTER */
            .footer {
                font-size: 7.5pt;
                margin-top: 15px;
                line-height: 1.2;
            }
            .price-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 8px;
                border: 3px double #000;
                font-size: 8pt; /* Smaller font for more columns */
            }

            .price-table th, 
            .price-table td {
                border: 1px solid #000;
                padding: 3px;
                text-align: center;
            }

            .price-table .option-header {
                text-align: left;
                font-weight: bold;
            }

            .price-table .total-row {
                font-weight: bold;
                background-color: #f0f0f0;
            }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="logo">
                <img src="{{ public_path('images/logo.png') }}" style="width:150px; height:150px;">
            </td>
            <td>
                <div class="quote-title">Quotation</div>
                <div class="company-info">
                    <strong>PCBs Global Incorporated.</strong><br>
                    Phone (855) 722-7456<br>
                    Fax: (855) 262-5305<br>
                    sales@pcbsglobal.com<br>
                    Quote Prepared By: {{ $quote->prepared_by ?? 'Isaac' }}
                </div>
            </td>
            <td class="quote-details">
                <strong>Quote No :</strong> {{ $quote->ord_id + 10000 }}<br>
                <strong>Quotation Date :</strong> {{ \Carbon\Carbon::parse($quote->created_at)->format('m/d/Y') }}<br>
                <strong>Quote Valid for :</strong> 30 Days<br><br>
                <strong>Quote To:</strong><br>{{ $quote->cust_name }}, <br /> {{ $quote->email }}
            </td>
        </tr>
    </table>

    <!-- SECTION BAR -->
    <div class="section-bar">Order Information</div>

    <!-- ORDER DETAILS -->
    <table class="info-table">
        <tr>
            <td><strong>Part Number:</strong> {{ $quote->part_no }}</td>
            <td><strong>Revision:</strong> {{ $quote->rev }}</td>
            <td><strong>PCB Type:</strong> {{ str_replace('Lyrs', 'Lyr', $quote->no_layer) }}</td>
            <td><strong>Material:</strong> {{ $quote->m_require }}</td>
            <td><strong>Thick:</strong> {{ $quote->thickness }} {{ $quote->thickness_tole }}</td>
            <td><strong>FOB:</strong> {{ $quote->fob }}</td>
            <td><strong>IPC Class:</strong> {{ $quote->ipc_class }}</td>
        </tr>
        <tr>
            <td><strong>Array Info:</strong> {{ $quote->array }}</td>
            <td colspan="2"><strong>Bd size:</strong> {{ $quote->board_size1 }} @if(!empty($quote->board_size2) && !empty($quote->board_size1) ) X @endif {{ $quote->board_size2 }}</td>
            <td><strong>Imp:</strong>
                @if($quote->con_impe_sing) Single @endif
                @if($quote->con_impe_diff) Differential @endif
            </td>
            <td colspan="3"><strong>Finish:</strong> {{ $quote->finish }}</td>
        </tr>
        <tr class="notes-row">
            <td colspan="7">
                <strong>Special Requirements / Notes:</strong>
                <table class="notes-table">
                    <tr>
                        <td>
                            <ol>
                                @if($quote->inner_copper)<li>{{ str_replace('Oz', 'Oz.', $quote->inner_copper) }} Cu
                                    Internal</li>@endif
                                @if($quote->start_cu)<li>{{ str_replace('Oz', 'Oz.', $quote->start_cu) }} Cu External
                                </li>@endif
                                @if($quote->plated_cu)<li>Other Plated Cu in Holes (Min.) {{ $quote->plated_cu }}</li>
                                @endif
                                @if($quote->trace_min)<li>Trace Min. = {{ $quote->trace_min }}</li>@endif
                                @if($quote->space_min)<li>Space Min. = {{ $quote->space_min }}</li>@endif
                            </ol>
                        </td>
                        <td>
                            <ol start="6">
                                @if($quote->design_array != 'no')<li>Factory to Design Array</li>@endif
                                @if($quote->array_type2)<li>V Score Array Type</li>@endif 
                                @if($quote->array_require1)<li>Array Requires Tooling Holes</li>@endif
                                @if($quote->counter_sink != 'no')<li>Countersink Required</li>@endif
                                @if($quote->cut_outs != 'No')<li>Control Depth Required </li>@endif
                            </ol>
                        </td>
                        <td>
                            <ol start="11">
                                @if($quote->logo === 'Factory')<li>Factory Logo</li>@endif
                                @if($quote->date_code)<li>{{ $quote->date_code }} Date Code Format</li>@endif
                                @if($quote->array_rail)<li>In Array Rail Electrical Test Stamp</li>@endif
                                @if($quote->xouts != 'no')<li>X-Out Allowed per Array </li>@endif
                                @if($quote->rosh_cert != 'No')<li>RoHS Cert Required  </li>@endif
                            </ol>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<!-- PRICE TABLE -->
@if(count($prices) > 0 && count($dayOptions) > 0)
<table class="price-table">
    <tr>
        <th>Option</th>
        <th>Qty</th>
        @foreach($dayOptions as $dayOpt)
        <th>{{ $dayOpt['value'] }} Days</th>
        @endforeach
    </tr>
    
    @foreach($prices as $index => $price)
    <!-- Price row -->
    <tr>
        <td><span class="compact-text">Option {{ $index + 1 }}</span></td>
        <td><span class="compact-text">{{ $price['qty'] }} Pcs</span></td>
        
        @foreach($dayOptions as $dayOpt)
        <td>
            @if($price['day'.$dayOpt['day']] > 0)
            <span class="compact-text">${{ number_format($price['day'.$dayOpt['day']], 2) }} ea</span>
            @else
            &nbsp;
            @endif
        </td>
        @endforeach
    </tr>
    
    <!-- Total row -->
    <tr class="total-row">
        <td colspan="2" class="total-label">
            <span class="compact-text">
                <strong>{{ $quote->new_or_rep == 'New Part' ? 'NRE/' : '' }}FOB Included</strong>
            </span>
        </td>
        
        @foreach($dayOptions as $dayOpt)
        <td>
            @if($price['day'.$dayOpt['day']] > 0)
            <?php
            $unitPrice = (float) str_replace(',', '', $price['day'.$dayOpt['day']]);
            $total = ($price['qty'] * $unitPrice) + $totalMisc;
            ?>
            <span class="compact-text"><strong>${{ number_format($total, 2) }}</strong></span>
            @else
            &nbsp;
            @endif
        </td>
        @endforeach
    </tr>
    
    <!-- Small spacer between options -->
    <tr style="height: 2px;">
        <td colspan="{{ count($dayOptions) + 2 }}" style="border: none; background: transparent;"></td>
    </tr>
    @endforeach
</table>
@else
<p>No pricing options available.</p>
@endif
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

</html>