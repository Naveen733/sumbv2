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

                <section>

                    <form action="/expenses-create-save" method="post" enctype="multipart/form-data" class="form-horizontal">
                        @isset($err) 
                        <div class="sumb-alert alert alert-{{ $errors[$err][1] }}" role="alert">
                            {{ $errors[$err][0] }}
                        </div>
                        @endisset

                        @csrf

                        <hr class="form-cutter">

                        <div class="row">
                            <div class="col-xl-5">
                                <div class="form-input--wrap">
                                    <label class="form-input--question">Expense Number <span>Read-Only</span></label>
                                    <div class="form--inputbox readOnly row">
                                        <div class="col-12">
                                            <input type="text" readonly="" value="{{ str_pad($data['expenses_count'], 10, '0', STR_PAD_LEFT); }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xl-5">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="invoice_date">Transaction Date <span>MM/DD/YYYY</span></label>
                                    <div class="date--picker row">
                                        <div class="col-12">
                                            <input type="text" id="invoice_date" name="invoice_date" placeholder="{{ date("m/d/Y") }}" class="form-control" value="{{ date("m/d/Y") }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xl-5">
                                <div class="form-input--wrap">
                                    <label for="client_name" class="form-input--question">
                                        Recipient's Name
                                    </label>
                                    <div class="form--inputbox recentsearch--input row">
                                        <div class="searchRecords col-12">
                                            <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off" required value="{{ !empty($form['client_name']) ? $form['client_name'] : '' }}">
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
                                                        <label for="savethisrep">
                                                            <input type="checkbox" id="savethisrep" name="savethisrep" value="yes" class="form-check-input" {{ !empty($form['save_client']) ? 'checked' : '' }}>
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
                        </div>

                        <div class="row">

                            <div class="col-xl-5">
                                <div class="form-input--wrap">
                                    <label for="invoice_details" class="form-input--question">
                                        Expenses Description <span>optional</span>
                                    </label>
                                    <div class="form--textarea row">
                                        <div class="col-12">
                                            <textarea name="invoice_details" id="invoice_details" rows="9" placeholder="Expenses Description" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-xl-5">
                                <div class="form-input--wrap">
                                    <label for="amount" class="form-input--question">Amount</label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <div class="with--prepend">
                                                <span class="prepend--text">$</span>
                                                <input type="number" id="amount" name="amount" placeholder="0.00" class="form-control" required min="0" value="" step="0.01" pattern="^\d+(?:\.\d{1,2})?|0$">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

    
</script>
</body>

</html>
<!-- end document-->