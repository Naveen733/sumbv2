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
                                        <a class="dropdown-item" href="/invoice-create">Add an Invoice</a>
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

                    <h4 class="sumb--title2">My Transactions</h4>

                    <div class="row">
                        
                        <div class="col-xl-12">

                            @isset($err) 
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            @endisset
                            <div class="sumb--recentlogdements sumb--putShadowbox">

                                <div class="table-responsive">
                                    <table>
                                        <thead>
                                            
                                            <tr>
                                                <th style="border-top-left-radius: 7px;">date</th>
                                                <th>Number</th>
                                                <th>Client</th>
                                                <th>Email</th>
                                                <th>type</th>
                                                <th>status</th>
                                                <th >amount</th>
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">options</th>
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            @if (empty($invoicedata['total']))
                                            <tr>
                                                <td colspan="8" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                            </tr>
                                            @else
                                                @foreach ($invoicedata['data'] as $idat)
                                            <tr>
                                                <td>{{ date('d-m-Y', strtotime($idat['updated_at'])); }}</td>
                                                <td>{{ str_pad($idat['transaction_id'], 10, '0', STR_PAD_LEFT); }}</td>
                                                <td>{{ $idat['client_name'] }}</td>
                                                <td>@if (!empty($idat['client_email'])) <a href="mailto:{{ $idat['client_email'] }}">{{ $idat['client_email'] }}</a> @else &nbsp; @endif</td>
                                                <td>{{ ucwords($idat['transaction_type']); }}</td>
                                                <td class="@if ($idat['status_paid'] == 'void') sumb--recentlogdements__status_rej @elseif ($idat['status_paid'] == 'paid') sumb--recentlogdements__status_acc @else sumb--recentlogdements__status_proc @endif">{{ ucwords($idat['status_paid']) }}</td>
                                                <td style="text-align:right;">${{ number_format((float)$idat['amount'], 2, '.', ',') }}</td>
                                                <td class="sumb--recentlogdements__actions" style="text-align:right;">
                                                    @if ($idat['transaction_type'] == 'invoice')
                                                    @if ($idat['status_paid'] == 'unpaid') <a href="/pdf/{{ $idat['invoice_pdf'] }}" target="_blank" title="Send to client" alt="Send to client"><i class="fa-solid fa-envelope-circle-check"></i></a> @endif
                                                    <a href="/pdf/{{ $idat['invoice_pdf'] }}" target="_blank" title="View PDF" alt="View PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                                    @endif
                                                    
                                                    <div class="sumb--fileSharebtn dropdown">
                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-gear"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                            @if ($idat['status_paid'] != 'paid') <a class="dropdown-item" href="/status-change/?tno={{ $idat['transaction_id'] }}&type={{ $idat['transaction_type'] }}&to=paid">Flag as PAID</a> @endif
                                                            @if ($idat['status_paid'] != 'unpaid') <a class="dropdown-item" href="/status-change/?tno={{ $idat['transaction_id'] }}&type={{ $idat['transaction_type'] }}&to=unpaid">Flag as UNPAID</a> @endif
                                                            @if ($idat['status_paid'] != 'void') <a class="dropdown-item" href="/status-change/?tno={{ $idat['transaction_id'] }}&type={{ $idat['transaction_type'] }}&to=void">Flag as VOID</a> @endif
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
<!-- end document-->