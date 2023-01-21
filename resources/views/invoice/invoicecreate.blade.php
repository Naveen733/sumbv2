@include('includes.head')
@include('includes.user-header')




<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-xl-6">
                <div class="form-input--wrap">
                    <label class="form-input--question" for="">Item Code </label>
                    <div class="form--inputbox">
                        <div class="col-12">
                            <input type="text"  class="form-control" id="invoice_item_code" name="invoice_item_code" placeholder=""  value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="form-input--wrap">
                    <label class="form-input--question" for="">Item Name </label>
                    <div class="form--inputbox">
                        <div class="col-12">
                            <input type="text"  class="form-control" id="invoice_item_name" name="invoice_item_name" placeholder=""  value="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-xl-6">
                <div class="form-input--wrap">
                    <label class="form-input--question" for="">Unit Price</label>
                    <div class="form--inputbox">
                        <div class="col-12">
                        <input type="number" class="form-control" id="invoice_item_unit_price" name="invoice_item_unit_price" placeholder=""  value="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
            <label class="form-input--question" for="">Tax Rate</label>
                <div class="input-group mb-3">
                    <!-- <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Options</label>
                    </div> -->
                    <select class="custom-select form-control" id="invoice_item_tax_rate" name="invoice_item_tax_rate" value="">
                        <option selected>Choose...</option>
                        <option value="0">Tax Exempt(0%)</option>
                        <option value="10">Tax Included(10%)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="form-input--wrap">
                    <label class="form-input--question" for="" >Description</label>
                    <textarea class="form-control" id="invoice_item_description" name="invoice_item_description"></textarea>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="invoice_part_row_id" value="">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="addInvoiceItem('invoice_part_row_id')">Save</button>
      </div>
    </div>
  </div>
</div>

<!--  New item pop-up model ends -->

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
                        <div class="alert alert-" role="alert">
                        <!-- <a href="" class="pop-model" data-toggle="modal" data-target="#exampleModalCenter">+ New Item</a> -->
                        <!-- Button trigger modal -->
                        <!-- <button type="button" class="btn btn-primary pop-model" data-toggle="modal" data-target="#exampleModalCenter">
                            Launch demo modal
                        </button> -->
                        

                        
                        </div>
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
                                            <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off" required value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['client_name'] : '' }}" >
                                        </div>
                                    </div>
                                    @error('client_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <div class="form--recentsearch clientname row">
                                        <div class="col-12">
                                            
                                            <div class="form--recentsearch__result">
                                                <ul>
                                                    
                                                @if (empty($clients))
                                                        <li>You dont have any clients at this time</li>
                                                    @else
                                                        @php $counter = 0; @endphp
                                                        @foreach ($clients as $ec)
                                                            @php $counter ++; @endphp
                                                            <li>
                                                                <button type="button" class="dcc_click" data-myid="{{ $counter }}">
                                                                    <span id="data_name_{{ $counter }}">{{ $ec['client_name'] }}</span>
                                                                    <input type="hidden" id="data_email_{{ $counter }}" value="{{ $ec['client_email'] }}">
                                                                    <input type="hidden" id="data_phone_{{ $counter }}" value="{{ $ec['client_phone'] }}">
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
                                            <input type="email" id="client_email" name="client_email" placeholder="Client Email Address" class="form-control" value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['client_email'] : '' }}">
                                        </div>
                                    </div>
                                    @error('client_email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label for="client_phone" class="form-input--question">
                                        Client Contact Number
                                    </label>
                                    <div class="form--inputbox row">
                                        <div class="col-12">
                                            <input type="text" id="client_phone" name="client_phone" placeholder="Client Contact Number" class="form-control" value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['client_phone'] : ''}}">
                                        </div>
                                    </div>
                                    <!-- @error('client_phone')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror -->
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
                                            <input type="text" id="invoice_date" name="invoice_issue_date" placeholder="date('m/d/Y')"  readonly value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['invoice_issue_date'] : ''}}">
                                        </div>
                                    </div>
                                    @error('invoice_issue_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="invoice_date">Due Date <span>Optional</span></label>
                                    <div class="date--picker row">
                                        <div class="col-12">
                                            <input type="text" id="invoice_duedate" name="invoice_due_date" placeholder="Due Date" readonly value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['invoice_due_date'] : ''}}">
                                        </div>
                                    </div>
                                    @error('invoice_due_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question">Invoice Number <span>Read-Only</span></label>
                                    <div class="form--inputbox readOnly row">
                                        <div class="col-12">
                                            <input type="text" readonly="" name="" value="{{!empty($invoice_number) ? 'INV-000'.($invoice_number + 1) : ''}}">
                                            <input type="hidden" readonly="" name="invoice_number" value="{{!empty($invoice_number) ? $invoice_number + 1 : ''}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                
                        <hr class="form-cutter">
                        <div class="input-group mb-3">
                            <select class="custom-select form-control" id="invoice_default_tax" name="invoice_default_tax" value="" onchange="InvoicepartsQuantity('invoice_default_tax')">
                                <!-- <option selected>Choose...</option> -->
                                <option value="tax_exclusive" {{!empty(session('form_data')['invoice_details']) && session('form_data')['invoice_details']['invoice_default_tax']=="tax_exclusive" ? "selected" : ''}}>Tax Exclusive</option>
                                <option value="tax_inclusive" {{!empty(session('form_data')['invoice_details']) && session('form_data')['invoice_details']['invoice_default_tax']=="tax_inclusive" ? "selected" : ''}}>Tax Inclusive</option>
                                <option value="no_tax" {{!empty(session('form_data')['invoice_details']) && session('form_data')['invoice_details']['invoice_default_tax']=="no_tax" ? "selected" : ''}}>No tax</option>
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table id="partstable">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:120px; min-width:120px;">Item</th>
                                        <th scope="col" style="width:100px; min-width:100px;">QTY</th>
                                        <th scope="col" style="width:320px; min-width:320px;">Description</th>
                                        <th scope="col" style="width:120px; min-width:120px;">Unit Price</th>
                                        <th scope="col" style="width:120px; min-width:120px;">Tax Rate</th>
                                        <th scope="col" style="width:120px; min-width:120px;">Amount</th>
                                        <th scope="col" style="width:20px; min-width:20px;">&nbsp;</th>
                                        <!-- <th scope="col" style="width:20px; min-width:20px;">&nbsp;</th>
                                        <th scope="col" style="width:20px; min-width:20px;">&nbsp;</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                @if (!empty(session('form_data')['invoice_details']))
                                    @php $invoice_details = session('form_data')['invoice_details']; @endphp
                                    @for ($i=0; $i< count($invoice_details['parts']); $i++)
                                        <?php $row_index = $invoice_details['parts'][$i]['invoice_parts_id'] ?>
                                        <tr id="{{'invoice_parts_row_id_'.$row_index}}" class="invoice_parts_form_cls">
                                            <td>
                                                <?php $invoice_part_code_name = !empty($invoice_details['parts'][$i]['invoice_parts_name'] && $invoice_details['parts'][$i]['invoice_parts_code'] ) 
                                                        ? $invoice_details['parts'][$i]['invoice_parts_code']. ":" .$invoice_details['parts'][$i]['invoice_parts_name'] : '' ?>
                                               
                                               <input type="hidden" id="{{'invoice_parts_code_'.$row_index}}" name="{{'invoice_parts_code_'.$row_index}}" value="{{!empty($invoice_details['parts'][$i]['invoice_parts_code']) ? $invoice_details['parts'][$i]['invoice_parts_code'] : ''}}">
                                               <input type="hidden" id="{{'invoice_parts_name_'.$row_index}}" name="{{'invoice_parts_name_'.$row_index}}" value="{{!empty($invoice_details['parts'][$i]['invoice_parts_name']) ? $invoice_details['parts'][$i]['invoice_parts_name'] : ''}}">

                                                <input data-toggle="dropdown" id="{{'invoice_parts_name_code_'.$row_index}}" name="{{'invoice_parts_name_code_'.$row_index}}" type="text" onkeyup="searchInvoiceparts(this)" value="{{!empty($invoice_part_code_name) ? $invoice_part_code_name : ''}}">

                                                <ul class="dropdown-menu" id="{{'invoice_item_list_'.$row_index}}" >
                                                    <li>
                                                        <a href="" class="pop-model" data-toggle="modal" data-target="#exampleModalCenter">+ New Item</a>
                                                    </li>
                                                    @if (!empty($invoice_items))
                                                        @php $counter = 0; @endphp
                                                        @foreach ($invoice_items as $item)
                                                            @php $counter ++; @endphp
                                                            <li>
                                                                <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getInvoiceItemsById('{{ $item['id'] }}', '{{$row_index}}')">
                                                                    <span id="data_name_{{ $counter }}">{{ $item['invoice_item_code'] }}:{{ $item['invoice_item_name'] }}</span>
                                                                    <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $item['id'] }}">
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul> 
                                                @error('invoice_parts_quantity_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input id="{{'invoice_parts_quantity_'.$row_index}}" name="{{'invoice_parts_quantity_'.$row_index}}" type="number" onchange="InvoicepartsQuantity('{{$row_index}}')" value="{{!empty($invoice_details['parts'][$i]['invoice_parts_quantity']) ? $invoice_details['parts'][$i]['invoice_parts_quantity'] : ''}}">
                                                @error('invoice_parts_quantity_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <textarea id="{{'invoice_parts_description_'.$row_index}}" name="{{'invoice_parts_description_'.$row_index}}" class="autoresizing" >{{!empty($invoice_details['parts'][$i]['invoice_parts_description']) ? $invoice_details['parts'][$i]['invoice_parts_description'] : ''}}</textarea>
                                                @error('invoice_parts_description_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            
                                            <td>
                                                <input id="{{'invoice_parts_unit_price_'.$row_index}}" name="{{'invoice_parts_unit_price_'.$row_index}}" type="number" value="{{!empty($invoice_details['parts'][$i]['invoice_parts_unit_price']) ? $invoice_details['parts'][$i]['invoice_parts_unit_price'] : ''}}" onchange="InvoicepartsQuantity('{{$row_index}}')">
                                                @error('invoice_parts_unit_price_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <input type="hidden" id="{{'invoice_parts_gst_'.$row_index}}" name="{{'invoice_parts_gst_'.$row_index}}" value="">
                                            </td>
                                            <td>
                                                <div class="input-group mb-3">
                                                    <select class="custom-select form-control" id="{{'invoice_parts_tax_rate_'.$row_index}}" name="{{'invoice_parts_tax_rate_'.$row_index}}" value="" onchange="InvoicepartsQuantity('{{$row_index}}')" value="">
                                                        <option selected>Choose...</option>
                                                        <option value="0" {{ $invoice_details['parts'][$i]['invoice_parts_tax_rate']=="0" ? 'selected' : '' }}>Tax Exempt(0%)</option>
                                                        <option value="10" {{ (!empty($invoice_details['parts'][$i]['invoice_parts_tax_rate']) && $invoice_details['parts'][$i]['invoice_parts_tax_rate']=="10") ? 'selected' : '' }}>Tax Included(10%)</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <input readonly id="{{'invoice_parts_amount_'.$row_index}}" name="{{'invoice_parts_amount_'.$row_index}}" type="number" value="{{!empty($invoice_details['parts'][$i]['invoice_parts_amount']) ? $invoice_details['parts'][$i]['invoice_parts_amount'] : ''}}">
                                                @error('invoice_parts_amount_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="tableOptions">
                                                <button class="btn sumb--btn delepart" type="button" onclick="deleteInvoiceParts(<?php echo $row_index?>)" ><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endfor
                                        @else
                                        <tr id="invoice_parts_row_id_0" class="invoice_parts_form_cls">
                                            <td>
                                                <input data-toggle="dropdown" type="text" id="invoice_parts_name_code_0" name="invoice_parts_name_code_0" onkeyup="searchInvoiceparts(this)" value="">
                                                <input type="hidden" id="invoice_parts_code_0" name="invoice_parts_code_0" value="">
                                                <input type="hidden" id="invoice_parts_name_0" name="invoice_parts_name_0" value="">

                                                <ul class="dropdown-menu" id="invoice_item_list_0">
                                                    <div id="add_new_invoice_item_0">
                                                        <a href="" class="pop-model" data-toggle="modal" data-target="#exampleModalCenter" onclick="openPopUpModel(0)">+ New Item</a>
                                                    </div>
                                                    @if (!empty($invoice_items))
                                                        @php $counter = 0; @endphp
                                                        @foreach ($invoice_items as $item)
                                                            @php $counter ++; @endphp
                                                            <li>
                                                                <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getInvoiceItemsById('{{ $item['id'] }}', 0)">
                                                                    <span id="data_name_{{ $counter }}">{{ $item['invoice_item_code'] }}:{{ $item['invoice_item_name'] }} </span>
                                                                    <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $item['id'] }}">
                                                                </button>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </td>
                                            <td>
                                                <input id="invoice_parts_quantity_0" name="invoice_parts_quantity_0" type="number" onchange="InvoicepartsQuantity(0)" value="">
                                            </td>
                                            <td>
                                                <textarea id="invoice_parts_description_0" name="invoice_parts_description_0" class="autoresizing" value=""></textarea>
                                            </td>
                                           
                                            <td>
                                                <input id="invoice_parts_unit_price_0" name="invoice_parts_unit_price_0" type="number" value="" onchange="InvoicepartsQuantity(0)">
                                                <input type="hidden" id="invoice_parts_gst_0" name="invoice_parts_gst_0" value="">
                                            </td>
                                            <td id="invoice_parts_tax_rate_td_0">
                                                <div class="input-group mb-3">
                                                    <select class="custom-select form-control" id="invoice_parts_tax_rate_0" name="invoice_parts_tax_rate_0" onchange="InvoicepartsQuantity(0)" value="">
                                                        <option selected>Choose...</option>
                                                        <option value="0">Tax Exempt(0%)</option>
                                                        <option value="10">Tax Included(10%)</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <input readonly id="invoice_parts_amount_0" name="invoice_parts_amount_0" type="number" value="">
                                            </td>
                                            <td class="tableOptions">
                                                <button class="btn sumb--btn delepart" type="button" onclick="deleteInvoiceParts(0)" ><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endif
                                    
                                    <tr class="add--new-line">
                                        <td colspan="5">
                                            <button class="btn sumb--btn" type="button" id="addnewline" onclick="addInvoiceParts()" ><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                        </td>
                                    </tr>
                                    
                                    <tr class="invoice-separator">
                                        <td colspan="5">hs</td>
                                    </tr>

                                    <tr class="invoice-total--subamount">
                                        <td colspan="2" rowspan="3"></td>
                                        <td>Subtotal (excl GST)</td>
                                        <td colspan="2">
                                            <!-- $0 -->
                                            <input type="text" id="invoice_sub_total" name="invoice_sub_total" readonly="" value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['invoice_sub_total'] : 0 }}">
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--gst">
                                        <td id="invoice_total_gst_text" >Total GST</td>
                                        <td colspan="2">
                                            <!-- $0 -->
                                            <input type="text" id="invoice_total_gst" name="invoice_total_gst" readonly="" value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['invoice_total_gst'] : 0 }}">
                                        </td>
                                    </tr>

                                    <tr class="invoice-total--amountdue">
                                        <td><strong>Amount Due</strong></td>
                                        <td colspan="2">
                                            <strong id="grandtotal"></strong>
                                            <input type="text" id="invoice_total_amount" name="invoice_total_amount" readonly="" value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['invoice_total_amount'] : 0 }}">
                                            <!-- <input type="hidden" name="gtotal" id="gtotal" value=""> -->
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
                                <input type="hidden" id="invoice_part_ids" name="invoice_part_total_count" value="{{!empty(session('form_data')['invoice_details']) ? session('form_data')['invoice_details']['invoice_part_total_count'] : '[0]' }}" />
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
        
        
        $('.dcc_click').on('click', function () {
            var clientid = $(this).data('myid');
            var clientname = $("#data_name_"+clientid).html();
            var clientdesc = $("#data_desc_"+clientid).html();
            
            $('#client_name').val( $("#data_name_"+clientid).html() );
            $('#client_email').val( $("#data_email_"+clientid).val() );
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
                $('#client_email').val('');
                $('#client_phone').val('');

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
                    $('#client_email').val('');
                    $('#client_phone').val('');
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

        $('#partstable').on('input', '.autoresizing', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    function openPopUpModel(id){
        $("#invoice_part_row_id").val('');
        $("#invoice_part_row_id").val(id);
        $("#invoice_item_code").val('');
        $("#invoice_item_name").val('');
        $("#invoice_item_unit_price").val('');
        $("#invoice_item_tax_rate").val('');
        $("#invoice_item_description").val('');
        
        $('#exampleModalCenter').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });
    }

    //Add new row on Table Particulars
    function addInvoiceParts(){
        var rowIndex = [0];
        rowIndex = $('#invoice_part_ids').val();
        rowIndex = JSON.parse(rowIndex);
        if(rowIndex.length>0){
            rowIndex = parseInt(Math.max(...rowIndex))+1;
        }else{
            rowIndex = 1;
        }
        var ulId = JSON.parse($('#invoice_part_ids').val())[0];

        if($("#invoice_default_tax").val() == 'no_tax'){
            $("#invoice_parts_tax_rate_"+rowIndex).css("display", "none");
        }

        
        $("#partstable tr.add--new-line").before('<tr class="invoice_parts_form_cls" id="invoice_parts_row_id_'+rowIndex+'" >\
                        <td><input data-toggle="dropdown" type="text" id="invoice_parts_name_code_'+rowIndex+'" name="invoice_parts_name_code_'+rowIndex+'" onkeyup="searchInvoiceparts(this)" value="">\
                        <input type="hidden" id="invoice_parts_code_'+rowIndex+'" name="invoice_parts_code_'+rowIndex+'" value="">\
                        <input type="hidden" id="invoice_parts_name_'+rowIndex+'" name="invoice_parts_name_'+rowIndex+'" value="">\
                        <ul class="dropdown-menu" id="invoice_item_list_'+rowIndex+'">\
                            </ul>\
                        </td>\
                        <td><input type="number" id="invoice_parts_quantity_'+rowIndex+'" name="invoice_parts_quantity_'+rowIndex+'" value="" onchange=InvoicepartsQuantity('+rowIndex+')></td>\
                        <td><textarea class="autoresizing" id="invoice_parts_description_'+rowIndex+'" name="invoice_parts_description_'+rowIndex+'" value=""></textarea></td>\
                        <td><input type="number" id="invoice_parts_unit_price_'+rowIndex+'" name="invoice_parts_unit_price_'+rowIndex+'" value="" onchange=InvoicepartsQuantity('+rowIndex+')>\
                            <input type="hidden" id="invoice_parts_gst_'+rowIndex+'" name="invoice_parts_gst_'+rowIndex+'" value="">\
                        </td>\
                        <td>\
                            <div class="input-group mb-3">\
                                <select class="custom-select form-control" id="invoice_parts_tax_rate_'+rowIndex+'" name="invoice_parts_tax_rate_'+rowIndex+'" onchange=InvoicepartsQuantity('+rowIndex+')>\
                                    <option selected>Choose...</option>\
                                    <option value="0">Tax Exempt(0%)</option>\
                                    <option value="10">Tax Included(10%)</option>\
                                </select>\
                            </div>\
                        </td>\
                        <td><input type="number" readonly id="invoice_parts_amount_'+rowIndex+'" name="invoice_parts_amount_'+rowIndex+'" value="" >\n\
                        </td><td class="tableOptions"><button class="btn sumb--btn delepart" type="button" onclick=deleteInvoiceParts('+rowIndex+')><i class="fas fa-trash-alt"></i></button></td></tr>');
        
        getInvoiceItemList(rowIndex);

        addOrRemoveInvoicePartsIds('add',rowIndex);
    }

    function getInvoiceItemList(id){
        var post_data = {}
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            $.ajax({
            method: "POST",
            url: "{{ url('/invoice-items') }}",
            // data: post_data,
            success:function(response){
                try{
                    response = JSON.parse(response);
                    
                    if(response && response.status == "success"){
                        
                        // $("#client_details").show();
                        $("#invoice_item_list_"+id).empty();
                        // $("#add_new_invoice_item").empty();
                        var counter = 0;
                        $("#invoice_item_list_"+id).append('<div id="add_new_invoice_item_'+id+'"><a href="" class="pop-model" data-toggle="modal" data-target="#exampleModalCenter" onclick=openPopUpModel('+id+')>+ New Item</a></div>')

                        $.each(response.data,function(key,value){
                            counter++;
                            $("#invoice_item_list_"+id).append('\n\<li>\n\
                                    <button type="button"  class="invoice_item" data-myid="'+counter+'" onclick=getInvoiceItemsById("'+encodeURI(value['id'])+'","'+id+'");>\n\
                                    <span id="data_name_'+counter+'">'+value['invoice_item_code']+':'+value['invoice_item_name']+'</span>\n\
                                    <input type="hidden" id="invoice_item_id_'+counter+'" name="invoice_item_id" value="'+value['id']+'">\n\
                                    </button></li>');
                        });
                        
                    }else if(response.status == "error"){
                        alert(esponse.err);
                        // $("#client_details").show();
                        // $("#invoice_item_list").empty();
                        // $("#invoice_item_list").append('<input type="checkbox" onclick=closeClientSuggestionBox() name="add_new_client"><label for="add_new_client">Add as a new active client?</label></br>');
                    }
                }catch(error){
                    // alertBottom(null,'Something went wrong, try again later');
                }
            },
            error:function(error){ 
                // alertBottom(null,"Something went wrong, please try again later");
            }
        });
    }
    // else{
    //     // $('#clients').empty(); 
    //     // $('#search_activity_error').html('The main business activity required minimum 2 characters');
    //     // $('#client_details').hide();
    // }


    function getInvoiceItemsById(itemId, rowId){
        var post_data = {}
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            $.ajax({
            method: "GET",
            url: "{{ url('/invoice-items') }}"+ '/' + itemId,
            // data: post_data,
            success:function(response){
                try{
                    response = JSON.parse(response);
                    
                    if(response && response.status == "success"){
                        console.log(response['data']['invoice_item_name']);
                        $("#invoice_parts_name_code_"+rowId).val('');
                        $("#invoice_parts_name_"+rowId).val('');
                        $("#invoice_parts_code_"+rowId).val('');
                        $("#invoice_parts_quantity_"+rowId).val('');
                        $("#invoice_parts_description_"+rowId).val('');
                        $("#invoice_parts_unit_price_"+rowId).val('');
                        // $("#invoice_parts_amount_"+rowId).val('');
                        $("#invoice_parts_tax_rate_"+rowId).val('');

                        $("#invoice_parts_name_code_"+rowId).val(response['data']['invoice_item_code']+':'+response['data']['invoice_item_name']);
                        $("#invoice_parts_name_"+rowId).val(response['data']['invoice_item_name']);
                        $("#invoice_parts_code_"+rowId).val(response['data']['invoice_item_code']);
                        $("#invoice_parts_quantity_"+rowId).val(response['data']['invoice_item_quantity']);
                        $("#invoice_parts_description_"+rowId).val(response['data']['invoice_item_description']);
                        $("#invoice_parts_unit_price_"+rowId).val(response['data']['invoice_item_unit_price']);
                        $("#invoice_parts_tax_rate_"+rowId).val(response['data']['invoice_item_tax_rate']);

                        InvoicepartsQuantity(rowId)

                    }else if(response.status == "error"){
                        alert(esponse.err);
                        // $("#client_details").show();
                        // $("#invoice_item_list").empty();
                        // $("#invoice_item_list").append('<input type="checkbox" onclick=closeClientSuggestionBox() name="add_new_client"><label for="add_new_client">Add as a new active client?</label></br>');
                    }
                }catch(error){
                    // alertBottom(null,'Something went wrong, try again later');
                }
            },
            error:function(error){ 
                // alertBottom(null,"Something went wrong, please try again later");
            }
        });
    }
    

    function addOrRemoveInvoicePartsIds(action_type, id){
        var rowIndex = [0];
        rowIndex = $('#invoice_part_ids').val();
        rowIndex = JSON.parse(rowIndex);
        
        if(action_type == "add"){
            if(rowIndex.indexOf(id)<0){
                rowIndex.push(id);
            }
        }else{
            var index = rowIndex.indexOf(id);
            console.log(rowIndex.length);
            console.log(rowIndex);
            if (index > -1) {
                rowIndex.splice(index, 1);
            }
            $('#invoice_parts_row_id_'+id).remove();
            $.each($('.invoice_parts_form_cls'),function(k,v){
                var key=k+1;
                var rowIds=v.id.split('_');
                rowIds = parseInt(rowIds[4]);
            })
        }
        $('#invoice_part_ids').val(JSON.stringify(rowIndex));
    }
    function deleteInvoiceParts(rowId){
        var rowIndex = [0];
        rowIndex = $('#invoice_part_ids').val();
        rowIndex = JSON.parse(rowIndex);
        if(rowIndex.length>1){
            addOrRemoveInvoicePartsIds("delete", rowId);
            InvoicepartsQuantity(rowId);
        }
    }
    
    function InvoicepartsQuantity(id){
        var rowIndex = $('#invoice_part_ids').val();
        rowIndex = JSON.parse(rowIndex);
        var sub_total=0;
        var total_gst=0;
        var gst_percentage = 0;
        for(var rowId=0; rowId<rowIndex.length; rowId++){
            var quantity = $("#invoice_parts_quantity_"+rowId).val();
            var unit_price = $("#invoice_parts_unit_price_"+rowId).val();

            var totalPrice = (parseFloat((quantity ? quantity : 0 )*( unit_price ? unit_price : 0 )));
            // var subPreviousAmount =  $("#invoice_sub_total").val() - $("#invoice_parts_amount_"+rowId).val()
            // $("#invoice_sub_total").val(subPreviousAmount)
            sub_total = sub_total + totalPrice;
            $("#invoice_parts_amount_"+rowId).val(totalPrice.toFixed(2))            
            $("#invoice_sub_total").val((parseFloat(sub_total)).toFixed(2));
            
            if($("#invoice_default_tax").val() == 'tax_exclusive'){
                $(".invoice-total--gst").show();
                $("#invoice_total_amount").val((parseFloat(sub_total) + parseFloat($("#invoice_total_gst").val())).toFixed(2));
                $("#invoice_parts_tax_rate_"+rowId).css("display", "block");
            }
            else if($("#invoice_default_tax").val() == 'no_tax'){
                $("#invoice_total_gst").val(0);
                $(".invoice-total--gst").hide();
                $("#invoice_parts_tax_rate_"+rowId).css("display", "none");
                $("#invoice_total_amount").val((parseFloat(sub_total)).toFixed(2));
            }
            else{
                $(".invoice-total--gst").show();
                $("#invoice_parts_tax_rate_"+rowId).css("display", "block");
                $("#invoice_total_amount").val((parseFloat(sub_total)).toFixed(2));
            }
               
            if(parseFloat($("#invoice_parts_tax_rate_"+rowId).val())>0 && totalPrice>0){
                if($("#invoice_default_tax").val() == 'tax_exclusive'){
                    
                    var gst = (totalPrice * $("#invoice_parts_tax_rate_"+rowId).val()/100);
                    total_gst = (parseFloat(total_gst) + gst).toFixed(2);
                    // const individual_row_gst = (totalPrice * (parseFloat($("#invoice_parts_tax_rate_"+rowId).val()/100))).toFixed(2);
                    // const previous_gst = $("#invoice_total_gst").val() - $("#invoice_parts_gst_"+rowId).val();

                    // const total_gst = (parseFloat(previous_gst) + individual_row_gst).toFixed(2);
                    gst_percentage = $("#invoice_parts_tax_rate_"+rowId).val();
                    $("#invoice_total_gst").val(total_gst);
                    $("#invoice_total_amount").val((parseFloat($("#invoice_sub_total").val()) + parseFloat($("#invoice_total_gst").val())).toFixed(2));
                    $("#invoice_total_gst_text").html("Total Tax "+ gst_percentage +' %');

                }
                else if($("#invoice_default_tax").val() == 'tax_inclusive'){
                    var inclusive_gst = (totalPrice - totalPrice / (1 + $("#invoice_parts_tax_rate_"+rowId).val()/100));
                    total_gst = (total_gst + inclusive_gst);

                    gst_percentage = $("#invoice_parts_tax_rate_"+rowId).val();
                    
                    $("#invoice_total_gst_text").html("Includes Tax "+ $("#invoice_parts_tax_rate_"+rowId).val() +' %');
                    $("#invoice_total_gst").val((parseFloat(total_gst)).toFixed(2));
                }
            }
            else if(parseFloat($("#invoice_parts_tax_rate_"+rowId).val()) == 0 && totalPrice>0){
                if($("#invoice_default_tax").val() == 'tax_exclusive'){

                    var gst = (totalPrice * $("#invoice_parts_tax_rate_"+rowId).val()/100);
                    
                    if(parseFloat(total_gst)>0){
                        console.log(total_gst);
                        $("#invoice_total_gst_text").html("Total Tax "+ gst_percentage +' %');
                    }else{
                        $("#invoice_total_gst_text").html("Total Tax "+ $("#invoice_parts_tax_rate_"+rowId).val()+' %');
                    }
                    // const individual_row_gst = $("#invoice_parts_gst_"+rowId).val();
                    // var total_gst = $("#invoice_total_gst").val();
                    // total_gst = Math.abs((parseFloat(total_gst - individual_row_gst))).toFixed(2)
                    $("#invoice_total_gst").val(total_gst);
                    // $("#invoice_total_gst_text").html("Total Tax "+ $("#invoice_parts_tax_rate_"+rowId).val()+' %');
                    // $("#invoice_parts_gst_"+rowId).val(0);

                    $("#invoice_total_amount").val((parseFloat($("#invoice_sub_total").val()) + parseFloat($("#invoice_total_gst").val())).toFixed(2));
                }
                else if($("#invoice_default_tax").val() == 'tax_inclusive'){
                    var inclusive_gst = (totalPrice - totalPrice / (1 + $("#invoice_parts_tax_rate_"+rowId).val()/100));
        
                    if(parseFloat(total_gst)>0){
                        console.log(total_gst);
                        $("#invoice_total_gst_text").html("Includes Tax "+ gst_percentage +' %');
                    }else{
                        $("#invoice_total_gst_text").html("Includes Tax "+ $("#invoice_parts_tax_rate_"+rowId).val()+' %');
                    }

                    $("#invoice_total_gst").val((parseFloat(total_gst)).toFixed(2));
                    // $("#invoice_total_amount").val((parseFloat($("#invoice_sub_total").val())).toFixed(2));
                }
               
            }
        // $("#invoice_total_gst").val(($("#invoice_sub_total").val()*0.1).toFixed(2))
        }
    }

    function InvoicepartsUnitPrice(obj){
        var rowIds=obj.id.split('_');
        rowIds = parseInt(rowIds[4]);
        var totalUnitPrice = $("#"+obj.id).val()*($("#invoice_parts_quantity_"+rowIds).val() ? $("#invoice_parts_quantity_"+rowIds).val() : 0);
        var subPreviousAmount =  $("#invoice_sub_total").val() - $("#invoice_parts_amount_"+rowIds).val()
        $("#invoice_sub_total").val(subPreviousAmount)
        $("#invoice_parts_amount_"+rowIds).val(totalUnitPrice)
        $("#invoice_sub_total").val((parseFloat($("#invoice_sub_total").val()) + totalUnitPrice).toFixed(2))
        $("#invoice_total_gst").val(($("#invoice_sub_total").val()*0.1).toFixed(2))
        $("#invoice_total_amount").val((parseFloat($("#invoice_sub_total").val()) + parseFloat($("#invoice_total_gst").val())).toFixed(2))
    }
    function searchInvoiceparts(obj){
        var value = $("#"+obj.id).val().toLowerCase();
        $(".dropdown-menu li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    }
    
    function addInvoiceItem(id){
        var id = $("#"+id).val();
        var post_data = {
            invoice_item_code: $("#invoice_item_code").val(),
            invoice_item_name: $("#invoice_item_name").val(),
            invoice_item_unit_price: $("#invoice_item_unit_price").val(),
            invoice_item_tax_rate: $("#invoice_item_tax_rate").val(),
            invoice_item_description: $("#invoice_item_description").val()
        };
        
        if(post_data){
            // $("#invoice_item_list").empty();
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                $.ajax({
                method: "POST",
                url: "{{ url('/add-invoice-item') }}",
                data: post_data,
                success:function(response){
                    try{
                        response = JSON.parse(response);
                        
                        if(response && response.status == "success"){
                            
                            // $("#client_details").show();
                            $("#invoice_item_list_"+id).empty();
                            // $("#add_new_invoice_item").empty();
                            var counter = 0;
                            $("#invoice_item_list_"+id).append('<div id="add_new_invoice_item_'+id+'"><a href="" class="pop-model" data-toggle="modal" data-target="#exampleModalCenter" onclick=openPopUpModel('+id+')>+ New Item</a></div>')

                            $.each(response.data,function(key,value){
                                counter++;
                                $("#invoice_item_list_"+id).append('\n\<li>\n\
                                <button type="button" class="invoice_item" data-myid="'+counter+'" onclick=getInvoiceItemsById("'+encodeURI(value['id'])+'","'+id+'");>\n\
                                <span id="data_name_'+counter+'">'+value['invoice_item_code']+':'+value['invoice_item_name']+'</span>\n\
                                                                </button></li>');
                            });
                            $("#invoice_parts_name_code_"+id).val('');
                            $("#invoice_parts_name_"+id).val('');
                            $("#invoice_parts_code_"+id).val('');
                            $("#invoice_parts_description_"+id).val('');
                            $("#invoice_parts_unit_price_"+id).val('');
                            $("#invoice_parts_quantity_"+id).val('');
                            // $("#invoice_parts_amount_"+id).val('');
                            $("#invoice_parts_tax_rate_"+id).val('');

                            $("#invoice_parts_name_code_"+id).val(post_data.invoice_item_code+':'+post_data.invoice_item_name);
                            $("#invoice_parts_name_"+id).val(post_data.invoice_item_name);
                            $("#invoice_parts_code_"+id).val(post_data.invoice_item_code);

                           
                            $("#invoice_parts_description_"+id).val(post_data.invoice_item_description);
                            $("#invoice_parts_unit_price_"+id).val(post_data.invoice_item_unit_price);
                            $("#invoice_parts_quantity_"+id).val(1.00);
                            $("#invoice_parts_tax_rate_"+id).val(post_data.invoice_item_tax_rate);

                            InvoicepartsQuantity(id)
                            // $("#invoice_parts_amount_"+id).val((parseFloat(post_data.invoice_item_unit_price) * 1).toFixed(2));

                            $(".close").click();
                        }else if(response.status == "error"){
                            alert(esponse.err);
                            // $("#client_details").show();
                            // $("#invoice_item_list").empty();
                            // $("#invoice_item_list").append('<input type="checkbox" onclick=closeClientSuggestionBox() name="add_new_client"><label for="add_new_client">Add as a new active client?</label></br>');
                        }
                    }catch(error){
                        // alertBottom(null,'Something went wrong, try again later');
                    }
                },
                error:function(error){ 
                    // alertBottom(null,"Something went wrong, please try again later");
                }
            });
        }else{
            // $('#clients').empty(); 
            // $('#search_activity_error').html('The main business activity required minimum 2 characters');
            // $('#client_details').hide();
        }
    }
</script>
</body>

</html>
<!-- end document-->