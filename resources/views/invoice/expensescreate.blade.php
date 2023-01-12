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
                    <h3 class="sumb--title">New Expense</h3>
                </section>
                <hr class="form-cutter">
                <section>
                <form action="/expenses-create-save" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                           
                            @isset($err) 
                                <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                                    {{ $errors[$err][0] }}
                                </div>
                                @endisset

                                @csrf
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question">Expense Number <span>Read-Only</span></label>
                                            <div class="form--inputbox readOnly row">
                                                <div class="col-12">
                                                    <input type="text" readonly="" value="{{ str_pad($data['expenses_count'], 10, '0', STR_PAD_LEFT); }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="expense_date">Transaction Date <span>MM/DD/YYYY</span></label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_date" name="expense_date" placeholder="{{ date("m/d/Y") }}" class="form-control" value="{{ date("m/d/Y") }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="partstable">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="width:110px; min-width:110px;">Recepient Name</th>
                                                    <th scope="col" style="width:125px; min-width:125px;">Description</th>
                                                    <th scope="col" style="width:60px; min-width:60px;">Tax Rate</th>
                                                    <th scope="col" style="width:60px; min-width:60px;">Amount</th>
                                                    <th scope="col" style="width:40px; min-width:40px;">&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($particulars))
                                                    <tr>
                                                        <td>
                                                           <input type="text" id="client_name" name="client_name[]"  autocomplete="off" required value="{{ !empty($form['client_name']) ? $form['client_name'] : '' }}">
                                                        </td>
                                                        <td>
                                                            <textarea name="expense_description[]" id="expense_description" class="autoresizing"></textarea>
                                                        </td>
                                                        <td>
                                                            <input id="expense_tax" name="expense_tax[]" type="number">
                                                        </td>
                                                        <td>
                                                            <input id="expense_amount" name="expense_amount[]" type="number">
                                                        </td>
                                                        <td class="tableOptions">
                                                            <button class="btn sumb-del-btn delepart" type="button" ><i class="fa-solid fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @else
                                                    @foreach ($particulars as $prts)
                                                    <tr id="part_{{ $prts['id'] }}" data-type="{{ $prts['part_type'] }}">
                                                        <!-- <td scope="row" id="part_qty_{{ $prts['id'] }}">{{ ($prts['unit_price']<1 ? '-' : $prts['unit_price']) }}</td> -->
                                                        <td id="part_desc_{{ $prts['id'] }}" style="text-align: left">{{nl2br($prts['description'])}}</td>
                                                        <td id="part_uprice_{{ $prts['id'] }}" data-amt="{{ $prts['unit_price'] }}">{{($prts['unit_price']<1 ? '-' : '$'.number_format($prts['unit_price'], 2, ".", ","))}}</td>
                                                        <td id="part_amount_{{ $prts['id'] }}" data-amt="{{ $prts['amount'] }}">{{'$'.number_format($prts['amount'], 2, ".", ",")}}</td>
                                                        <td>
                                                            <button class="btn sumb--btn editpart" type="button" data-partid="{{ $prts['id'] }}" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> 
                                                            <button class="btn sumb--btn delepart" type="button" data-partid="{{ $prts['id'] }}"><i class="fa-solid fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                
                                                    @endforeach
                                                @endif
                                                
                                                <tr class="add--new-line">
                                                    <td colspan="5">
                                                        <button class="btn sumb--btn" type="button" id="addnewline"><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                                    </td>
                                                </tr>
                                                
                                                <tr class="invoice-separator">
                                                    <td colspan="5">hs</td>
                                                </tr>

                                                <tr class="invoice-total--subamount">
                                                    <td colspan="2" rowspan="3"></td>
                                                    <td>Subtotal<span>
                                                        <select style="border: none;" name="" id="">
                                                            <option value="">Incl. Tax</option>
                                                            <option value="">Excl. Tax</option>
                                                        </select> </span>
                                                    </td>
                                                    
                                                    <td  colspan="2">
                                                    <input readonly id="expense_total_amount" name="expense_total_amount" type="number">
                                                    </td>
                                                </tr>

                                                <tr class="invoice-total--gst">
                                                    <td>Total GST</td>
                                                    <td colspan="2">
                                                    <input readonly type="hidden" name="total_gst" id="total_gst" value="0">
                                                    </td>
                                                </tr>

                                                <tr class="invoice-total--amountdue">
                                                    <td><strong>Total</strong></td>
                                                    <td colspan="2">
                                                        <strong id="grandtotal"></strong>
                                                        <input type="hidden" name="gtotal" id="gtotal" value="">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                        </div>
                        
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <table id="file-upload">
                            <tbody>
                                <td></td>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    <div style="padding:2rem 0rem;" class="row">
                            
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                            <a href="/invoice" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                        </div> 
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                            <button style="float: right;" type="submit" class="btn sumb--btn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                            <button style="float: right;" type="submit" class="btn sumb--btn preview--btn"><i class="fa-solid fa-eye"></i> Preview</button>
                            <button style="float: right;" type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i> Clear Invioce</button>
                        </div>
                            
                    </div>
                    </form>
                </section>
           

               
                
                
            </div>
        </div>
    </div>
    </div>

    </div>


<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Saved Recipients</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="replist" style="overflow-x: auto; max-height:600px;">
                    
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Options</th>
    
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($exp_clients))
                            <tr>
                                <td colspan="3">You dont have any client recipient at this time</td>
                            </tr>
                            @endif
                            @php $counter = 0; @endphp
                            @foreach ($exp_clients as $ec)
                            @php $counter ++; @endphp
                            <tr>
                                <th scope="row" id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</th>
                                <td id="data_desc_{{ $counter }}">{{ $ec['client_description'] }}</td>
                                <td><button type="button" class="btn btn-primary btn-sm dcc_click" data-dismiss="modal" data-myid="{{ $counter }}">Use This</button></td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
  

<!-- END PAGE CONTAINER-->


@include('includes.footer')

<script>
    $(function() {
        $( "#expense_date" ).datepicker();
        $('.dcc_click').on('click', function () {
            //console.log('clicked!');
            //console.log( $(this).data('myid') );
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
            var clientdesc = $("#data_desc_"+clientid).html();
            //console.log(clientdesc);
            $('#client_name').val(clientname);
            $('#invoice_details').val(clientdesc);
            $('.form--recentsearch').hide();
        });



        //hide search by default

        $('.form--recentsearch').hide();
        $('li.add--newactclnt').hide();

        //Client Name Search

        $('#client_name').on('keyup', function() {

            $('.form--recentsearch.clientname').show();
            var value = $(this).val().toLowerCase();
            var clientList = $(".clientname .form--recentsearch__result li button");
            var matchedItems = $(".clientname .form--recentsearch__result li button").filter(function() {
                return $(this).text().toLowerCase().indexOf(value) > -1;              
            });
            
            if(value == ''){
                $('.form--recentsearch.clientname').hide();
                $('.clientname li.add--newactclnt input').prop('checked',false);
                $('#client_name').removeClass('saveNewRecord');

            } else if($('#client_name').hasClass('saveNewRecord')) {

                if(matchedItems.length !=0) {
                    $('.clientname li.add--newactclnt').hide();
                    $('.clientname li.add--newactclnt input').prop('checked',false);
                    $('#client_name').removeClass('saveNewRecord');
                    matchedItems.toggle(true);
                } else {
                    $('.form--recentsearch.clientname').hide();
                }

            } else {

                clientList.toggle(false);
                matchedItems.toggle(true);

                if (matchedItems.length == 0) {
                    $('.clientname li.add--newactclnt').show();
                } else {
                    $('.clientname li.add--newactclnt input').prop('checked',false);
                    $('.clientname li.add--newactclnt').hide();
                }
            }

            
        });

        //New Record -- Add New Icon

        $('li.add--newactclnt input').on('click', function () {

            if(this.id == 'savethisrep') {
                if($('#savethisrep').is(':checked')){
                    $('#client_name').addClass('saveNewRecord');
                } else {
                    $('#client_name').removeClass('saveNewRecord');
                }
                $('.form--recentsearch.clientname').hide();

            } else {
                if($('#save_invdet').is(':checked')){
                    $('#invoice_name').addClass('saveNewRecord');
                } else {
                    $('#invoice_name').removeClass('saveNewRecord');
                }
                $('.form--recentsearch.invoicedeets').hide();
            }

        });
    });

     //Add new row on Table Particulars

     $('#addnewline').on('click', function(){
            $('#partstable tr.add--new-line').before(
                '<tr><td><input type=\"text\" id=\"client_name\" name=\"client_name[]\"  autocomplete=\"off\" required value=\"\"></td>\n'+
                '<td><textarea name=\"expense_description[]\" id=\"expense_description\" class=\"autoresizing\"></textarea></td>\n'+
                '<td><input id=\"expense_tax\" name="expense_tax[]" type=\"number\"></td>\n'+
                '<td><input id=\"expense_amount\" name=\"expense_amount[]\" type=\"number\"></td>\n'+
                '<td class=\"tableOptions\">\n'+
                    '<button class=\"btn sumb-del-btn delepart\" type=\"button\" ><i class=\"fa-solid fa-trash\"></i></button>\n'+
                '</td>\n'+
                '</tr>');
        });

        $(document).on('click', '.delepart', function(){ 
            $(this).parents('tr').remove();

            var calculated_total_sum = 0;
                
            $("#partstable #expense_amount").each(function () {
                var get_textbox_value = $(this).val();
                if ($.isNumeric(get_textbox_value)) {
                    calculated_total_sum += parseFloat(get_textbox_value);
                    }                  
                });
            $("#expense_total_amount").val(calculated_total_sum);
        });

        $('#partstable').on('input', '.autoresizing', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        //Add tottal Amount
        $(document).ready(function () {
       
            $("#partstable").on('input', '#expense_amount', function () {
                var calculated_total_sum = 0;
                
                $("#partstable #expense_amount").each(function () {
                    var get_textbox_value = $(this).val();
                    if ($.isNumeric(get_textbox_value)) {
                        calculated_total_sum += parseFloat(get_textbox_value);
                        }                  
                    });
                        $("#expense_total_amount").val(calculated_total_sum);
                });
        });

        //Auto compute Amount per line
        
        // $('#partstable').on('keyup','#expense_amount', function(){
        //     var totalAmount = $("#part_qty").val()*$(this).val();
        //     $("#part_amount").val(totalAmount);
        // });

        // $('#partstable').on('keyup','#part_qty', function(){
        //     var totalAmount = $("#part_uprice").val()*$(this).val();
        //     $("#part_amount").val(totalAmount);
        // });

        // public function store(Request $request)
        // {
        //     if(count($request->desc) > 0){
        //         foreach($request->desc as $key => $value){
        //             $data2 = array(
        //                 'description' => $request->desc[$key],
        //                 'amount' => $request->txtAmt[$key]
        //             );
        //         }
        //     }
        //     dd($data2);
        // }
    
</script>
</body>

</html>
<!-- end document-->