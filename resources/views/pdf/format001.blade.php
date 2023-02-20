<html>
<head>
</head>
<body style="max-width:800px;">
    <h1>Invoice</h1>
    @if (!empty($inv['logo'])) 
    <img src="{{ $inv['logobase64'] }}">
    <br><br>
     @endif
    <table style="width: 100%;">
        <td style="vertical-align:top;">
            <p>
            <strong><i>Client:</i></strong><br>
            <strong>{{ $inv['client_name'] }}</strong><br>
            {{ $inv['client_email'] }}<br>
            @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
            @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!} <br> @endif
            </p>
        </td>
        <td style="vertical-align:top;">
            <p>
                <strong><i>From:</i></strong><br>
                <strong>{{ $inv['invoice_name'] }}</strong><br>
                {{ $inv['invoice_email'] }}<br>
                @if (!empty($inv['invoice_phone'])) {{ $inv['invoice_phone'] }}<br> @endif
                {{ $inv['invoice_address'] }}<br>
            </p>
        </td>
        <td style="vertical-align:top;">
            <p>
                <b>Invoice Number:</b> {{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}<br>
                <b>Invoice Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_date'])) }}<br>
                @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }}<br> @endif 
            </p>
        </td>
    </table>
    
    <h3>Invoice details</h3>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th scope="col" style="min-width:50px">Item</th>
                <th scope="col" style="min-width:50px">Qty</th>
                <th scope="col" style="min-width:50px">Description</th>
                <th scope="col" style="min-width:50px">Unit Price</th>
                <th scope="col" style="min-width:50px">Tax Rate</th>
                <th scope="col" style="min-width:50px">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inv['inv_parts'] as $ip)
            <tr style="">
                <td style="">{{ $ip['invoice_parts_name'] }}</td>
                <td>{{ !empty($ip['invoice_parts_quantity']) ? $ip['invoice_parts_quantity'] : '-' }}</td>
                <td>{!! nl2br(e($ip['invoice_parts_description'])) !!}</td>
                <td style="">{{ !empty($ip['invoice_parts_unit_price']) ? $ip['invoice_parts_quantity'] : ""}}</td>
                <td style="">{{ $ip['invoice_parts_tax_rate'] }}</td>
                <td style="">{{ '$'.number_format($ip['invoice_parts_amount'], 2, ".", ",") }}</td>
            </tr>
            @endforeach

            <tr class="invoice-total--subamount" style='text-align:right'>
                <td colspan="2" rowspan="3"></td>
                <td>Subtotal (excl GST)</td>
                <td colspan="2">
               {{'$'.number_format($inv['invoice_sub_total'], 2, ".", ",") }}
                </td>
            </tr>

            <tr class="invoice-total--gst" style='text-align:right'>
                <td id="invoice_total_gst_text" >Total GST</td>
                <td colspan="2">
                {{'$'.number_format($inv['invoice_total_gst'], 2, ".", ",") }}
                </td>
            </tr>

            <tr class="invoice-total--amountdue" style='text-align:right'>
                <td><strong>Amount Due</strong></td>
                <td colspan="2">
                    <strong id="grandtotal"></strong>
                    {{'$'.number_format($inv['invoice_total_amount'], 2, ".", ",") }}

                </td>
            </tr>
        </tbody>
    </table>
   
    <br><br><br>
    <h4>@if (!empty($inv['invoice_due_date'])) Due Date: {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }}<br> @endif</h4>
    <div class="footer">{!! nl2br(e($inv['invoice_terms'])) !!}</div>
    @if (!empty($inv['pdfpreview']))
    <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
    @endif


</body>
</html>