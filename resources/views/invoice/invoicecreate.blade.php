@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')
    
<!-- MAIN CONTENT-->
<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="overview-wrap" style="margin-bottom: 30px;">
                        <h2 class="title-1">Invoice</h2>
                    </div>
                </div>
                <div class="col-md-12">
                    <p style="margin-bottom:20px;"><a href="/dashboard">Dashboard</a> | <a href="/invoice">Transactions</a> | <b>Create An Invoice</b></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                <form action="/invoice-create-save" method="post" enctype="multipart/form-data" class="form-horizontal">    
                <div class="card">
                        <div class="card-header">
                            <strong>Invoice</strong> Form
                        </div>
                        <div class="card-body card-block">
                            @csrf
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class=" form-control-label">Invoice Number</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <p class="form-control-static">{{ str_pad($data['invoice_count'], 10, '0', STR_PAD_LEFT); }}</p>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="invoice_date" class=" form-control-label">Transaction Date (mm/dd/yyyy)</label>
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="text" id="invoice_date" name="invoice_date" placeholder="{{ date("m/d/Y") }}" class="form-control" readonly value="{{ date("m/d/Y") }}">
                                </div>
                                <div class="col col-md-3">
                                    <label for="invoice_duedate" class=" form-control-label">Due Date (optional)</label>
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="text" id="invoice_duedate" name="invoice_duedate" placeholder="" class="form-control" readonly value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom:20px;"><p><h4>Client Info</h4></p></div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="client_name" class="form-control-label">Client Name</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <div class="input-group mb-3">
                                        <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Client Name" aria-label="Client Name" aria-describedby="button-addon2" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="button-addon2" data-toggle="modal" data-target="#staticBackdrop">Select Active Clients</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="client_email" class=" form-control-label">Client Email</label>
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="email" id="client_email" name="client_email" placeholder="Client Email Address" class="form-control">
                                </div>
                            
                                <div class="col col-md-3">
                                    <label for="client_phone" class=" form-control-label">Client Phone</label>
                                </div>
                                <div class="col-12 col-md-3">
                                    <input type="text" id="client_phone" name="client_phone" placeholder="Client Contact Number" class="form-control">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="client_address" class=" form-control-label">Client Address (optional)</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <textarea name="client_address" id="client_address" rows="5" placeholder="Client Address" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label for="invoice_details" class=" form-control-label">Client Description (optional)</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    <textarea name="invoice_details" id="invoice_details" rows="5" placeholder="Client Description" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col col-md-3">
                                    <label class=" form-control-label">Save</label>
                                </div>
                                <div class="col col-md-9">
                                    <div class="form-check">
                                        <div class="checkbox">
                                            <label for="checkbox1" class="form-check-label ">
                                                <input type="checkbox" id="checkbox1" name="checkbox1" value="option1" class="form-check-input" checked>Do you want to save this client to an active client?
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row form-group" style="margin-top:30px; margin-bottom:30;">
                                <div class="col-md-6"><h4>Particulars</h4></div>
                                <div class="col-md-6" style="text-align: right;">
                                    <button class="btn btn-primary btn-sm" type="button" id="addnewpart" data-toggle="modal" data-target="#particulars">Add New</button> 
                                    <button class="btn btn-primary btn-sm" type="button" id="clearallpart" data-toggle="modal" data-target="#particulars">Clear All</button> 
                                </div>
                                <div class="col col-md-12" style="margin-top:20px;">
                                    <div class="table-responsive">
                                        <table class="table" id="partstable">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="width:75px; min-width:75px;">QTY</th>
                                                    <th scope="col">Description</th>
                                                    <th scope="col" style="width:150px; min-width:150px;">Unit Price</th>
                                                    <th scope="col" style="width:150px; min-width:150px;">Amount</th>
                                                    <th scope="col" style="text-align: right; width:150px; min-width:150px;">Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($particulars))
                                                <tr>
                                                    <td id="noparts" colspan="5" style="padding:20px;text-align:center;">You do not have any particulars at this time.</td>
                                                </tr>
                                                @else
                                                    @foreach ($particulars as $prts)
                                                    <tr id="part_{{ $prts['id'] }}" data-type="{{ $prts['part_type'] }}">
                                                        <td scope="row" id="part_qty_{{ $prts['id'] }}">{{ ($prts['unit_price']<1 ? '-' : $prts['unit_price']) }}</td>
                                                        <td id="part_desc_{{ $prts['id'] }}">{{nl2br($prts['description'])}}</td>
                                                        <td style="text-align: right;" id="part_uprice_{{ $prts['id'] }}" data-amt="{{ $prts['unit_price'] }}">{{($prts['unit_price']<1 ? '-' : '$'.number_format($prts['unit_price'], 2, ".", ","))}}</td>
                                                        <td style="text-align: right;" id="part_amount_{{ $prts['id'] }}" data-amt="{{ $prts['amount'] }}">{{'$'.number_format($prts['amount'], 2, ".", ",")}}</td>
                                                        <td style="text-align: right;">
                                                            <button class="btn btn-primary btn-sm editpart" type="button" data-partid="{{ $prts['id'] }}" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> 
                                                            <button class="btn btn-primary btn-sm delepart" type="button" data-partid="{{ $prts['id'] }}"><i class="fa-solid fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="3" style="text-align: right;"><strong>Total Amount</strong></td>
                                                    <td style="text-align: right;">
                                                    <strong id="grandtotal">${{number_format($gtotal, 2, ".", ",")}}</strong>
                                                    <input type="hidden" name="gtotal" id="gtotal" value="{{ $gtotal }}">
                                                    </td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom:20px;"><p><h4>Your Invoice Details</h4></p></div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col col-md-3">
                                            <label for="invoice_name" class=" form-control-label">Invoice Name From</label>
                                        </div>
                                        <div class="col-12 col-md-9">
                                            <div class="input-group mb-3">
                                                <input type="text" id="invoice_name" name="invoice_name" class="form-control" placeholder="Invoice Name" aria-label="Invoice Name" aria-describedby="button-addon2" required>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" id="invoice_name_addon2" data-toggle="modal" data-target="#staticBackdrop">Select Active Invoice Info</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col col-md-3">
                                            <label for="invoice_email" class=" form-control-label">Invoice Email</label>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <input type="text" id="invoice_email" name="invoice_email" placeholder="Invoice Email" class="form-control">
                                        </div>
                                        <div class="col col-md-3">
                                            <label for="invoice_phone" class=" form-control-label">Invoice Phone (optional)</label>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <input type="text" id="invoice_phone" name="invoice_phone" placeholder="Invoice Phone" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col col-md-3">
                                            <label for="invoice_terms" class=" form-control-label">Footer / Terms and Condition (optional)</label>
                                        </div>
                                        <div class="col-12 col-md-9">
                                            <textarea name="invoice_terms" id="invoice_terms" rows="5" placeholder="Footer / Terms and Condition" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col col-md-3">
                                            <label for="client_email" class=" form-control-label">Default Logo:</label><br>
                                            <button class="btn btn-primary btn-sm" type="button" id="change logo" data-toggle="modal" data-target="#logoModal">Change Logo</button> 
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <img src="/img/nologo.png" style="max-height:100px;">
                                            <input type="hidden" name="invoice_logo" id="invoice_logo" value="">
                                        </div>
                                        <div class="col col-md-3">
                                            <label for="client_email" class=" form-control-label">Template:</label><br>
                                            <button class="btn btn-primary btn-sm" type="button" id="change logo">Change Template</button> 
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <a href="/img/format001.pdf" target="_blank"><img src="/img/format001.jpg" style="max-height:100px;border:1px solid #000;"></a>
                                            <input type="hidden" name="invoice_format" id="invoice_format" value="format001">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col col-md-3">
                                            <label class=" form-control-label">Save</label>
                                        </div>
                                        <div class="col col-md-9">
                                            <div class="form-check">
                                                <div class="checkbox">
                                                    <label for="save_invdet" class="form-check-label ">
                                                        <input type="checkbox" id="save_invdet" name="save_invdet" value="invdet" class="form-check-input" checked>Do you want to save this details to an active details?
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            
                            
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-dot-circle-o"></i> Save</button>
                            <button type="reset" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Reset</button>
                            <a href="/invoice" class="btn btn-warning btn-sm"><i class="fa-solid fa-circle-left"></i> Back</a>
                        </div>
                    </div>
                    </form>
                </div>
                
            </div>
            
            
            <div class="row">
                <div class="col-md-12">
                    <div class="copyright">
                        <p>Copyright © 2022 SUM[B]. All rights reserved.</p>
                    </div>
                </div>
            </div>
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
                                <th scope="col">Description</th>
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
                                <td id="data_desc_{{ $counter }}">{{ $ec['client_description'] }}</td>
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
<!-- END PAGE CONTAINER-->


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
                                var myhtml = '<tr id="part_'+rparse.id+'" data-type="'+rparse.type+'"><td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'">'+rparse.desc+'</td><td style="text-align: right;" id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td style="text-align: right;" id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td style="text-align: right;"><button class="btn btn-primary btn-sm editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn btn-primary btn-sm delepart" type="button" data-partid="'+rparse.id+'"><i class="fa-solid fa-trash"></i></button></td></tr>';
                                $("#grandtotal").html('$'+rparse.grand_total);
                                $("#gtotal").html(rparse.grand_total);
                                $('#partstable tr:last').prev().after(myhtml);
                                $('#particulars').modal('toggle');
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
                                var myhtml = '<td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'">'+rparse.desc+'</td><td style="text-align: right;" id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td style="text-align: right;" id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td style="text-align: right;"><button class="btn btn-primary btn-sm editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn btn-primary btn-sm delepart" type="button" data-partid="'+rparse.id+'"><i class="fa-solid fa-trash"></i></button></td>';
                                $("#grandtotal").html('$'+rparse.grand_total);
                                $("#gtotal").html(rparse.grand_total);
                                $('#part_'+rparse.id).html(myhtml);
                                $('#particulars').modal('toggle');
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
                            $("#gtotal").html(rparse.grand_total);
                        }
                    }
                });
            }
        });
    });
    
</script>
</body>

</html>
<!-- end document-->