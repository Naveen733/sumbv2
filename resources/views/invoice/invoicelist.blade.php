@include('includes.head')
@include('includes.user-header')

<div class="modal fade" id="delete_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Delete Invoice</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this invoice <span id="delete_invoice_number"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="delete_invoice" value="">Delete</button>
      </div>
    </div>
  </div>
</div>

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">

                <section>
                    <h3 class="sumb--title">Invoice & Expenses</h3>
                </section>

                <section>
                    <div class="sumb--statistics row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="sumb--dashstatbox sumb--putShadowbox statistic__item--blue">
                                <div class="sumb-statistic__item invoce-expenses__stats">
                                    <h2>
                                        $1
                                    </h2>
                                    <span>Total Invoice Amount</span>
                                    <span>1 Paid Invoice</span>
                                    <span>2 Unpaid Invoice</span>
                                    <span>3 Void Invoice</span>
                                    <div class="icon">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="sumb--dashstatbox sumb--putShadowbox statbox__item--rejected">
                                <div class="sumb-statistic__item invoce-expenses__stats">
                                    <h2>
                                        $1
                                    </h2>
                                    <span>Total Expenses Amount </span>
                                    <span>1 Paid Expenses</span>
                                    <span>2 Unpaid Expenses</span>
                                    <span>3 Void Expenses</span>
                                    <div class="icon">
                                        <i class="fa-solid fa-check-to-slot"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="row">


                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">

                            <h4 class="sumb--title2">Add New Transaction</h4>
                            <div class="sumb--dashboardServices sumb--putShadowbox">

                                <div class="sumb--fileAddbtn dropdown">
                                    <a class="fileAddbtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-circle-plus"></i>add invoice or expenses</a>
                
                                    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="mainlinkadd">
                                        <a class="dropdown-item" href="/invoice/create">Add an Invoice</a>
                                        <a class="dropdown-item" href="/expenses-create">Add an Expenses</a>
                                        <!--<a class="dropdown-item" href="#">Add an Adjustment</a>-->
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <h4 class="sumb--title2">Filter My Transactions</h4>
                            <div class="sumb--dashboardServices sumb--putShadowbox">

                            <div class="btn-group sumb--dashboardDropdown" role="group">
                                <button id="btnGroupDrop_type" type="button" data-toggle="dropdown" aria-expanded="false">
                                    @if (app('request')->input('type') === 'invoice')
                                        Invoice
                                    @elseif (app('request')->input('type') === 'expenses')
                                        Expenses
                                    @else
                                         Invoice & Expenses
                                    @endif
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop_type">
                                    <a class="dropdown-item" href="{{$paging['starpage']}}&type=invoice">Invoice</a>
                                    <a class="dropdown-item" href="{{$paging['starpage']}}&type=expenses">Expenses</a>
                                    <a class="dropdown-item" href="/invoice">View All</a>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row">
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
                            <form action="/invoice"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Enter Number, Email, Amount</label>
                                            <div class="form--inputbox ">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" id="search_number_email_amount" name="search_number_email_amount" placeholder="Invoice no, Email, Amount"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}">
                                                    <!-- <input type="hidden" id="search_email" name="search_email"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}">
                                                    <input type="hidden" id="search_invoice_number" name="search_invoice_number"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}">
                                                    <input type="hidden" id="search_amount" name="search_amount"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}"> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($start_date) ? $start_date : ''}}">
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
                                                    <input type="text" id="end_date" name="end_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($end_date) ? $end_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap" style="margin-top:35px">
                                        <!-- <a href="/invoice?search_number_email_amount='0001' " onclick="clearSearchItems()" >Search</a> -->
                                        <button type="button" name="search_invoice" class="btn sumb--btn" value="Search" onclick="searchItems(null, null)">Search</button>
                                            &nbsp; <span><b>or</b></span>&nbsp;
                                            <a href="#" onclick="clearSearchItems()" style="font-size: 12px;font-weight:bold">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="sumb--recentlogdements sumb--putShadowbox">
                                <div class="table-responsive">
                                    <table class="invoice_list">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" id="invoice_issue_date" onclick="searchItems('invoice_issue_date', '{{!empty($orderBy) && $orderBy == 'invoice_issue_date' ? $direction  : 'ASC'}}')"> Invoice date </th>
                                                <th id="invoice_number" onclick="searchItems('invoice_number', '{{!empty($orderBy) && $orderBy == 'invoice_number' ? $direction  : 'ASC'}}')">Number</th>
                                                <th id="client_name" onclick="searchItems('client_name', '{{!empty($orderBy) && $orderBy == 'client_name' ? $direction  : 'ASC'}}')">Client</th>
                                                <th id="client_email" onclick="searchItems('client_email', '{{!empty($orderBy) && $orderBy == 'client_email' ? $direction  : 'ASC'}}')">Email</th>
                                                <th id="invoice_status" onclick="searchItems('invoice_status', '{{!empty($orderBy) && $orderBy == 'invoice_status' ? $direction  : 'ASC'}}')">status</th>
                                                <th id="invoice_total_amount" onclick="searchItems('invoice_total_amount', '{{!empty($orderBy) && $orderBy == 'invoice_total_amount' ? $direction  : 'ASC'}}')">Amount</th>
                                                <th>Sent</th>
                                                <!-- <th>Edit</th> -->
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (empty($invoicedata['total']))
                                            <tr>
                                                <td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                            </tr>
                                            @else
                                                @foreach ($invoicedata['data'] as $invoice)
                                            <tr>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'">{{ date('d-m-Y', strtotime($invoice['invoice_issue_date'])); }}</td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'">{{ 'INV-'. str_pad($invoice['invoice_number'], 6, '0', STR_PAD_LEFT); }}</td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'">{{ $invoice['client_name'] }}</td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'">@if (!empty($invoice['client_email'])) <a href="mailto:{{ $invoice['client_email'] }}">{{ $invoice['client_email'] }}</a> @else &nbsp; @endif</td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'"><b>{{$invoice['invoice_status']}}</b></td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'">${{ number_format((float)$invoice['invoice_total_amount'], 2, '.', ',') }}</td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'"><em style="margin-right: 3px;margin-left: 5px;color: #390;">{{ $invoice['invoice_sent'] ? 'Sent' : 'Unsent' }}</em></td>
                                                <!-- <td><a class="btn" href="/invoice/{{$invoice['id']}}/edit"><i class='far fa-edit'></i></a> <a class="btn" href="/invoice/{{$invoice['id']}}/edit"><i class='far fa-edit'></i></a></td> -->
                                                <td class="sumb--recentlogdements__actions" style="text-align:right;">
                                                    <div class="sumb--fileSharebtn dropdown">
                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                            @if($invoice['invoice_status'] == 'Paid')
                                                                <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Unpaid">Flag as UNPAID</a> 
                                                                <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Voided">Flag as VOID</a> 
                                                            @elseif($invoice['invoice_status'] == 'Unpaid')
                                                            <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Voided">Flag as VOID</a> 
                                                            <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Paid">Flag as PAID</a>
                                                            <p class="delete-invoice" onclick="deleteInvoice({{ str_pad($invoice['invoice_number'], 6, '0', STR_PAD_LEFT); }}, {{$invoice['id']}});" >Delete</p>
                                                            
                                                            <!-- <a class="dropdown-item" onclick="deleteInvoice({{ str_pad($invoice['invoice_number'], 6, '0', STR_PAD_LEFT); }}, {{$invoice['id']}});">Delete</a> -->

                                                            <!-- <span class="btn btn-primary" data-toggle="modal" data-target="#delete_invoice_modal" onclick="deleteInvoice({{ str_pad($invoice['invoice_number'], 6, '0', STR_PAD_LEFT); }});">Delete</span> -->
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <table>
                                    <tr class="sumb--recentlogdements__pagination">
                                        <td colspan="8">
                                            <!-- table pagination -->
                                            <div class="btn-group" role="group" aria-label="Basic example">

                                                <a href="{{ empty($paging['first']) ? 'javascript:void(0)' : $paging['first'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['first']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-left"></i></a>
                                                <a href="{{ empty($paging['prev']) ? 'javascript:void(0)' : $paging['prev'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['prev']) ? 'disabled' : '' }}" ><i class="fas fa-angle-left"></i></a>
                                                <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary" >Page {{$paging['now']}}</a>
                                                <a href="{{ empty($paging['next']) ? 'javascript:void(0)' : $paging['next'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['next']) ? 'disabled' : '' }}" ><i class="fas fa-angle-right"></i></a>
                                                <a href="{{ empty($paging['last']) ? 'javascript:void(0)' : $paging['last'] }}" type="button" class="btn btn-outline-secondary {{ empty($paging['last']) ? 'disabled' : '' }}"><i class="fas fa-angle-double-right"></i></a>
                                                <!--
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        Sort: Newest to Oldest
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a class="dropdown-item" href="#">Oldest To Newest</a>
                                                        <a class="dropdown-item" href="#">First Name Ascending</a>
                                                        <a class="dropdown-item" href="#">First Name Decending</a>
                                                        <a class="dropdown-item" href="#">Last Name Ascending</a>
                                                        <a class="dropdown-item" href="#">Last Name Decending</a>
                                                    </div>
                                                </div>
                                                -->
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                        Display: {{$invoicedata['per_page']}} Items
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=1' }}">1 Item</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=5' }}">5 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=10' }}">10 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=25' }}">25 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=50' }}">50 Items</a>
                                                        <a class="dropdown-item" href="{{ $paging['starpage'].'?page=1&ipp=100' }}">100 Items</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
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
</body>

</html>

<script>
    function deleteInvoice(invoice_number, id){
        console.log(invoice_number);
        
        $("#delete_invoice_number").text('');
        $("#delete_invoice_number").text(invoice_number);
        $("#delete_invoice").val('');
        $("#delete_invoice").val(id);
        
        $('#delete_invoice_modal').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });
    }

    $(document).on('click', '#delete_invoice', function(event) {
        var invoice_id = $("#delete_invoice").val();
        console.log(invoice_id);
        var url = "{{URL::to('/invoice/{id}/delete')}}";
        url = url.replace('{id}', invoice_id);
        location.href = url;
    });

    $(function() {
        $( "#start_date" ).datepicker();
        $( "#end_date" ).datepicker();
        
    });

    <?php if(!empty($orderBy)){?>
        <?php if($direction == 'ASC'){?> 
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-down"></i>');    
        <?php } if($direction == 'DESC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-up"></i>');    
        <?php }?> 
    <?php }?>

    function clearSearchItems(){
        $("#search_number_email_amount").val('');
        $("#start_date").val('');
        $("#end_date").val('');
    }

    function searchItems(orderBy, direction){
        if(orderBy && direction){
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value='+orderBy+' >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value='+direction+' >');
        }else{
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value="invoice_issue_date" >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value="ASC">');
        }

        $("#search_form").submit();
    }
</script>
<!-- end document-->