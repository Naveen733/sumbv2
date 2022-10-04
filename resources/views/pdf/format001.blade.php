<html>
<head>
</head>
<body style="max-width:800px;">
    <h1>Invoice</h1>
    @if (!empty($inv['logo'])) <img src="{{ $inv['logobase64'] }}"><br><br> @endif
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
            </p>
        </td>
        <td style="vertical-align:top;">
            <p>
                <b>Invoice Number:</b> {{ str_pad($inv['transaction_id'], 10, '0', STR_PAD_LEFT) }}<br>
                <b>Invoice Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_date'])) }}<br>
                @if (!empty($inv['invoice_duedate'])) <b>Due Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_duedate'])) }}<br> @endif 
            </p>
        </td>
    </table>
    
    <h3>Invoice details</h3>
    <table style="width: 100%;">
        <thead>
            <tr>
                <th>QTY</th>
                <th>DESCRIPTION</th>
                <th>UNIT PRICE</th>
                <th>AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inv['inv_parts'] as $ip)
            <tr>
                <td>{{ !empty($ip['quantity']) ? $ip['quantity'] : '-' }}</td>
                <td>{!! nl2br(e($ip['description'])) !!}</td>
                <td style="text-align:right;">{{ !empty($ip['unit_price']) ? '$'.number_format($ip['quantity'], 2, ".", ",") : '-' }}</td>
                <td style="text-align:right;">{{ '$'.number_format($ip['amount'], 2, ".", ",") }}</td>
            </tr>
            @endforeach
            
            <tr>
                <td colspan="3" style="text-align:right;">Total Amount:</td>
                <td style="text-align:right;">{{$inv['amount']}}</td>
            </tr>
            
        </tbody>
    </table>
    <br><br><br>
    <div class="footer">{!! nl2br(e($inv['invoice_terms'])) !!}</div>
    @if (!empty($inv['pdfpreview']))
    <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
    @endif
</body>
</html>