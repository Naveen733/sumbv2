@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title">Transactions</h3>
                </section>

                <section>
                    <div class="row" >
                        <div class="col-xl-12">
                            @isset($err) 
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            @endisset

                            @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    <ul>
                                        <li>{!! \Session::get('success') !!}</li>
                                    </ul>
                                </div>
                            @endif
                            <form action="/reports"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row" style="padding: 20px; 0px;">
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="report_start_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($report_start_date) ? $report_start_date : '' }}">
                                                </div>
                                            </div>
                                        <!-- <div class="form--inputbox date--picker">
                                            <div class="col-12">
                                                <input type="number"  class="form-control" id="start_date" name="start_date" placeholder="Start date"  value="">
                                            </div>
                                        </div> -->
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="report_end_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($report_end_date) ? $report_end_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                    <label class="form-input--question" for="">Accounts</label>
                                        <div class="input-group mb-3">
                                                <!-- <option selected value="">Choose...</option> -->
                                                <select class="custom-select form-control" id="testSelect1" name="report_chart_accounts_ids[]" id='testSelect1' multiple name=accounts[] >
                                                    @foreach($account_parts as $particulars)
                                                        <option <?php if(in_array($particulars['id'], $account_parts_code)) echo 'selected' ?> value="{{ $particulars['id'] }}">{{ $particulars['chart_accounts_particulars_code']}} - {{$particulars['chart_accounts_particulars_name'] }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                        <!-- <select class="selectpicker" multiple data-live-search="true" name="cat[]">
                                            <option value="php">PHP</option>
                                            <option value="react">React</option>
                                            <option value="jquery">JQuery</option>
                                            <option value="javascript">Javascript</option>
                                            <option value="angular">Angular</option>
                                            <option value="vue">Vue</option>
                                        </select> -->

                                        <!-- <div class="input-group mb-3">
                                            <select class="custom-select form-control" id="invoice_item_tax_rate" name="invoice_item_tax_rate" value="" required>
                                                <option selected value="">Choose...</option>
                                                @foreach($account_parts as $particulars)
                                                    <option value="{{!empty($particulars['chart_accounts_particulars_code']) ? $particulars['chart_accounts_particulars_code'] : '' }}" {{!empty($account_parts_code) && $account_parts_code == $particulars['chart_accounts_particulars_code'] ? 'selected' : '' }}>{{$particulars['chart_accounts_particulars_code']}} - {{$particulars['chart_accounts_particulars_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div> -->
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-input--wrap" style="margin-top:35px">
                                        <button type="submit" name="search_invoice" class="btn sumb--btn" value="Search">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- <div> <h3>Profit and Loss</h3></div> -->
                            <div class="row">
                                <div class="col-sm-6 col-md-6 col-xl-6">
                                    <h3 style="padding:10px">{{!empty($transaction_details)  ? $transaction_details[0]['chart_accounts_particulars_name'].' Transaction' : '' }}</h3>
                                </div>
                            </div>
                            <div class="sumb--recentlogdements sumb--putShadowbox">
                                <div class="table-responsive">
                                    <table class="">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" id="invoice_issue_date" > Date </th>
                                                <th id="invoice_number" >Description</th>
                                                <th id="client_name" >Source</th>
                                                <th id="client_email" >Debt</th>
                                                <th id="invoice_status" >Credit</th>
                                                <th id="invoice_total_amount" >Running Balance</th>
                                                <th>Gross</th>
                                                <th>Tax</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($transaction_details))
                                            @foreach($transaction_details as $transaction)
                                            <tr>
                                                <td style="color: #000000">
                                                    <h5>{{!empty($transaction['chart_accounts_particulars_name']) ? $transaction['chart_accounts_particulars_name'] .' Transaction' : ''}}</h5>
                                                </td>
                                                <!-- <div class="row"><div class="col-sm-6 col-md-6"></div></div> -->
                                            </tr>
                                            @foreach($transaction['particulars'] as $invoice_items)
                                            <tr>
                                                
                                                <td onclick="window.location='/invoice/{{$invoice_items['invoice']['primary_invoice_id']}}/edit'" target="_blank">
                                                    {{!empty($invoice_items['invoice']['invoice_issue_date']) ? $invoice_items['invoice']['invoice_issue_date'] : ''}}
                                                </td>
                                                <td onclick="window.location='/invoice/{{$invoice_items['invoice']['primary_invoice_id']}}/edit'" target="_blank">
                                                    {{!empty($invoice_items['invoice_parts_description']) ? $invoice_items['invoice_parts_description'] : ''}}
                                                </td>
                                                <td onclick="window.location='/invoice/{{$invoice_items['invoice']['primary_invoice_id']}}/edit'" target="_blank">
                                                    Receivable Invoice  
                                                </td>
                                                <td>
                                                -
                                                </td>
                                                <td onclick="window.location='/invoice/{{$invoice_items['invoice']['primary_invoice_id']}}/edit'" target="_blank">
                                                    {{!empty($invoice_items['invoice_parts_credit_amount']) ? number_format($invoice_items['invoice_parts_credit_amount'], 2)  : '-'}}
                                                </td>
                                                <td>
                                                -
                                                </td>
                                                <td>
                                                    {{!empty($invoice_items['invoice_parts_amount']) ? number_format($invoice_items['invoice_parts_amount'], 2) : '-'}}
                                                </td>
                                                <td>
                                                    {{!empty($invoice_items['invoice_gst']) ? number_format($invoice_items['invoice_gst'], 2) : '-'}}
                                                </td>
                                                <!-- <td>Total {{$transaction['chart_accounts_particulars_name']}}</td> -->
                                                
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td style="color: #000000"><b>Total {{!empty($transaction['chart_accounts_particulars_name']) ? $transaction['chart_accounts_particulars_name'] : ''}}</b></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="color: #000000"><b> {{!empty($transaction['total_credits_amount']) ? number_format($transaction['total_credits_amount'], 2) : ''}}</b></td>
                                                <td></td>
                                                <td style="color: #000000"><b> {{!empty($transaction['total_parts_amount']) ? number_format($transaction['total_parts_amount'], 2) : ''}}</b></td>
                                                <td style="color: #000000"><b> {{!empty($transaction['total_tax_amount']) ? number_format($transaction['total_tax_amount'], 2) : ''}}</b></td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    &nbsp;
                </section>
                
            </div>
        </div>
    </div>
</div>

<!-- END PAGE CONTAINER-->


@include('includes.footer')


<script>
$(document).ready(function(){

$('#testSelect1').multiselect({
  nonSelectedText: 'Select Framework',
  enableFiltering: true,
  enableCaseInsensitiveFiltering: true,
  buttonWidth:'400px'
});
});
$(function() {
    $( "#start_date" ).datepicker();
    $( "#end_date" ).datepicker();
});
</script>
<!-- end document-->