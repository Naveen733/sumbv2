<html>
  <head>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
      <style>
        .invoice-header {
          background-color: #f8f9fa;
          padding: 20px;
          text-align: right;
        }
        .invoice-info {
          background-color: #ffffff;
          padding: 20px;
          border: 1px solid #e1e1e1;
        }
        .invoice-details {
          background-color: #f8f9fa;
          padding: 20px;
          border: 1px solid #e1e1e1;
        }
        .invoice-footer {
          background-color: #ffffff;
          padding: 20px;
          text-align: right;
          border-top: 1px solid #e1e1e1;
        }
        .invoice-logo {
          margin-left: 10px;
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
        <td style="vertical-align:top;" class="invoice-info">
              <strong><i>Client:</i></strong><br>
              <strong>{{ $inv['client_name'] }}</strong><br>
                {{ $inv['client_email'] }}<br>
                @if (!empty($inv['client_phone'])) {{ $inv['client_phone'] }}<br> @endif
                @if (!empty($inv['client_address'])) {!! nl2br(e($inv['client_address'])) !!}  @endif
            
        </td>
        <td style="vertical-align:top;" class="invoice-info">
            <strong><i>From:</i></strong><br>
            <strong>{{ $inv['invoice_name'] }}</strong><br>
              {{ $inv['invoice_email'] }}<br>
              @if (!empty($inv['invoice_phone'])) {{ $inv['invoice_phone'] }}<br> @endif
              {{ $inv['invoice_address'] }}<br>
        </td>
        <td style="vertical-align:top;" class="invoice-info">
            <b>Invoice Number:</b> {{ 'INV-'.str_pad($inv['invoice_number'], 6, '0', STR_PAD_LEFT) }}<br>
            <b>Invoice Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_date'])) }}<br>
            @if (!empty($inv['invoice_due_date'])) <b>Due Date:</b> {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }}<br> @endif
        </td>
      </table>
      <br>
      <div class="row">
        <div class="col-12">
          <div class="invoice-details">
            <table class="table table-bordered">
              <thead>
                  <tr>
                  <th>Item</th>
                  <th>Description</th>
                  <th>Qty</th>
                  <th>Price</th>
                  <th>Tax Rate</th>
                  <th>Amount</th>
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
              </tbody>
            </table>
          </div>
        </div>
      </div>  
      
      <div class="row">
        <div class="col-12">
          <div class="invoice-details">
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
      </div>
      
      <br><br><br>
      <h4>@if (!empty($inv['invoice_due_date'])) Due Date: {{ date('m/d/Y', strtotime($inv['invoice_due_date'])) }} @endif</h4>
      <div class="footer">{!! nl2br(e($inv['invoice_terms'])) !!}</div>
      @if (!empty($inv['pdfpreview']))
      <small><a href="/pdf/{{$inv['invoice_pdf']}}" target="_blank">Invoice link: {{$inv['invoice_pdf']}}</a></small>
      @endif
  </body>
</html>