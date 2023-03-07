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
                    <h3 class="sumb--title">Profit and Loss</h3>
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
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="date('m/d/Y')"  readonly value="">
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
                                                    <input type="text" id="end_date" name="end_date" placeholder="date('m/d/Y')"  readonly value="">
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
                            <!-- <div> <h3>Profit and Loss</h3></div> -->
                            <div class="sumb--recentlogdements sumb--putShadowbox">
                                
                                <div class="table-responsive">
                                    <table class="invoice_list">
                                        <thead>
                                            <tr>
                                                <h5>Cost of Sales</h5>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total_cost_of_sales = 0;?>
                                            @foreach($invoice_list as $list)
                                            <tr>
                                                <td>
                                                   {{$list['chart_accounts_particulars_name']}} 
                                                </td>
                                                <?php
                                                    $total = 0;
                                                    foreach($list['particulars'] as $parts) {
                                                        $total += $parts['invoice_parts_amount'];
                                                        $total_cost_of_sales += $parts['invoice_parts_amount'];
                                                    }
                                                    ?>
                                                <td>
                                                    {{$total}}     
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td><b>Total Cost of Sales</b> </td>
                                                <td>
                                                    {{$total_cost_of_sales}}                                         
                                                </td>
                                            </tr>
                                            
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