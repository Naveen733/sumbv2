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
                            <form action="/profit-loss"  method="GET" enctype="multipart/form-data" id="search_form">
                                <div class="row">
                                    
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="">Start Date</label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="start_date" name="start_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($start_date) ? $start_date : '' }}">
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
                                                    <input type="text" id="end_date" name="end_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($end_date) ? $end_date : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-input--wrap" style="margin-top:35px">
                                            <button type="submit" name="search_profit_loss" class="btn sumb--btn" value="Search" >Search</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="sumb--recentlogdements sumb--putShadowbox">
                                    <div class="table-responsive">

                                        <table class="invoice_list">
                                            <thead>
                                                <tr>
                                                    <th colspan='1'></th>
                                                    @foreach($items as $item)
                                                        <th>
                                                            <h5>{{$item['start_date']}}</h5>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th colspan='2'><h5>Cost of sales</h5></th>
                                                </tr>
                                                <?php $total_cost_of_sales = 0;?>
                                                @if(!empty($items))
                                                            @foreach($items[0]['transactions'] as $transaction)

                                                                <?php if($transaction['transaction_type'] == 'invoice'){ ?>
                                                                    @foreach($transaction['accounts'] as $account)

                                                                        <?php $total_parts_amount = 0 ?>
                                                                        @foreach($account['particulars'] as $particular)
                                                                            <?php $total_parts_amount += $particular['parts_amount'];?>
                                                                        @endforeach

                                                                        <tr>
                                                                            <td>
                                                                                {{$account['chart_accounts_particulars_name']}}
                                                                            </td>
                                                                            <td>
                                                                                <a href="#" style="font-size: 13px;">{{number_format($total_parts_amount, 2)}} </a>
                                                                            </td>
                                                                       <?php  foreach($items as $item_k => $item){
                                                                            $item_found = false;
                                                                            if($item_k != 0){
                                                                                foreach($item['transactions'] as  $item_transaction){
                                                                                    if( $item_transaction['transaction_type'] == 'invoice'){
                                                                                        foreach($item_transaction['accounts'] as $item_account){
                                                                                            if($item_account['chart_accounts_particulars_name'] == $account['chart_accounts_particulars_name']){


                                                                                                $item_total_parts_amount = 0;
                                                                                                foreach($account['particulars'] as $particular){
                                                                                                    $item_total_parts_amount += $particular['parts_amount'];
                                                                                                }?>
                                                                                                <td>
                                                                                                    <a href="#" style="font-size: 13px;">{{number_format($item_total_parts_amount, 2)}} </a>
                                                                                                </td>

                                                                                            <?php }
                                                                                            $item_found = true;
                                                                                        }
                                                                                    }
                                                                                    // if($item_found) break;
                                                                                }
                                                                                
                                                                            }
                                                                        }?>

                                                                        
                                                                        </tr>

                                                                    @endforeach
                                                                <?php }?>
                                                            @endforeach

                                                            <tr>
                                                                <td><b>Total Cost of Sales</b> </td>
                                                                <td>
                                                                    <!-- <b>{{ !empty($total_profit_loss) && $total_profit_loss['total_cost_of_sale'] ? number_format($total_profit_loss['total_cost_of_sale'], 2) : '' }}</b>                                       -->
                                                                </td>
                                                            </tr>


                                                        <tr>
                                                            <th colspan='2'><h5>Operating Expenses</h5></th>
                                                        </tr>
                                                            @foreach($items[0]['transactions'] as $transaction)

                                                                <?php if($transaction['transaction_type'] == 'expense'){ ?>
                                                                    @foreach($transaction['accounts'] as $account)

                                                                        <?php $total_parts_amount = 0 ?>
                                                                        @foreach($account['particulars'] as $particular)
                                                                            <?php $total_parts_amount += $particular['parts_amount'];?>
                                                                        @endforeach

                                                                        <tr>
                                                                            <td>
                                                                                {{$account['chart_accounts_particulars_name']}}
                                                                            </td>
                                                                            <td>
                                                                                <a href="#" style="font-size: 13px;">{{number_format($total_parts_amount, 2)}} </a>
                                                                            </td>
                                                                       <?php  foreach($items as $item_k => $item){
                                                                        
                                                                            if($item_k != 0){
                                                                                foreach($item['transactions'] as  $item_transaction){
                                                                                    if( $item_transaction['transaction_type'] == 'expense'){
                                                                                        foreach($item_transaction['accounts'] as $item_account){
                                                                                            if($item_account['chart_accounts_particulars_name'] == $account['chart_accounts_particulars_name']){


                                                                                                $item_total_parts_amount = 0;
                                                                                                foreach($account['particulars'] as $particular){
                                                                                                    $item_total_parts_amount += $particular['parts_amount'];
                                                                                                }?>
                                                                                                <td>
                                                                                                    <a href="#" style="font-size: 13px;">{{number_format($item_total_parts_amount, 2)}} </a>
                                                                                                </td>

                                                                                            <?php }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }?>

                                                                        
                                                                        </tr>

                                                                    @endforeach
                                                                <?php }?>
                                                            @endforeach
                                                        
                                                    <tr>
                                                        <td><b>Total Operating Expenses</b> </td>
                                                        <td>
                                                            <!-- <b>{{ !empty($total_profit_loss) && $total_profit_loss['total_cost_of_sale'] ? number_format($total_profit_loss['total_cost_of_sale'], 2) : '' }}</b>                                       -->
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
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
    
    $(function() {
        $( "#start_date" ).datepicker();
        $( "#end_date" ).datepicker();
        
    });

</script>
<!-- end document-->