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
                @if($type == 'create')
                <h3 class="sumb--title">New Expense</h3>
                @elseif($type == 'edit')
                <h3 class="sumb--title">Edit Expense ({{ $expense_details['expense_number'] }})</h3>
                @elseif($type == 'view')
                <h3 class="sumb--title">
                    Expense ({{ $expense_details['expense_number'] }})
                    <span class="expense--status-icon {{$expense_details['status_paid']}}">
                        @if(!empty($expense_details) && $expense_details['status_paid'] == 'void')   
                            Void
                        @elseif(!empty($expense_details) && $expense_details['status_paid'] == 'paid')
                            Paid
                        @endif
                    </span>
                </h3>
                    
                <div class="invoice--status-deets">This expense entry is on Read Only mode. Entries flagged as <u>{{$expense_details['status_paid']}}</u> cannot be edited.</div>
                @endif
            </section>

            <hr class="form-cutter">
            
            <section>
               
                @if($type == 'edit')
                    <form id="expense-form-edit" action="/expense/{{ $expense_details['id'] }}/update" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{ method_field('put') }}
                @elseif($type == 'create')
                    <form id="expense-form-create" action="/expense-save" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                @elseif($type == 'view')
                    <form id="expense-form-view" action="" method="" enctype="multipart/form-data">
                    {{ csrf_field() }}
                @endif
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="row">

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question">Expense Number <span>Read-Only</span></label>
                                            <div class="form--inputbox readOnly row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_number" name="expense_number" required readonly value="{{ !empty($expense_details['expense_number']) ? $expense_details['expense_number'] : 'EXP-'. str_pad($data['expenses_count'], 10, '0', STR_PAD_LEFT); }}">
                                                    @error('expense_number')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="expense_date">Date <span>MM/DD/YYYY</span></label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_date" name="expense_date" required class="form-control" value="{{ !empty($expense_details['expense_date']) ? date('m/d/Y', strtotime($expense_details['expense_date'])) :  date("m/d/Y")  }}">
                                                    @error('expense_date')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label class="form-input--question" for="expense_due_date">Due Date <span>MM/DD/YYYY</span></label>
                                            <div class="date--picker row">
                                                <div class="col-12">
                                                    <input type="text" id="expense_due_date" name="expense_due_date" required class="form-control" value="{{ !empty($expense_details['expense_due_date']) ? date('m/d/Y', strtotime($expense_details['expense_due_date'])) : '' }}" >
                                                    @error('expense_due_date')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-12">
                                        <div class="form-input--wrap">
                                            <label for="client_name" class="form-input--question">
                                                Recipient's Name
                                            </label>
                                            <div class="form--inputbox recentsearch--input row">
                                                <div class="searchRecords col-12">
                                                    <input type="text" id="client_name" name="client_name" required class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off"  value="{{ !empty($expense_details['client_name']) ? $expense_details['client_name'] : '' }}">
                                                    @error('client_name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
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
                                
                            </div>
                            <div class="col-xl-6">
                                <div class="row" style="height: 95.7%;">

                                    <div class="col-xl-12">

                                        <div class="sumb-expense-upload-container d-flex align-items-center justify-content-center">

                                            <div id="sumb-file-upload-container">
                                                
                                                <div class="sumb-expense-dropzone">
                                                    <i class="fa-solid fa-upload"></i>
                                                    <p>Upload an image</p>
                                                    <p class="muted">Drag & drop here or select your file manually</p>
                                                </div>

                                                <input id="file_upload" name="file_upload" accept="image/jpg,image/jpeg,image/png,application/pdf" type="file" class="sumb-expense-dropzone-input">

                                                @error('file_upload')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror

                                            </div>

                                            <div id="sumb-receipt-container">
                                                <!-- pdf upload  -->
                                                <iframe id="pdf-preview" src="{{ !empty($expense_details['file_upload']) ? asset($expense_details['file_upload']) : '' }}"></iframe>

                                                <div class="sumb-expense-receipt-actions d-flex">
                                                    <div role="presentation" data-ref="toggled-wrapper">
                                                        <button class="btn sumb--btn delepart deleFile" type="button" ><i class="fa-solid fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        
                                    </div>

                                </div>
                                
                            </div>
                        </div>


                        <div class="row expsenses--table">

                            <div class="col-xl-12">
                                <div class="table-responsive">
                                    <table id="partstable">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width:150px; min-width:150px;">Description</th>
                                                <th scope="col" style="width:20px; min-width:20px;">Qty</th>
                                                <th scope="col" style="width:20px; min-width:20px;">Unit Price</th>
                                                <th scope="col" style="width:140px; min-width:140px;">Account</th>
                                                <th scope="col" style="width:100px; min-width:100px;">Tax Rate</th>
                                                <th scope="col" style="width:40px; min-width:40px;">Amount</th>
                                                <th scope="col" style="width:40px; min-width:40px;">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @if (empty($expense_particulars)) 
                                                <tr>
                                                    <td>
                                                        <textarea name="expense_description[]" id="expense_description" step="any" class="autoresizing" required></textarea>
                                                    </td>
                                                    <td>
                                                        <input type="number" id="item_quantity" name="item_quantity[]" step="any"  required>
                                                    </td>
                                                    <td>
                                                        <input type="number" id="item_unit_price" name="item_unit_price[]" step="any"  required>
                                                    </td>
                                                    <td>

                                                        <div class="form-input--wrap">
                                                            <div class="row">
                                                                <div class="col-12 for--tables">
                                                                    <select class="form-input--dropdown" data-live-search="true" id="item_account" name="item_account[]" required>
                                                                        <option v>Select</option>
                                                                        <option value="400-Advertising">400 - Advertising</option>
                                                                        <option value="404-Bank Fees">404 - Bank Fees</option>
                                                                        <option value="408-Cleaning">408 - Cleaning</option>
                                                                        <option value="412-Consulting & Accounting">412 - Consulting & Accounting</option>
                                                                        <option value="420-Entertainment">420 - Entertainment</option>
                                                                        <option value="425-Freight & Courier">425 - Freight & Courier</option>
                                                                        <option value="429-General Expenses">429 - General Expenses</option>
                                                                        <option value="433-Insurance">433 - Insurance</option>
                                                                        <option value="437-Interest Expense">437 - Interest Expense</option>
                                                                        <option value="441-Legal expenses">441 - Legal expenses</option>
                                                                        <option value="445-Light, Power, Heating">445 - Light, Power, Heating</option>
                                                                        <option value="449-Motor Vehicle Expenses">449 - Motor Vehicle Expenses</option>
                                                                        <option value="453-Office Expenses">453 - Office Expenses</option>
                                                                        <option value="461-Printing & Stationery">461 - Printing & Stationery</option>
                                                                        <option value="469-Rent">469 - Rent</option>
                                                                        <option value="473-Repairs and Maintenance">473 - Repairs and Maintenance</option>
                                                                        <option value="485-Subscriptions">485 - Subscriptions</option>
                                                                        <option value="489-Telephone & Internet">489 - Telephone & Internet</option>
                                                                        <option value="493-Travel National">493 - Travel - National</option>
                                                                        <option value="494-Travel International">494 - Travel - International</option>
                                                                        <option value="710-Office Equipment">710 - Office Equipment</option>
                                                                        <option value="720-Computer Equipment">720 - Computer Equipment</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td>
                                                        <div class="form-input--wrap">
                                                            <div class="row">
                                                                <div class="col-12 for--tables">
                                                                    <select class="form-input--dropdown" name="expense_tax[]" id="expense_tax">
                                                                        <option value="0">Tax Exempt</option>
                                                                        <option value="10">General Tax</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input class="input--readonly" readonly id="expense_amount" name="expense_amount[]" type="number" step="any" required>
                                                    </td>
                                                    <td class="tableOptions">
                                                        <button class="btn sumb--btn delepart" type="button" ><i class="fa-solid fa-trash-alt"></i></button>
                                                    </td>
                                                </tr>
                                            @else
                                            <tr>
                                                @foreach ($expense_particulars as $prts)
                                                
                                                <td>
                                                    <textarea name="expense_description[]" id="expense_description" step="any" class="autoresizing" required>{{ !empty($prts['expense_description']) ? $prts['expense_description'] : '' }}</textarea>
                                                </td>
                                                
                                                <td>
                                                    <input type="number" id="item_quantity" name="item_quantity[]" value="{{ !empty($prts['item_quantity']) ? $prts['item_quantity'] : '' }}"  required>
                                                </td>
                                                <td>
                                                    <input type="number" id="item_unit_price" name="item_unit_price[]" value="{{ !empty($prts['item_unit_price']) ? $prts['item_unit_price'] : '' }}" step="any"  required>
                                                </td>
                                                <td>
                                                    <div class="form-input--wrap">
                                                        <div class="row">
                                                            <div class="col-12 for--tables">
                                                                <select class="form-input--dropdown" data-live-search="true" id="item_account" name="item_account[]" step="any" required>
                                                                    <option value="">Select</option>
                                                                    <option value="400-Advertising">400 - Advertising</option>
                                                                    <option value="404-Bank Fees">404 - Bank Fees</option>
                                                                    <option value="408-Cleaning">408 - Cleaning</option>
                                                                    <option value="412-Consulting & Accounting">412 - Consulting & Accounting</option>
                                                                    <option value="420-Entertainment">420 - Entertainment</option>
                                                                    <option value="425-Freight & Courier">425 - Freight & Courier</option>
                                                                    <option value="429-General Expenses">429 - General Expenses</option>
                                                                    <option value="433-Insurance">433 - Insurance</option>
                                                                    <option value="437-Interest Expense">437 - Interest Expense</option>
                                                                    <option value="441-Legal expenses">441 - Legal expenses</option>
                                                                    <option value="445-Light, Power, Heating">445 - Light, Power, Heating</option>
                                                                    <option value="449-Motor Vehicle Expenses">449 - Motor Vehicle Expenses</option>
                                                                    <option value="453-Office Expenses">453 - Office Expenses</option>
                                                                    <option value="461-Printing & Stationery">461 - Printing & Stationery</option>
                                                                    <option value="469-Rent">469 - Rent</option>
                                                                    <option value="473-Repairs and Maintenance">473 - Repairs and Maintenance</option>
                                                                    <option value="485-Subscriptions">485 - Subscriptions</option>
                                                                    <option value="489-Telephone & Internet">489 - Telephone & Internet</option>
                                                                    <option value="493-Travel National">493 - Travel - National</option>
                                                                    <option value="494-Travel International">494 - Travel - International</option>
                                                                    <option value="710-Office Equipment">710 - Office Equipment</option>
                                                                    <option value="720-Computer Equipment">720 - Computer Equipment</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-input--wrap">
                                                        <div class="row">
                                                            <div class="col-12 for--tables">
                                                                <select class="form-input--dropdown" name="expense_tax[]" id="expense_tax">
                                                                    <option <?php echo ($prts['expense_tax']) ==  '0' ? ' selected="selected"' : '';?>  value="0">Tax Exempt</option>
                                                                    <option <?php echo ($prts['expense_tax']) ==  '10' ? ' selected="selected"' : '';?> value="10">General Tax</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <input class="input--readonly" type="number" id="expense_amount" name="expense_amount[]" value="{{ !empty($prts['expense_amount']) ? $prts['expense_amount'] : '' }}"  required>
                                                </td>
                                                <td class="tableOptions">
                                                    <button class="btn sumb--btn delepart" type="button"><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                                @endforeach
                                            @endif
                                            
                                            <tr class="add--new-line">
                                                <td colspan="7">
                                                    <button class="btn sumb--btn" type="button" id="addnewline"><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                                </td>
                                            </tr>
                                            @error('expense_description.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('item_quantity.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('item_unit_price.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('expense_tax.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            @error('expense_amount.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                            
                                            <tr class="invoice-separator">
                                                <td colspan="7">&nbsp;</td>
                                            </tr>

                                            <tr class="expenses-tax--option">
                                                <td colspan="4">&nbsp;</td>
                                                <td>Tax Option</td>
                                                <td colspan="2">
                                                    <div class="form-input--wrap">
                                                        <div class="col-12 for--tables">
                                                            <select name="tax_type" id="tax_type" class="form-input--dropdown">
                                                                @if(empty($expense_details['tax_type']))
                                                                    <option value="0">Include Tax</option>
                                                                    <option value="1">Exclude Tax</option>
                                                                @else
                                                                    <option <?php echo ($expense_details['tax_type']) ==  '0' ? ' selected="selected"' : '';?> value="0">Include Tax</option>
                                                                    <option <?php echo ($expense_details['tax_type']) ==  '1' ? ' selected="selected"' : '';?> value="1">Exclude Tax</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr class="invoice-total--subamount">
                                                <td colspan="4" rowspan="3">
                                                    &nbsp;
                                                </td>
                                                <td>Subtotal</td>
                                                <td colspan="2">
                                                    <input readonly required id="expense_total_amount" step="any" name="expense_total_amount" type="number" value="{{ !empty($expense_details['expense_total_amount']) ? $expense_details['expense_total_amount'] : '' }}">
                                                    @error('expense_total_amount')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>

                                            <tr class="invoice-total--gst">
                                                <td>Total GST</td>
                                                <td colspan="2">
                                                    <input type="number" required readonly step="any" name="total_gst" id="total_gst" value="{{ !empty($expense_details['total_gst']) ? $expense_details['total_gst'] : '' }}">
                                                    @error('total_gst')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror    
                                                </td>
                                            </tr>

                                            <tr class="invoice-total--amountdue">
                                                <td><strong>Total</strong></td>
                                                <td colspan="2">
                                                    <strong id="grandtotal"></strong>
                                                    <input type="number" required readonly step="any" class="grandtotal" name="total_amount" id="total_amount" value="{{ !empty($expense_details['total_amount']) ? $expense_details['total_amount'] : '' }}">
                                                    @error('total_amount')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        
                    
                        </div>

                        

                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                <a href="/expense" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                                </div> 
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                    <button value="save_expense" name="save_expense" style="float: right;" type="submit" class="btn sumb--btn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                    <button style="float: right;" type="button" onclick="previewExpense()" class="btn sumb--btn preview--btn"><i class="fa-solid fa-eye"></i> Preview</button>
                                    <button style="float: right;" type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i> Clear Expense</button>
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


<!-- Expense preview model -->
<div class="modal fade bd-example-modal-lg modal-reskin" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog invoice--preview">
    <div class="modal-content">

        <div class="modal-header">
            <h5 class="modal-title invoiceprev--header" id="staticBackdropLabel">Expense Preview</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
            </button>
        </div>

        <!------- Expense Preview Modal ------>

        <div class="modal-body">
            <div class="container">

                <div class="container">
                    <center>
                        <h2 class="mb-4 mt-2">Expense Preview</h4>
                    </center>

                    <div class="invoicetable--header">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <ul class="list-unstyled">
                                    <li>To: <span id="expense_preview_to"></span></li>
                                    <li>Invoice number: <span id="expense_preview_expense_number"></span></li>
                                    <li>Issued: <span id="expense_preview_issue_date"></span></li>
                                    <li>Due: <span id="expense_preview_due_date"></span></li>
                                </ul>
                            </div>
                            <div class="col-xl-6 col-lg-6 invoicetable--header_from">
                                <ul class="list-unstyled">
                                    <li>From: <span id="expense_preview_from">{{$userinfo['1']}}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped" id="expense_preview_parts">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:320px; min-width:320px;">Description</th>
                                    <th scope="col" style="width:100px; min-width:100px;">QTY</th>
                                    <th scope="col" style="width:120px; min-width:120px;">Unit Price</th>
                                    <th scope="col" style="width:120px; min-width:120px;">Tax</th>
                                    <th scope="col" style="width:120px; min-width:120px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="expense_preview_parts_rows"></tbody>
                                
                        </table>
                    </div>

                    <div class="row mt-4 invoice--extrainfo">
                        <div class="col-xl-8 col-lg-8">
                            <p class="invoice--paymentnotes mb-1">
                                Add additional notes and payment information (e.g Bank Account)
                            </p>
                            <p class="invoice--paymentnotes">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque in maximus orci. Sed augue lectus, ultrices sit amet enim nec, commodo sodales lacus. Phasellus ultricies molestie eleifend. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam eu felis ante. Suspendisse sed ex sed felis semper elementum.
                            </p>
                        </div>
                        <div class="col-xl-4 col-lg-4">
                            <table class="table table-clear invoice--paymentinfo">
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Subtotal</strong>
                                        </td>
                                        <td class="center" id="expense_preview_sub_total"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Total Tax %</strong>
                                        </td>
                                        <td class="center" id="expense_preview_total_tax"></td>
                                    </tr>
                                    <tr class="invoice--paymentinfo-total_amount">
                                        <td>
                                            <strong>Total</strong>
                                        </td>
                                        <td class="center" id="expense_preview_total_amount"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    

                    
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
        </div>
    <!--------------END---------------------->
    </div>
  </div>
</div>

<!-- Modal -->
<!-- <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
</div> -->
  

<!-- END PAGE CONTAINER-->


@include('includes.footer')

<script>


    $(document).ready(function () {
        $(".bd-example-modal-lg").on("hidden.bs.modal", function () {
    // put your default event here
            $("#expense_preview_to").text($("#client_name").val());
                    $("#expense_preview_expense_number").text();
                    $("#expense_preview_issue_date").text();
                    $("#expense_preview_due_date").text();

                   // $("#expense_preview_parts_rows").last().remove();
                   $("#expense_preview_parts_rows").empty()

                    $("#expense_preview_sub_total").text();
                    $("#expense_preview_total_tax").text();
                    $("#expense_preview_total_amount").text();
        });
    });

     function previewExpense(){
        var to = $("#client_name").val();
        
             $("#expense_preview_to").text($("#client_name").val());
             $("#expense_preview_expense_number").text($("#expense_number").val());
             $("#expense_preview_issue_date").text($("#expense_date").val());
             $("#expense_preview_due_date").text($("#expense_due_date").val());

            expense_description_array = [];
            expense_item_quantity_array = [];
            expense_item_unit_price_array = [];
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_description[]"]').each(function() {
                expense_description_array.push(this.value);
            })
            $('[name="item_quantity[]"]').each(function() {
                expense_item_quantity_array.push(Number(this.value));
            })
            $('[name="item_unit_price[]"]').each(function() {
                expense_item_unit_price_array.push(Number(this.value));
            })
            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })
            
             $("#partstable #expense_description").each(function (index) {
                // console.log(expense_description_array[index]);
                // console.log(expense_item_quantity_array[index]);
                // console.log(expense_item_unit_price_array[index]);
                // console.log(expense_amount_array[index]);
                // console.log(expense_tax_array[index]);
                $("#expense_preview_parts_rows").append(
                     '<tr><td>'+expense_description_array[index]+'</td>\n'+
                     '<td>'+expense_item_quantity_array[index]+'</td>\n'+
                     '<td>'+expense_item_unit_price_array[index]+'</td>\n'+
                     '<td>'+expense_tax_array[index]+'</td>\n'+
                     '<td>'+expense_amount_array[index]+'</td>\n'+
                    '</tr>');
             });
             $("#expense_preview_sub_total").text($("#expense_total_amount").val());
             $("#expense_preview_total_tax").text($("#total_gst").val());
             $("#expense_preview_total_amount").text($("#total_amount").val());

            $(".bd-example-modal-lg").modal({
                show: true
            });
        }

   $(function() {
       // $("#expense_date").datepicker().datepicker('setDate', 'today');
        $("#expense_date").datepicker();
        $("#expense_due_date").datepicker();
        $('.dcc_click').on('click', function () {
            //console.log('clicked!');
            //console.log( $(this).data('myid') );
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
           // var clientdesc = $("#data_desc_"+clientid).html();
            //console.log(clientdesc);
            $('#client_name').val(clientname);
            //$('#invoice_details').val(clientdesc);
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
            '<tr><td><textarea name=\"expense_description[]\" id=\"expense_description\" class=\"autoresizing\" required></textarea></td>\n'+
            '<td><input type=\"number\" step="any" id=\"item_quantity\" name=\"item_quantity[]\" required \"></td>\n'+
            '<td><input type=\"number\" step="any" id=\"item_unit_price\" name=\"item_unit_price[]\" required \"></td>\n'+
            '<td><div class=\"form-input--wrap\"><div class=\"row\"><div class=\"col-12 for--tables\">'+
                '<select class="form-input--dropdown" data-live-search="true" id="item_account" name="item_account[]" step="any" required>\n'+
                    '<option value="">Select</option>\n'+
                    '<option value="400-Advertising">400 - Advertising</option>\n'+
                    '<option value="404-Bank Fees">404 - Bank Fees</option>\n'+
                    '<option value="408-Cleaning">408 - Cleaning</option>\n'+
                    '<option value="412-Consulting & Accounting">412 - Consulting & Accounting</option>\n'+
                    '<option value="420-Entertainment">420 - Entertainment</option>\n'+
                    '<option value="425-Freight & Courier">425 - Freight & Courier</option>\n'+
                    '<option value="429-General Expenses">429 - General Expenses</option>\n'+
                    '<option value="433-Insurance">433 - Insurance</option>\n'+
                    '<option value="437-Interest Expense">437 - Interest Expense</option>\n'+
                    '<option value="441-Legal expenses">441 - Legal expenses</option>\n'+
                    '<option value="445-Light, Power, Heating">445 - Light, Power, Heating</option>\n'+
                    '<option value="449-Motor Vehicle Expenses">449 - Motor Vehicle Expenses</option>\n'+
                    '<option value="453-Office Expenses">453 - Office Expenses</option>\n'+
                    '<option value="461-Printing & Stationery">461 - Printing & Stationery</option>\n'+
                    '<option value="469-Rent">469 - Rent</option>\n'+
                    '<option value="473-Repairs and Maintenance">473 - Repairs and Maintenance</option>\n'+
                    '<option value="485-Subscriptions">485 - Subscriptions</option>\n'+
                    '<option value="489-Telephone & Internet">489 - Telephone & Internet</option>\n'+
                    '<option value="493-Travel National">493 - Travel - National</option>\n'+
                    '<option value="494-Travel International">494 - Travel - International</option>\n'+
                    '<option value="710-Office Equipment">710 - Office Equipment</option>\n'+
                    '<option value="720-Computer Equipment">720 - Computer Equipment</option>\n'+
                '</select>\n'+
            '</div></div></div></td>'+
            '<td><div class=\"form-input--wrap\"><div class=\"row\"><div class=\"col-12 for--tables\"><select name=\"expense_tax[]\" id=\"expense_tax\" class=\"form-input--dropdown\" required><option value=\"0\">Tax Exempt</option><option value=\"10\">General Tax</option></select></div></div></div></td>\n'+
            '<td><input class=\"input--readonly\" readonly id=\"expense_amount\" name=\"expense_amount[]\" type=\"number\" step="any" required></td>\n'+
            '<td class=\"tableOptions\">\n'+
                '<button class=\"btn sumb--btn delepart\" type=\"button\" ><i class=\"fa-solid fa-trash-alt\"></i></button>\n'+
            '</td></tr>');
    });
        
    $('#partstable').on('input', '.autoresizing', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    
    $(document).ready(function () {
        //is status is void
        // if($("#status_paid").val() == 'void'){
        //      $("#expense-form-edit :input").prop('disabled', true); 
        // }

        //view page restrict editing for status void and paid
        $("#expense-form-view :input").prop('disabled', true); 

        //file upload hide show scenario handle
        if($('#pdf-preview').attr('src'))
        {
            $('#sumb-file-upload-container').hide();
        }else{
            $('#sumb-receipt-container').hide();
        }
        //file-upload
        $('#file_upload').on('change',function() {
            $('#sumb-file-upload-container').hide();
            
            const fileInput = document.getElementById('file_upload');
            const selectedFile = fileInput.files[0];
            const url = URL.createObjectURL( selectedFile );
            
            $('#pdf-preview').attr("src", url);
            $('#sumb-receipt-container').show();
        })
        //
        $('.deleFile').on('click',function(){
            // alert("s");
            $('#pdf-preview').attr("src", "");
            $('#sumb-receipt-container').hide();
            $('#file_upload').val("");
            $('#sumb-file-upload-container').show();
        })
                       
        //row total Amount,form total amoount, total tax
        var body = $('#partstable').children('tbody').first();
        body.on('change', 'input[type="number"]', function() {
            var cells = $(this).closest('tr').children('td');
            var value1 = cells.eq(1).find('input').val();
            var value2 = cells.eq(2).find('input').val();
            var value3 = cells.eq(5).find('input').val(value1 * value2);

            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($("#tax_type").val() == 0)
            {
                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount").val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(Number(total_amount.toFixed(2)));
            }
        });

        body.on('change', $("#expense_tax"), function() {
            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($("#tax_type").val() == 0)
            {
                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount").val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(Number(total_amount.toFixed(2)));
            }
        });

        body.on('change', $("#tax_type"), function() {
            var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($("#tax_type").val() == 0)
            {
                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount").val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(Number(total_amount.toFixed(2)));
            }
        });

        body.on('click', '.delepart', function(){ 
        $(this).parents('tr').remove();

        var calculated_total_sum = 0;
            var calculated_total_gst_amount = 0;
            expense_amount_array = [];
            expense_tax_array = [];

            $('[name="expense_amount[]"]').each(function() {
                expense_amount_array.push(Number(this.value));
            })
            $('[name="expense_tax[]"]').each(function() {
                expense_tax_array.push(Number(this.value));
            })

            if($("#tax_type").val() == 0)
            {
                $("#partstable #expense_amount").each(function (index) {
                    
                calculated_total_sum += parseFloat(expense_amount_array[index]);
                if(expense_tax_array[index] > 0){
                    var subractbleTaxAmount = (expense_amount_array[index]) * (100 / (100 + (expense_tax_array[index])))
                    var taxAmount = expense_amount_array[index] - subractbleTaxAmount;
                    calculated_total_gst_amount += parseFloat(taxAmount);
                }
                });
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                $("#total_amount").val(Number(calculated_total_sum.toFixed(2)));
            }
            else if($("#tax_type").val() == 1)
            {
                $("#partstable #expense_amount").each(function (index) {

                    calculated_total_sum += parseFloat(expense_amount_array[index]);
                    if(expense_tax_array[index] > 0)
                    {
                    calculated_total_gst_amount += parseFloat((expense_amount_array[index] * expense_tax_array[index])/100);
                    }   
                });
                    
                $("#expense_total_amount").val(Number(calculated_total_sum.toFixed(2)));
                $("#total_gst").val(Number(calculated_total_gst_amount.toFixed(2)));
                var total_amount = calculated_total_sum + calculated_total_gst_amount;
                $("#total_amount").val(Number(total_amount.toFixed(2)));
            }
        });

    });

        // $("input[name='item_quantity[]']").change(function() {
        //     var arrayOfVar = []
        //     $.each($("input[name='item_quantity[]']"),function(indx,obj){
        //         arrayOfVar.push($(obj).val());
        //     });
        //     console.log(arrayOfVar);
        // });

        //onchange each option
            // function getData(){
            //     var inps = document.getElementsByName('nave[]');
            //     for (var i = 0; i <inps.length; i++) {
            //     var inp=inps[i];
            //         alert("nave["+i+"].value="+inp.value);
            //     }
            //     }

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