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
                    <h3 class="sumb--title">Create an Invoice</h3>
                </section>

                <section>

                    <form action="/invoice-create-save" method="post" enctype="multipart/form-data" class="form-horizontal">

                        @isset($err)
                        <div class="alert alert-{{ $errors[$err][1] }}" role="alert">
                            {{ $errors[$err][0] }}
                        </div>
                        @endisset


                        @csrf

                        <hr class="form-cutter">

                        <h4 class="form-header--title">Which client is this Invoice for?</h4>

                        <div class="row">

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_name" class="form-input--question">
                                        Client Name
                                    </label>
                                    <div class="form--inputbox recentsearch--input row">
                                        <div class="searchRecords col-12">
                                            <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" required value="{{ !empty($form['client_name']) ? $form['client_name'] : '' }}">
                                        </div>
                                    </div>
                                    <div class="form--recentsearch clientname row">
                                        <div class="col-12">
                                            
                                            <div class="form--recentsearch__result">
                                                <ul>
                                                    @if (empty($exp_clients))
                                                        <li>You dont have any clients at this time</li>
                                                    @else
                                                        @php $counter = 0; @endphp
                                                        @foreach ($exp_clients as $ec)
                                                            @php $counter ++; @endphp
                                                            <li>
                                                                <button type="button" class="dcc_click" data-myid="{{ $counter }}">
                                                                    <span id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</span>
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    @endif

                                                    <li class="add--newactclnt">
                                                        <label for="save_client">
                                                            <input type="checkbox" id="save_client" name="save_client" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
                                                            <div class="option--title">
                                                                Add as a new active client?
                                                                <span>Note: When the name is existing it will overide the old one.</span>
                                                            </div>
                                                        </label>
                                                    </li>
                                                </ul>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                
                            </div>


                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_email" class="form-input--question">
                                        Client Email
                                    </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="email" id="client_email" name="client_email" placeholder="Client Email Address" class="form-control" value="{{ !empty($form['client_email']) ? $form['client_email'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_phone" class="form-input--question">
                                        Client Contact Number
                                    </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="text" id="client_phone" name="client_phone" placeholder="Client Contact Number" class="form-control" value="{{ !empty($form['client_phone']) ? $form['client_phone'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr class="form-cutter">

                        <h4 class="form-header--title">Your Invoice Details</h4>

                        <div class="row">

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="invoice_date">Date Issued <span>MM/DD/YYYY</span></label>
                                    <div class="date--picker row">
                                        <div class="col-12">
                                            <input type="text" id="invoice_date" name="invoice_date" placeholder="{{ !empty($form['invoice_date']) ? $form['invoice_date'] : date('m/d/Y') }}"  readonly value="{{ !empty($form['invoice_date']) ? $form['invoice_date'] : date('m/d/Y') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="invoice_date">Due Date <span>Optional</span></label>
                                    <div class="date--picker row">
                                        <div class="col-12">
                                            <input type="text" id="invoice_duedate" name="invoice_duedate" placeholder="Due Date" readonly value="{{ !empty($form['invoice_duedate']) ? $form['invoice_duedate'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question">Invoice Number <span>Read-Only</span></label>
                                    <div class="form--inputbox readOnly row">
                                        <div class="col-12">
                                            <input type="text" readonly="" value="{{ str_pad($data['invoice_count'], 10, '0', STR_PAD_LEFT); }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                                
                        <hr class="form-cutter">

                        
                        <div class="table-responsive">
                            <table id="partstable">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:100px; min-width:100px;">QTY</th>
                                        <th scope="col" style="width:320px; min-width:320px;">Description</th>
                                        <th scope="col" style="width:120px; min-width:120px;">Unit Price</th>
                                        <th scope="col" style="width:120px; min-width:120px;">Amount</th>
                                        <th scope="col" style="width:20px; min-width:20px;">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (empty($particulars))
                                        <tr>
                                            <td>
                                                <input id="part_qty" type="number">
                                            </td>
                                            <td>
                                                <textarea class="autoresizing"></textarea>
                                            </td>
                                            <td>
                                                <input id="part_uprice" type="number">
                                            </td>
                                            <td>
                                                <input id="part_amount" type="number">
                                            </td>
                                            <td class="tableOptions">
                                                <button class="btn sumb--btn delepart" type="button" ><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($particulars as $prts)
                                        <tr id="part_{{ $prts['id'] }}" data-type="{{ $prts['part_type'] }}">
                                            <td scope="row" id="part_qty_{{ $prts['id'] }}">{{ ($prts['unit_price']<1 ? '-' : $prts['unit_price']) }}</td>
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
                                        <td>Subtotal (excl GST)</td>
                                        <td colspan="2">
                                            $0
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--gst">
                                        <td>Total GST</td>
                                        <td colspan="2">
                                            $0
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--amountdue">
                                        <td><strong>Amount Due</strong></td>
                                        <td colspan="2">
                                            <strong id="grandtotal">${{number_format($gtotal, 2, ".", ",")}}</strong>
                                            <input type="hidden" name="gtotal" id="gtotal" value="{{ $gtotal }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        

                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                    <a href="/invoice" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                                </div> 
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                    <button type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i> Clear Invioce</button>
                                    <button type="submit" class="btn sumb--btn preview--btn"><i class="fa-solid fa-eye"></i> Preview</button>
                                    <button type="submit" class="btn sumb--btn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                </div>
                            </div>
                        </div>

                        
                    </form>
                        
                </section>
                
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Saved Clients</h5>
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
                                <th scope="col">Email</th>
                                <th scope="col">Address</th>
                                <th scope="col">Options</th>
    
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($exp_clients))
                            <tr>
                                <td colspan="3">You dont have any clients at this time</td>
                            </tr>
                            @else
                            @php $counter = 0; @endphp
                            @foreach ($exp_clients as $ec)
                            @php $counter ++; @endphp
                            <tr>
                                <th scope="row" id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</th>
                                <td id="data_email_{{ $counter }}">{{ $ec['client_email'] }}</td>
                                <td id="data_address_{{ $counter }}">{{ $ec['client_address'] }}</td>
                                <td><button type="button" class="btn btn-primary btn-sm dcc_click" data-dismiss="modal" data-myid="{{ $counter }}">Use This</button></td>
                            </tr>
                            @endforeach
                            @endif
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

<div class="modal fade" id="invDetails" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="invDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invDetailsLabel">Saved Invoice Details</h5>
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
                                <th scope="col">Email</th>
                                <th scope="col">Address</th>
                                <th scope="col">Options</th>
    
                            </tr>
                        </thead>
                        <tbody>

                            @if (empty($exp_clients))
                            <tr>
                                <td colspan="3">You dont have any clients at this time</td>
                            </tr>
                            @else
                            @php $counter = 0; @endphp
                            @foreach ($exp_clients as $ec)
                            @php $counter ++; @endphp
                            <tr>
                                <th scope="row" id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</th>
                                <td id="data_email_{{ $counter }}">{{ $ec['client_email'] }}</td>
                                <td id="data_address_{{ $counter }}">{{ $ec['client_address'] }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm dcc_click" data-dismiss="modal" data-myid="{{ $counter }}">Use This</button>
                                    <input id="data_phone_{{ $counter }}" type="hidden" name="data_phone_{{ $counter }}" value="{{$ec['client_phone']}}"/>
                                    <input id="data_details_{{ $counter }}" type="hidden" name="data_details_{{ $counter }}" value="{{$ec['client_details']}}"/>
                                </td>
                            </tr>
                            @endforeach
                            @endif
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

<div class="modal fade" id="particulars" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="particularsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="particularsLabel">Particulars Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="partform">
            <div class="modal-body">
                <div id="partformblank" style="overflow-x: auto; max-height:600px;">
                    <div class="container">
                        <p style="margin-bottom:20px;"><strong>Note:</strong> <i>Please use the optional Quantity and Unit Price if you are dealing with goods. while if your using services just use the required Description and Amount.</i></p>
                        <input type="hidden" id="fprocess" name="process" value="a">
                        <input type="hidden" id="partid" name="partid" value="0">
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label for="part_qty" class=" form-control-label">Particular Type</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-warning btn-sm active" id="form_goods" onclick="prtg()">
                                      <input type="radio" name="partype" value="goods" id="form_radio_goods" checked> Goods
                                    </label>
                                    <label class="btn btn-warning btn-sm" id="form_services" onclick="prts()">
                                      <input type="radio" name="partype" value="services" id="form_radio_services"> Services
                                    </label>
                                  </div>
                            </div>
                        </div>
                        <div class="row form-group" id='pqty'>
                            <div class="col col-md-3">
                                <label for="part_qty" class=" form-control-label">Quantity (Optional)</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="number" id="part_qty" name="part_qty" min="0" placeholder="Quantity" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label for="part_desc" class=" form-control-label">Description</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <textarea name="part_desc" id="part_desc" rows="5" placeholder="Expenses Description" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="row form-group" id='puprice'>
                            <div class="col col-md-3">
                                <label for="part_uprice" class=" form-control-label">Price (Optional)</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="number" id="part_uprice" name="part_uprice" min="0" placeholder="Unit Price Per Quantity" class="form-control">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col col-md-3">
                                <label for="part_amount" class=" form-control-label">Amount</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <input type="number" id="part_amount" name="part_amount" min="0" placeholder="Total Amount" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button id="part_save_button" type="button" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="logoModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="logoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoModalLabel">Logo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="partform">
            <div class="modal-body">
                <iframe src="/invoice-logo-upload" style="width:100%;border:0px;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>


@include('includes.footer')

<script>
    function prtg() {
        $('#pqty').show();
        $('#puprice').show();
    }
    function prts() {
        $('#pqty').hide();
        $('#puprice').hide();
    }
    $(function() {
        $( "#invoice_date" ).datepicker();
        $( "#invoice_duedate" ).datepicker();
      
        $('#part_save_button').on('click', function () {

            var myform = $('#partform');
            if (document.querySelector('#partform').reportValidity())  {
                
                console.log($('#fprocess').val());
                var fprocess = $('#fprocess').val();
                
                var formdata2 = $("form#partform").serializeArray();
                
                if (fprocess == 'a') {
                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    $.ajax({
                        url: "{{ url('/invoice-particulars-add') }}",
                        method: 'post',
                        data: formdata2,
                        success: function(result){
                            var rparse = JSON.parse(result);
                            if(rparse.chk == 'success') {
                                var myhtml = '<tr id="part_'+rparse.id+'" data-type="'+rparse.type+'"><td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'" style="text-align: left;">'+rparse.desc+'</td><td id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td><button class="btn sumb--btn editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn sumb--btn delepart" type="button" data-partid="'+rparse.id+'"><i class="fa-solid fa-trash"></i></button></td></tr>';
                                $("#grandtotal").html('$'+rparse.grand_total);
                                $("#gtotal").val(rparse.grand_total);
                                $('#partstable tr:last').prev().after(myhtml);
                                $('#particulars').modal('toggle');
                                $('#tnoparts').hide();
                            }
                        }
                    });
                } else if (fprocess == 'e') {
                    console.log('edit section');
                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    $.ajax({
                        url: "{{ url('/invoice-particulars-add') }}",
                        method: 'post',
                        data: formdata2,
                        success: function(result){
                            var rparse = JSON.parse(result);
                            if(rparse.chk == 'success') {
                                var myhtml = '<td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'" style="text-align: left;">'+rparse.desc+'</td><td id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td ><button class="btn sumb--btn editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn sumb--btn delepart" type="button" data-partid="'+rparse.id+'"><i class="fa-solid fa-trash"></i></button></td>';
                                $("#grandtotal").html('$'+rparse.grand_total);
                                $("#gtotal").val(rparse.grand_total);
                                $('#part_'+rparse.id).html(myhtml);
                                $('#particulars').modal('toggle');
                                $('#tnoparts').hide();
                            }
                        }
                    });
                }
            } else {
                console.log("error!");
            }
        });
        $("#addnewpart").on('click', function () {
            $('#fprocess').val('a');
            $('#partid').val(0);
            $("#part_qty").val('');
            $("#part_desc").val('');
            $("#part_uprice").val('');
            $("#part_amount").val('');
            $("#form_goods").addClass("active"); $("#form_services").removeClass("active");
            $("#form_radio_goods").prop("checked", true); $("#form_radio_services").prop("checked", false);
            $('#pqty').show();
            $('#puprice').show();
        });
        
        $('#partstable').on('click', ".editpart", function () {
            var partid = $(this).data('partid');
            var type = $("#part_"+partid).data('type');
            var qty = $("#part_qty_"+partid).html();
            var desc = $("#part_desc_"+partid).html();
            var uprice = $("#part_uprice_"+partid).data('amt');
            var amount = $("#part_amount_"+partid).data('amt');
            $('#fprocess').val('e');
            $('#partid').val(partid);
            $("#part_qty").val(qty);
            $("#part_desc").val(desc);
            $("#part_uprice").val(uprice);
            $("#part_amount").val(amount);
            if (type == "goods") {
                $("#form_goods").addClass("active"); $("#form_services").removeClass("active");
                $("#form_radio_goods").prop("checked", true); $("#form_radio_services").prop("checked", false);
                $('#pqty').show();
                $('#puprice').show();
            } else {
                $("#form_services").addClass("active"); $("#form_goods").removeClass("active");
                $("#form_radio_services").prop("checked", true); $("#form_radio_goods").prop("checked", false);
                $('#pqty').hide();
                $('#puprice').hide();
            }
        });
        
        $('#partstable').on('click', ".delepart", function () {
            var partid = $(this).data('partid');
            var desc = $("#part_desc_"+partid).html();
            console.log(partid);
            if (confirm("Are you sure you want to delete this particulars:\n"+desc)) {
                console.log('delete');
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                $.ajax({
                    url: "{{ url('/invoice-particulars-delete') }}",
                    method: 'post', 
                    data: { 'partid' : partid },
                    success: function(result){
                        console.log(result);
                        var rparse = JSON.parse(result);
                        if(rparse.chk == 'success') {
                            console.log('delete now!');
                            $("#part_"+rparse.partid).remove();
                            $("#grandtotal").html('$'+rparse.gtotal);
                            $("#gtotal").val(rparse.grand_total);
                            if(!$("#gtotal").val()) {
                                $('#tnoparts').show();
                            }
                        }
                    }
                });
            }
        });
        
        $('.dcc_click').on('click', function () {
            //console.log('clicked!');
            //console.log( $(this).data('myid') );
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
            var clientdesc = $("#data_desc_"+clientid).html();
            //console.log(clientdesc);
            $('#client_name').val( $("#data_name_"+clientid).html() );
            $('#client_email').val( $("#data_email_"+clientid).html() );
            $('#client_phone').val( $("#data_phone_"+clientid).val() );
            $('#client_address').val( $("#data_address_"+clientid).html() );
            $('#invoice_details').val( $("#data_details_"+clientid).val() );
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

            if(this.id == 'save_client') {
                if($('#save_client').is(':checked')){
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


        //Add new row on Table Particulars

        $('#addnewline').on('click', function(){
            $('#partstable tr.add--new-line').before('<tr><td><input type=\"number\"></td><td><textarea class=\"autoresizing\"></textarea></td><td><input type=\"number\"></td><td><input type=\"number\"></td><td class=\"tableOptions\"><button class=\"btn sumb--btn delepart\" type=\"button\" ><i class=\"fa-solid fa-trash\"></i></button></td></tr>');
        });

        $('#partstable').on('input', '.autoresizing', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        //Auto compute Amount per line

        $('#partstable').on('keyup','#part_uprice', function(){
            var totalAmount = $("#part_qty").val()*$(this).val();
            $("#part_amount").val(totalAmount);
        });

        $('#partstable').on('keyup','#part_qty', function(){
            var totalAmount = $("#part_uprice").val()*$(this).val();
            $("#part_amount").val(totalAmount);
        });



    });

    
</script>
</body>

</html>
<!-- end document-->