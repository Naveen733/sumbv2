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
                    <h3 class="sumb--title">Transactions - Invoice & Expenses</h3>
                </section>

                <section>
                    <div class="row">
                        
                        <div class="col-xl-12">
                            <!--<pre>
                                {{ print_r($invoicedata) }}
                                {{ print_r($ourl) }}
                            </pre>-->
                            <div class="sumb--dashboardFileVault sumb--putShadowbox">
                                <div class="row">
                                    <div class="col-xl-8">
                                        <div class="path--bar">
                                            <p><i class="fa-solid fa-money-bill-transfer"></i> <a href="{{$paging['starpage']}}&type=invoice">Invoices</a> | <a href="{{$paging['starpage']}}&type=expenses">Expenses</a><!-- | <a href="#">Adjustments</a>--></p>
                                        </div>
                                    </div>

                                    <div class="col-xl-4">
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
                                
                            </div>

                            @isset($err) 
                            <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                {{ $errors[$err][0] }}
                            </div>
                            @endisset
                            <div class="sumb--recentlogdements sumb--putShadowbox">

                                <div style="margin-bottom:20px;">
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

                                                    
                                    </div>

                                <div class="table-responsive">
                                    
                                    
                                    <table>
                                        <thead>
                                            
                                            <tr>
                                                <th style="border-top-left-radius: 7px;width:75px;">date</th>
                                                <th style="width:75px;">Number</th>
                                                <th>Client</th>
                                                <th>Email</th>
                                                <th>type</th>
                                                <th>status</th>
                                                <th style="width:75px;">amount</th>
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px; width:150px;">options</th>
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            @if (empty($invoicedata['total']))
                                            <tr>
                                                <td colspan="7" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                            </tr>
                                            @else
                                                @foreach ($invoicedata['data'] as $idat)
                                            <tr>
                                                <td>{{ date('d-m-Y', strtotime($idat['updated_at'])); }}</td>
                                                <td>{{ str_pad($idat['transaction_id'], 10, '0', STR_PAD_LEFT); }}</td>
                                                <td>{{ $idat['client_name'] }}</td>
                                                <td>@if (!empty($idat['client_email'])) <a href="mailto:{{ $idat['client_email'] }}">{{ $idat['client_email'] }}</a> @else &nbsp; @endif</td>
                                                <td>{{ ucwords($idat['transaction_type']); }}</td>
                                                <td class="sumb--recentlogdements__status_acc @if ($idat['status_paid'] == 'void') void @elseif ($idat['status_paid'] == 'paid') paid @endif">{{ ucwords($idat['status_paid']) }}</td>
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
                                </div>
                        </div>
                    </div>
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