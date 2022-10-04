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
                        <h2 class="title-1">Expenses</h2>
                    </div>
                </div>
                <div class="col-md-12">
                    <p style="margin-bottom:20px;"><a href="/dashboard">Dashboard</a> | <a href="/invoice">Expenses & Invoice</a> | <b>Create An Expenses</b></p>
                </div>
            </div>
            <form action="/expenses-create-save" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="row">
                <div class="col-lg-12">
                    @isset($err) 
                    <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                        {{ $errors[$err][0] }}
                    </div>
                    @endisset
                <div class="card">
                        <div class="card-header">
                            <strong>Expenses</strong> Form
                        </div>
                        <div class="card-body card-block">
                            
                                @csrf
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class=" form-control-label">Name</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <p class="form-control-static">{{ str_pad($data['expenses_count'], 10, '0', STR_PAD_LEFT); }}</p>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="invoice_date" class=" form-control-label">Transaction Date (mm/dd/yyyy)</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <input type="text" id="invoice_date" name="invoice_date" placeholder="{{ date("m/d/Y") }}" class="form-control" readonly value="{{ date("m/d/Y") }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="client_name" class="form-control-label">Expenses Name</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <div class="input-group mb-3">
                                            <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Recipient's username" aria-label="Expenses Name" aria-describedby="button-addon2" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="button-addon2" data-toggle="modal" data-target="#staticBackdrop">Select Active Recipient</button>
                                            </div>
                                          </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="invoice_details" class=" form-control-label">Expenses Description (optional)</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <textarea name="invoice_details" id="invoice_details" rows="9" placeholder="Expenses Description" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label class=" form-control-label">Save</label>
                                    </div>
                                    <div class="col col-md-9">
                                        <div class="form-check">
                                            <div class="checkbox">
                                                <label for="savethisrep" class="form-check-label ">
                                                    <input type="checkbox" id="savethisrep" name="savethisrep" value="1" class="form-check-input" checked>Do you want to save this recipient to an active recipient?<br>
                                                    <small>Note: when the name is existing it will overide the old one.</small>
                                                </label>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3">
                                        <label for="amount" class=" form-control-label">Amount</label>
                                    </div>
                                    <div class="col-12 col-md-9">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text">$</span>
                                            </div>
                                            <input type="number" id="amount" name="amount" placeholder="0.00" class="form-control" required min="0" value="" step="0.01" pattern="^\d+(?:\.\d{1,2})?|0$">
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
                </div>
                
            </div>
            </form>
            
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
        $( "#invoice_date" ).datepicker();
        $('.dcc_click').on('click', function () {
            //console.log('clicked!');
            //console.log( $(this).data('myid') );
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
            var clientdesc = $("#data_desc_"+clientid).html();
            //console.log(clientdesc);
            $('#client_name').val(clientname);
            $('#invoice_details').val(clientdesc);
            
        });
    });
</script>
</body>

</html>
<!-- end document-->