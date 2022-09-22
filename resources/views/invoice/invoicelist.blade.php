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

                            <div class="sumb--dashboardFileVault sumb--putShadowbox">
                                <div class="row">
                                    <div class="col-xl-8">
                                        <div class="path--bar">
                                            <p><i class="fa-solid fa-money-bill-transfer"></i> <a href="#">Invoices</a> | <a href="#">Expenses</a> | <a href="#">Adjustments</a></p>
                                        </div>
                                    </div>

                                    <div class="col-xl-4">
                                        <div class="sumb--fileAddbtn dropdown">
                                            <a class="fileAddbtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-circle-plus"></i>add invoice or expenses</a>
                
                                            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="mainlinkadd">
                                                <a class="dropdown-item" href="/invoice-create">Add an Invoice</a>
                                                <a class="dropdown-item" href="/expenses-create">Add an Expenses</a>
                                                <a class="dropdown-item" href="#">Add an Adjustment</a>
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

                                

                                <div class="table-responsive">
                                    <div style="margin-bottom:20px;">
                                        <!-- table pagination -->
                                          

                                                <div class="btn-group" role="group" aria-label="Basic example">

                                                    <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary"><i class="fas fa-angle-double-left"></i></a>
                                                    <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary"><i class="fas fa-angle-left"></i></a>
                                                    <a href="javascript:void(0)" type="button" class="btn btn-outline-secondary" >Page 1 of 1</a>
                                                    <a href="#" type="button" class="btn btn-outline-secondary"><i class="fas fa-angle-right"></i></a>
                                                    <a href="#" type="button" class="btn btn-outline-secondary"><i class="fas fa-angle-double-right"></i></a>

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

                                                    <div class="btn-group" role="group">
                                                        <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                            Display: 10 Items
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                            <a class="dropdown-item" href="#">5 Items</a>
                                                            <a class="dropdown-item" href="#">10 Items</a>
                                                            <a class="dropdown-item" href="#">25 Items</a>
                                                            <a class="dropdown-item" href="#">50 Items</a>
                                                            <a class="dropdown-item" href="#">100 Items</a>
                                                        </div>
                                                    </div>
                                                        
                                                        

                                                </div>

                                                    
                                    </div>
                                    
                                    <table>
                                        <thead>
                                            
                                            <tr>
                                                <th style="border-top-left-radius: 7px;">date</th>
                                                <th>Number</th>
                                                <th>Client</th>
                                                <th>type</th>
                                                <th>status</th>
                                                <th>amount</th>
                                                <th class="sumb--recentlogdements__actions" style="border-top-right-radius: 7px;">options</th>
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            @if (empty($invoicedata))
                                            <tr>
                                                <td colspan="7" style="padding: 30px 15px; text-align:center;">No Data At This time.</td>
                                            </tr>
                                            @else
                                                @foreach ($invoicedata as $idat)
                                            <tr>
                                                <td>{{ date('d-m-Y H:i', strtotime($idat['updated_at'])); }}</td>
                                                <td>{{ str_pad($idat['transaction_id'], 10, '0', STR_PAD_LEFT); }}</td>
                                                <td>{{ $idat['client_name'] }}</td>
                                                <td>{{ ucwords($idat['transaction_type']); }}</td>
                                                <td class="sumb--recentlogdements__status_acc">{{ ucwords($idat['status_paid']) }}</td>
                                                <td style="text-align:right;">${{ number_format((float)$idat['amount'], 2, '.', ',') }}</td>
                                                <td class="sumb--recentlogdements__actions" style="text-align:right;">
                                                    @if (!empty($idat['invoice_invoice']))
                                                    <a href="/pdf/{{ $idat['invoice_invoice'] }}" target="_blank"><i class="fa-solid fa-file-pdf"></i></a>
                                                    @endif
                                                    <div class="sumb--fileSharebtn dropdown">
                                                        <a class="fileSharebtn" href="#" role="button" id="mainlinkadd" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-share-nodes"></i></a>
                            
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="mainlinkadd">
                                                            <a class="dropdown-item" href="user-reg-company-form.php">Sharing Option 1</a>
                                                            <a class="dropdown-item" href="user-reg-abn-form.php">Sharing Option 2</a>
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