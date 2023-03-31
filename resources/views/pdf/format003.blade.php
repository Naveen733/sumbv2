<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style>
      .invoice-header {
      background-color: #f8f9fa;
      padding: 20px;
      text-align: right;
    }
  </style>
</head>
<body>
    <div class="row">
      <div class="col-md-6">
        <div class="invoice-logo">
          <h3>Logo</h3>
        </div>
      </div>
      <div class="col-md-6">
        <div class="invoice-header">
          <h3>INVOICE</h3>
        </div>
      </div>
    </div>
    <table style="width: 100%;">
      <tr>
        <td style="vertical-align:top;">
            <p>
              <strong><i>Client:</i></strong><br>
              <strong>{{ $inv['client_name'] }}</strong><br>
                {{ $inv['client_email'] }}<br>
                @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
                @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!}  @endif
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
        <td style="vertical-align:top; ">
            <p>
            <b>Invoice Number:</b> {{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}<br>
            <b>Invoice Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_date'])) }}<br>
            @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }}<br> @endif
            </p>
        </td>
    </tr>
    </table>
    <div class="row">
      <div class="col-md-12">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Item</th>
              <th>Qty</th>
              <th>Description</th>
              <th>Unit price</th>
              <th>Tax rate</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($inv['inv_parts'] as $ip)
              <tr style="">
                  <td style="">{{ $ip['parts_name'] }}</td>
                  <td>{{ !empty($ip['parts_quantity']) ? $ip['parts_quantity'] : '-' }}</td>
                  <td>{!! nl2br(e($ip['parts_description'])) !!}</td>
                  <td style="">{{ !empty($ip['parts_unit_price']) ? $ip['parts_unit_price'] : ""}}</td>
                  <td style="">{{ $ip['invoice_tax_rates']['tax_rates'].'%' }}</td>
                  <td style="">{{ '$'.number_format($ip['parts_amount'], 2, ".", ",") }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6"></div>
      <div class="col-md-6">
        <table class="table table-bordered">
          <tbody>
            <tr>
              <td>Subtotal (excl GST)</td>
              <td style="text-align: right">{{'$'.number_format($inv['invoice_sub_total'], 2, ".", ",") }}</td>
            </tr>
            <tr>
              <td>Total GST</td>
              <td style="text-align: right">{{'$'.number_format($inv['invoice_total_gst'], 2, ".", ",") }}</td>
            </tr>
            <tr>
              <td><strong>Total:</strong></td>
              <td style="text-align: right"><strong>{{'$'.number_format($inv['invoice_total_amount'], 2, ".", ",") }}</strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <br><br><br>
    <h4>@if (!empty($inv['invoice_due_date'])) Due Date: {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }} @endif</h4>
    <div class="footer">{!! nl2br(e($inv['invoice_terms'])) !!}</div>
    @if (!empty($inv['pdfpreview']))
    <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
    @endif
</body>
</html>
