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
                    <h3 class="sumb--title">Invoice</h3>
                </section>

                <section>
                    <div class="sumb--statistics row">


                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
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
                        

                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
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
                        <div class="col-xl-12">
                            @isset($err) 
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            @endisset

                            @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    {!! \Session::get('success') !!}
                                </div>
                            @endif

                            <form action="/invoice"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 order-xl-1">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Invoice No.</label>
                                            <div class="form--inputbox row">
                                                <div class="col-12">
                                                    <input type="text" id="search_number_email_amount" name="search_number_email_amount" placeholder="Invoice No., Email, Amount"  value="{{!empty($search_number_email_amount) ? $search_number_email_amount : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 order-xl-2">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="Date('MM/DD/YYYY')"  readonly value="{{!empty($start_date) ? $start_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 order-xl-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">End Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="end_date" name="end_date" placeholder="Date('MM/DD/YYYY')"  readonly value="{{!empty($end_date) ? $end_date : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-12 order-xl-4">

                                        <div class="btn-group sumb--dashboardDropdown transaction--filter" role="group">
                                            <button id="btnGroupDrop_type" type="button" data-toggle="dropdown" aria-expanded="false">
                                                @if (app('request')->input('type') === 'invoice')
                                                    Invoice
                                                @else
                                                    Filter My Transactions
                                                @endif
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop_type">
                                                <a class="dropdown-item" href="">Paid</a>
                                                <a class="dropdown-item" href="">Unpaid</a>
                                                <a class="dropdown-item" href="">Void</a>
                                                <a class="dropdown-item" href="/invoice">View All</a>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 order-xl-5 order-lg-1 order-md-1 order-sm-2 order-2">
                                        <a class="transaction--Addbtn" href="/invoice/create"><i class="fa-solid fa-circle-plus"></i>Add New Invoice</a>
                                    </div>
                                    
                                    <div class="invoice-list--btns col-xl-4 col-lg-6 col-md-6 col-sm-12 order-xl-6 order-lg-2 order-md-2 order-sm-1 order-1" style="text-align: right;">
                                        <button type="button" name="search_invoice" class="btn sumb--btn " value="Search" onclick="searchItems(null, null)"><i class="fa-solid fa-magnifying-glass"></i>Search</button>
                                        <button type="button" class="btn sumb--btn sumb-clear-btn" onclick="clearSearchItems()"><i class="fa-solid fa-circle-xmark"></i>Clear Search</button>
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
                                                <th id="client_email" onclick="searchItems('client_email', '{{!empty($orderBy) && $orderBy == 'client_email' ? $direction  : 'ASC'}}')">Client Email</th>
                                                <th id="invoice_status" onclick="searchItems('invoice_status', '{{!empty($orderBy) && $orderBy == 'invoice_status' ? $direction  : 'ASC'}}')">Status</th>
                                                <th id="invoice_total_amount" onclick="searchItems('invoice_total_amount', '{{!empty($orderBy) && $orderBy == 'invoice_total_amount' ? $direction  : 'ASC'}}')">Amount</th>
                                                <th>Email</th>
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
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'"><span class="payment--status-{{$invoice['invoice_status']}}">{{$invoice['invoice_status']}}</span></td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'">${{ number_format((float)$invoice['invoice_total_amount'], 2, '.', ',') }}</td>
                                                <td onclick="window.location='/invoice/{{$invoice['id']}}/edit'"><span class="{{ $invoice['invoice_sent'] ? 'email2client_sent' : 'email2client_pending' }}"></span></td>
                                                <!-- <td><a class="btn" href="/invoice/{{$invoice['id']}}/edit"><i class='far fa-edit'></i></a> <a class="btn" href="/invoice/{{$invoice['id']}}/edit"><i class='far fa-edit'></i></a></td> -->
                                                <td class="sumb--recentlogdements__actions" style="text-align:right;">
                                                    <div class="sumb--fileSharebtn dropdown <?php echo $invoice['invoice_status'] ?>">
                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                            @if($invoice['invoice_status'] == 'Paid')
                                                                <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Unpaid">Flag as UNPAID</a> 
                                                                <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Voided">Flag as VOID</a> 
                                                            @elseif($invoice['invoice_status'] == 'Unpaid')
                                                                <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Voided">Flag as VOID</a> 
                                                                <a class="dropdown-item" href="/status-change/?invoice_id={{ $invoice['id'] }}&status=Paid">Flag as PAID</a>
                                                                <a class="dropdown-item" onclick="deleteInvoice({{ str_pad($invoice['invoice_number'], 6, '0', STR_PAD_LEFT); }}, {{$invoice['id']}});">Delete</a>
                                                            
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
        return false;
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