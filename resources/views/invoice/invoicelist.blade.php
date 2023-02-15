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
                    <h3 class="sumb--title">Expenses</h3>
                </section>

                <section>
                    <div class="sumb--statistics row">
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
                        <div class="col-xl-6">
                            <h4 class="sumb--title2">My Expenses</h4>
                        </div>
                        <div class="col-xl-6 sumb--fileAddbtn">
                        <a class="fileAddbtn" href="/expense-create">New Expenses</a>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="row">
                        
                        <div class="col-xl-12">

                            @isset($err) 
                            <br>
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            <br>
                            @endisset
                            
                            <form action="/invoice"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Enter</label>
                                            <div class="form--inputbox ">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" id="search_number_name_amount" name="search_number_name_amount" placeholder="Exp No, Name, Amt"  value="{{!empty($search_number_name_amount) ? $search_number_name_amount : ''}}">
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
                                            <button type="button" name="search_invoice" class="btn sumb--btn" value="Search" onclick="searchItems(null, null)">Search</button>
                                            &nbsp; <span><b>or</b></span>&nbsp;
                                            <a href="#" onclick="clearSearchItems()" style="font-size: 12px;font-weight:bold">Clear</a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="sumb--recentlogdements sumb--putShadowbox">

                                <div class="table-responsive">
                                    <table class="expense_list">
                                        <thead>
                                            <tr>
                                                <th style="border-top-left-radius: 7px;" id="expense_date" onclick="searchItems('expense_date', '{{!empty($orderBy) && $orderBy == 'expense_date' ? $direction  : 'ASC'}}')"> Invoice date </th>
                                                <th id="expense_number" onclick="searchItems('expense_number', '{{!empty($orderBy) && $orderBy == 'expense_number' ? $direction  : 'ASC'}}')">Number</th>
                                                <th id="client_name" onclick="searchItems('client_name', '{{!empty($orderBy) && $orderBy == 'client_name' ? $direction  : 'ASC'}}')">Client</th>
                                                <th id="status_paid" onclick="searchItems('status_paid', '{{!empty($orderBy) && $orderBy == 'status_paid' ? $direction  : 'ASC'}}')">Status</th>
                                                <th id="total_amount" onclick="searchItems('total_amount', '{{!empty($orderBy) && $orderBy == 'total_amount' ? $direction  : 'ASC'}}')">Amount</th>
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">options</th>
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            @if (empty($expensedata['total']))
                                            <tr>
                                                <td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                            </tr>
                                            @else
                                                @foreach ($expensedata['data'] as $idat)
                                                @if($idat['inactive_status'] == 0)
                                                <tr>
                                                    <td>{{ date('d-m-Y', strtotime($idat['expense_date'])); }}</td>
                                                    <td>{{ str_pad($idat['expense_number'], 10, '0', STR_PAD_LEFT); }}</td>
                                                    <td>{{ $idat['client_name'] }}</td>
                                                    <!-- <td>@if (!empty($idat['client_email'])) <a href="mailto:{{ $idat['client_email'] }}">{{ $idat['client_email'] }}</a> @else &nbsp; @endif</td> -->
                                                    
                                                    <td class="@if ($idat['status_paid'] == 'void') sumb--recentlogdements__status_rej @elseif ($idat['status_paid'] == 'paid') sumb--recentlogdements__status_acc @else sumb--recentlogdements__status_proc @endif">{{ ucwords($idat['status_paid']) }}</td>
                                                    
                                                    <td style="text-align:right;">${{ number_format((float)$idat['total_amount'], 2, '.', ',') }}</td>
                                                    
                                                    <td class="sumb--recentlogdements__actions" style="text-align:right;">
                                                    
                                                    
                                                            @if($idat['status_paid'] == 'paid')
                                                            <div class="sumb--fileSharebtn dropdown">
                                                                <a href="{{ url('/expense/'.$idat['transaction_id'].'/view') }}"><i class="fa-solid fa-eye"></i></a>
                                                                <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                                                                
                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                <a class="dropdown-item" href="/status-change/?id={{ $idat['transaction_id'] }}&type=unpaid">Flag as Unpaid</a>    
                                                                <a class="dropdown-item" href="/expense-void/?id={{ $idat['transaction_id'] }}&type=void">Flag as Void</a>
                                                                </div>
                                                            </div>
                                                            @elseif($idat['status_paid'] == 'void')
                                                            <div class="sumb--fileSharebtn dropdown">
                                                                <a href="{{ url('/expense/'.$idat['transaction_id'].'/view') }}"><i class="fa-solid fa-eye"></i></a>
                                                                <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                                                                
                                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                    <a class="dropdown-item">Some options</a>
                                                                </div>
                                                            </div>
                                                            @else
                                                            <div class="sumb--fileSharebtn dropdown">
                                                                <a href="{{ url('/expense/'.$idat['transaction_id'].'/edit') }}"><i class="fa-solid fa-edit"></i></a>
                                                                <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                                                                
                                                                <div style="text-align: center;" class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                                    <a class="dropdown-item" href="/status-change/?id={{ $idat['transaction_id'] }}&type=paid">Flag as Paid</a>
                                                                    <a class="dropdown-item" href="/expense-void/?id={{ $idat['transaction_id'] }}&type=void">Flag as Void</a>
                                                                    <li value="{{ $idat['transaction_id'] }}" id="deleteExpense">
                                                                        Delete
                                                                    </li>
                                                                </div>
                                                            </div>    
                                                            @endif
                                                    </td>
                                                </tr>
                                                @endif
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
                                                        Display: {{$expensedata['per_page']}} Items
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

<div id="deleteExpenseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delete Expense</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this expense?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
        <button id="deleteExpenseConfirm" type="button" class="btn btn-danger btn-default" data-dismiss="modal">Delete</button>
      </div>
    </div>

  </div>
</div>

<!-- END PAGE CONTAINER-->


@include('includes.footer')
</body>

</html>

<script>
    //to delete selected expense
    var expenseID;
     $(document).on('click', '#deleteExpense', function(event) {
        expenseID = $(event.target).val();
        $("#deleteExpenseModal").modal('show');
    });

    
    $(document).on('click', '#deleteExpenseConfirm', function(event) {
    if(expenseID){
        var url = "{{ route('delete-expense', ':id') }}";
        url = url.replace(':id', expenseID);
        location.href = url;
    }else{
        alert("Select an expense to be deleted")
    }
       
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
        $("#search_number_name_amount").val('');
        $("#start_date").val('');
        $("#end_date").val('');
    }

    function searchItems(orderBy, direction){
        if(orderBy && direction){
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value='+orderBy+' >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value='+direction+' >');
        }else{
            $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value="expense_date" >');
            $("#search_form").append('<input id="direction" type="hidden" name="direction" value="ASC">');
        }
        $("#search_form").submit();
    }
</script>
<!-- end document-->