@include('includes.head')
@include('includes.user-header')

<!--  New item pop-up modal starts -->
<div class="modal fade modal-reskin modal-invoice--additem" id="newItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title document--header" id="exampleModalLongTitle">Add New Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Item Code</label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="text" required class="form-control" id="invoice_item_code" name="invoice_item_code" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_item_code_error"></div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Item Name </label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="text" required  class="form-control" id="invoice_item_name" name="invoice_item_name" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_item_name_error"></div>
                        </div>
                    </div>

                </div>
                <div class="row">

                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Unit Price</label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="number" required class="form-control" id="invoice_item_unit_price" name="invoice_item_unit_price" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_item_unit_price_error"></div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Tax Rate</label>
                            <div class="row">
                                <div class="col-12">
                                    @if(!empty($tax_rates))
                                    <select class="form-input--dropdown" id="invoice_item_tax_rate" name="invoice_item_tax_rate" value="" required>
                                        <option selected value="">Tax Rate Option</option>
                                        @foreach($tax_rates as $tax_rate)
                                            <option hidden="hidden" id="{{$tax_rate['id']}}" value="{{$tax_rate['tax_rates']}}"></option>
                                            <option value="{{$tax_rate['id']}}">{{$tax_rate['tax_rates_name']}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="" role="alert" id="invoice_item_tax_rate_error"></div>
                    </div>

                </div>
                <div class="row">

                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Account Type</label>
                            
                            <input class="invoice-expenses--input" data-toggle="dropdown" type="text" id="invoice_item_chart_accounts_parts" name="invoice_item_chart_accounts_parts"  value="" style="margin-bottom: 17px;">
                            <input type="hidden" id="invoice_item_chart_accounts_parts_id" value="">
                            
                            
                            <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list">
                                <li id="add_new_invoice_chart_account" class="add-new--btn">
                                    <a href="#" class="pop-model" data-toggle="modal" data-target="#newAddAccountModal" onclick="openNewAddAccountPopUpModel('invoice_item_part_row_id', 'addItem')"><i class="fa-solid fa-circle-plus"></i>New Account</a>
                                </li>
                                @if (!empty($chart_account))
                                    @php $counter = 0; @endphp
                                    @foreach ($chart_account as $item)
                                        <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>
                                            <!-- <h4>{{$item['chart_accounts_name']}}</h4> -->
                                            @foreach ($item['chart_accounts_particulars'] as $particulars)
                                            <?php
                                                $user = array_search($particulars['chart_accounts_type_id'], array_column($item['chart_accounts_types'], 'id'));
                                            ?>
                                            <li>
                                                <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addInvoiceChartAccount('{{ $particulars['id'] }}', '', 'addItem')">
                                                    <span id="data_name_{{ $counter }}">{{ $particulars['chart_accounts_particulars_code'] }} : {{ $particulars['chart_accounts_particulars_name'] }} </span>
                                                    <input type="hidden" id="invoice_item_chart_accounts_type_id" name="invoice_item_chart_accounts_type_id" value="{{$item['chart_accounts_types'][$user]['chart_accounts_type']}}">
                                                    <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $particulars['id'] }}">
                                                </button>
                                            </li>
                                            
                                            @endforeach
                                        <!-- </optgroup> -->
                                    @endforeach
                                @endif
                            </ul>
                            <div class="" role="alert" id="invoice_chart_accounts_type_error"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Description</label>
                            <textarea id="invoice_item_description" name="invoice_item_description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="invoice_item_part_row_id" value="">
                <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" onclick="addInvoiceItem('invoice_item_part_row_id')">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- New item pop-up modal ends -->


<!--  Add new account pop-up modal starts -->
<div class="modal fade modal-reskin modal-invoice--addaccount" id="newAddAccountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title document--header" id="exampleModalLongTitle">New Add Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Account Type</label>
                            @if(!empty($chart_accounts_types))
                                <select class="form-input--dropdown" id="invoice_chart_accounts_type_id">
                                    <option value="">Select Account Type</option>
                                    @foreach($chart_accounts_types as $chart_accounts)
                                        @if(!empty($chart_accounts))
                                            <optgroup label="{{$chart_accounts['chart_accounts_name']}}">
                                                    <!-- <option id="invoice_chart_accounts_id" value="{{!empty($chart_accounts) ? $chart_accounts['id'] : ''}}"  hidden></option> -->
                                                @foreach($chart_accounts['chart_accounts_types'] as $types)
                                                    <option id="invoice_chart_accounts_id_{{$types['id']}}" value="{{!empty($chart_accounts) ? $chart_accounts['id'] : ''}}"  hidden></option>
                                                    <option value="{{$types['id']}}">{{!empty($types['chart_accounts_type']) ? $types['chart_accounts_type'] : ''}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                            <div class="" role="alert" id="invoice_chart_accounts_type_error"></div>
                        </div>
                        
                    </div>

                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Tax Rate</label>
                            <div class="row">
                                <div class="col-12">
                                    @if(!empty($tax_rates))
                                        <select class="form-input--dropdown" id="invoice_chart_accounts_tax_rate" name="invoice_chart_accounts_tax_rate" value="" required>
                                            <option selected value="">Tax Rate Option</option>
                                            @foreach($tax_rates as $tax_rate)
                                                <option hidden="hidden" id="{{$tax_rate['id']}}" value="{{$tax_rate['tax_rates']}}"></option>
                                                <option value="{{$tax_rate['id']}}">{{$tax_rate['tax_rates_name']}}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_chart_accounts_tax_rate_error"></div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for=""> Code </label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="text" required  class="form-control" id="invoice_chart_accounts_code" name="invoice_chart_accounts_code" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_chart_accounts_code_error"></div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Name</label>
                            <div class="form--inputbox row">
                                <div class="col-12">
                                    <input type="text" required  class="form-control" id="invoice_chart_accounts_name" name="invoice_chart_accounts_name" placeholder=""  value="">
                                </div>
                            </div>
                            <div class="" role="alert" id="invoice_chart_accounts_name_error"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="" >Description</label>
                            <textarea class="form-control" id="invoice_chart_accounts_description" name="invoice_chart_accounts_description"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" id="invoice_account_part_row_id" value="">
                <input type="hidden" id="add_account_from" value="">
                <button type="button" class="btn btn-secondary save--close" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save--btn" onclick="addNewAccount('invoice_account_part_row_id')">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Add new account modal ends -->


<!-- Send Invoice Modal -->

<div class="modal fade modal-reskin modal-invoice--send" id="send_invoice_modal" tabindex="-1" role="dialog" aria-labelledby="send_invoice_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content send-invoice-modal">
              
            <div class="modal-header">
                <h5 class="modal-title sending--header" id="exampleModalLongTitle">Send Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
                </button>
            </div>
            
            <form action="/invoice/send-email?invoice_id={{$invoice_id}}"  method="post" enctype="multipart/form-data">
            {{ method_field('POST') }}
            {{ csrf_field() }}  

                    <div class="modal-body">


                        <div class="form-input--wrap row send-invoice-form">
                            <label for="inputEmail3" class="col-sm-2 form-input--question">To</label>

                            
                            <div class="form--inputbox col-sm-10">
                                <input type="text" id="send_invoice_to_emails" name="send_invoice_to_emails" value="">
                                <span style="color: #8c8c8c; font-weight: 400; font-size: 12px;"> Separate multiple email addresses with a comma (,)</span>
                            </div>
                            
                            @error('send_invoice_to_emails')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="form-input--wrap row send-invoice-form">
                            <label for="inputEmail3" class="col-sm-2 form-input--question">From</label>
                            <div class="form--inputbox readOnly col-sm-10">
                                <input type="text" id="send_invoice_from" readonly name="send_invoice_from" value="">
                            </div>
                        </div>


                        <div class="form-input--wrap row send-invoice-form">
                            <label for="inputPassword3" class="col-sm-2 form-input--question">Subject</label>
                            <div class="form--inputbox col-sm-10">
                                <input type="text" class="form-control" id="send_invoice_subject" name="send_invoice_subject" value="">
                            </div>
                            @error('send_invoice_subject')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-input--wrap row send-invoice-form">
                            <label for="inputPassword3" class="col-sm-2 form-input--question">Message</label>
                            <div class="col-sm-10">
                                <textarea class="form-control send-invoice-form-text-area"  id="send_invoice_message"  name="send_invoice_message" rows="10"></textarea>
                                <input type="hidden" id="send_invoice_message_hidden" name="send_invoice_message_hidden" value="">
                            </div>
                            @error('send_invoice_message')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary close--btn" data-dismiss="modal">Close</button>
                    <button type="sudmit" name="send_invoice" class="btn sumb--btn send--btn" value="Send Invoice">Send Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Invoice Modal End -->


<!-- Invoice Preview -->

<div class="modal fade bd-example-modal-lg modal-reskin" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog invoice--preview">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title invoiceprev--header" id="staticBackdropLabel">Invoice Preview</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa-solid fa-xmark"></i></span>
            </button>
        </div>

        <div class="modal-body">
            <div class="container">

                <center>
                    <h2 class="mb-4 mt-2">Invoice Preview</h4>
                </center>

                <div class="invoicetable--header">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            <ul class="list-unstyled">
                                <li>To: <span id="invoice_preview_to"></span></li>
                                <li>Invoice number: <span id="invoice_preview_invoice_number"></span></li>
                                <li>Issued: <span id="invoice_preview_issue_date"></span></li>
                                <li>Due: <span id="invoice_preview_due_date"></span></li>
                            </ul>
                        </div>
                        <div class="col-xl-6 col-lg-6 invoicetable--header_from">
                            <ul class="list-unstyled">
                                <li>From: <span id="invoice_preview_from">{{$userinfo['1']}}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-striped" id="invoice_preview_parts">
                        <thead>
                            <tr>
                                <th scope="col" style="width:135px; min-width:135px;">Item</th>
                                <th scope="col" style="width:70px; min-width:70px;">QTY</th>
                                <th scope="col" style="width:200px; min-width:200px;">Description</th>
                                <th scope="col" style="width:100px; min-width:100px;">Unit Price</th>
                                <th scope="col" style="width:170px; min-width:170px;">Account</th>
                                <th scope="col" style="width:205px; min-width:205px;">Tax Rate</th>
                                <th scope="col" style="width:70px; min-width:70px;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="invoice_preview_parts_rows"></tbody>
                            
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
                                    <td class="center" id="invoice_preview_sub_total"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Total Tax %</strong>
                                    </td>
                                    <td class="center" id="invoice_preview_total_tax"></td>
                                </tr>
                                <tr class="invoice--paymentinfo-total_amount">
                                    <td>
                                        <strong>Total</strong>
                                    </td>
                                    <td class="center" id="invoice_preview_total_amount"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

    </div>
  </div>
</div>

<!-- Invoice Preview End -->


<div class="page-container">

    @include('includes.user-top')
    
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid" id="my-div-to-mask">
                <section>
                    @if(!empty($invoice_details && $invoice_details['invoice_status']) && $invoice_details['invoice_status'] == 'Voided' || !empty($invoice_details && $invoice_details['invoice_status']) && $invoice_details['invoice_status'] == 'Paid')
                        <h3 class="sumb--title">
                            Invoice INV-{{str_pad($invoice_details['invoice_number'], 6, '0', STR_PAD_LEFT)}}

                            <span class="invoice--status-icon {{ $invoice_details['invoice_status']}}">
                                @if(!empty($invoice_details) && $invoice_details['invoice_status'] == 'Voided')   
                                    {{$invoice_details['invoice_status']}}
                                @elseif(!empty($invoice_details) && $invoice_details['invoice_status'] == 'Paid')
                                    {{ $invoice_details['invoice_status']}}
                                @endif
                            </span>

                        </h3>
                        <div class="invoice--status-deets">This invoice entry is on Read Only mode. Entries flagged as <u>{{!empty($invoice_details) ? $invoice_details['invoice_status'] : '' }}</u> cannot not be edited.</div>
                    @elseif(!empty($invoice_details) && $type == 'edit') 
                        <h3 class="sumb--title">Edit Invoice</h3>
                    @else 
                        <h3 class="sumb--title">Create an Invoice</h3>
                    @endif
                </section>
                <section>
                    <form action="/invoice-create-save?invoice_id={{$invoice_id}}&type={{$type}}" method="post" enctype="multipart/form-data" class="form-horizontal" id="invoice_form">
                        
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
                                            <input type="text" id="client_name" name="client_name" class="form-control" placeholder="Search Client Name" aria-label="Client Name" aria-describedby="button-addon2" autocomplete="off" required value="{{!empty($invoice_details && $invoice_details['client_name']) ? $invoice_details['client_name'] : '' }}" >
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
                                            <input type="email" id="client_email" name="client_email" placeholder="Client Email Address" class="form-control" value="{{!empty($invoice_details) ? $invoice_details['client_email'] : '' }}">
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
                                            <input type="text" id="client_phone" name="client_phone" placeholder="Client Contact Number" class="form-control" value="{{!empty($invoice_details) ? $invoice_details['client_phone'] : ''}}">
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
                                            <input type="text" id="invoice_issue_date" name="invoice_issue_date" placeholder="date('m/d/Y')"  readonly value="{{!empty($invoice_details) ? date('m/d/Y', strtotime($invoice_details['invoice_issue_date'])) : ''}}">
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
                                            <input type="text" id="invoice_duedate" name="invoice_due_date" placeholder="Due Date" readonly value="{{!empty($invoice_details) ?  date('m/d/Y', strtotime($invoice_details['invoice_due_date']))  : ''}}">
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
                                            <input type="text" readonly="" id="invoice_number" name="" value="{{!empty($invoice_details) ? 'INV-'.str_pad($invoice_details['invoice_number'], 6, '0', STR_PAD_LEFT) : 'INV-'.str_pad($invoice_number, 6, '0', STR_PAD_LEFT) }}">
                                            <input type="hidden" readonly="" id="invoice_number_hidden" name="invoice_number" value="{{!empty($invoice_details) ? $invoice_details['invoice_number'] : $invoice_number }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                
                        <hr class="form-cutter">


                        <div class="row">
                            <div class="col-xl-8">
                                &nbsp;
                            </div>

                            <div class="col-xl-4">

                                <div class="form-input--wrap">
                                    <label class="form-input--question">
                                        Amounts are
                                    </label>
                                    <div class="row">
                                        <div class="col-12">
                                            <select class="form-input--dropdown" id="invoice_default_tax" name="invoice_default_tax" value="" onchange="InvoicepartsQuantity('invoice_default_tax')">
                                                <option value="tax_exclusive" {{!empty(session('form_data')['invoice_details']) && session('form_data')['invoice_details']['invoice_default_tax']=="tax_exclusive" ? "selected" : ''}}>Tax Exclusive</option>
                                                <option value="tax_inclusive" {{!empty(session('form_data')['invoice_details']) && session('form_data')['invoice_details']['invoice_default_tax']=="tax_inclusive" ? "selected" : ''}}>Tax Inclusive</option>
                                                <option value="no_tax" {{!empty(session('form_data')['invoice_details']) && session('form_data')['invoice_details']['invoice_default_tax']=="no_tax" ? "selected" : ''}}>No tax</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="table-responsive">
                            <table id="partstable">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:135px; min-width:135px;">Item</th>
                                        <th scope="col" style="width:70px; min-width:70px;">QTY</th>
                                        <th scope="col" style="width:200px; min-width:200px;">Description</th>
                                        <th scope="col" style="width:80px; min-width:80px;">Unit Price</th>
                                        <th scope="col" style="width:170px; min-width:170px;">Account</th>
                                        <th scope="col" style="width:205px; min-width:205px;">Tax Rate</th>
                                        <th scope="col" style="width:70px; min-width:70px;">Amount</th>
                                        <th scope="col" style="width:20px; min-width:20px;">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    @if (!empty($invoice_details))
                                        <?php $invoice_part_total_count = json_decode($invoice_details['invoice_part_total_count'], true)?>
                                        @php $row_index = 0; @endphp
                                        @foreach($invoice_details['parts'] as $parts)
                                        @php !empty($parts['invoice_parts_id']) ? ($row_index = $parts['invoice_parts_id']) : $row_index; @endphp
                                        
                                        @if(count($invoice_part_total_count) != count($invoice_details['parts']))
                                            @php array_push($invoice_part_total_count, $row_index); @endphp
                                            @php $invoice_details['invoice_part_total_count'] = json_encode($invoice_part_total_count); @endphp
                                        @endif
                                        <tr id="{{'invoice_parts_row_id_'.$row_index}}" class="invoice_parts_form_cls">
                                            <td>
                                                <?php $invoice_part_code_name = !empty($parts['invoice_parts_name'] && $parts['invoice_parts_code'] ) 
                                                        ? $parts['invoice_parts_code']. " : " .$parts['invoice_parts_name'] : '' ?>
                                               
                                                <input type="hidden" id="{{'invoice_parts_code_'.$row_index}}" name="{{'invoice_parts_code_'.$row_index}}" value="{{!empty($parts['invoice_parts_code']) ? $parts['invoice_parts_code'] : ''}}">
                                                <input type="hidden" id="{{'invoice_parts_name_'.$row_index}}" name="{{'invoice_parts_name_'.$row_index}}" value="{{!empty($parts['invoice_parts_name']) ? $parts['invoice_parts_name'] : ''}}">
                                                <input type="hidden" id="{{'invoice_parts_id_'.$row_index}}" name="{{'invoice_parts_id_'.$row_index}}" value="{{!empty($parts['id']) ? $parts['id'] : ''}}">
                                                <input autocomplete="off" data-toggle="dropdown" id="{{'invoice_parts_name_code_'.$row_index}}" name="{{'invoice_parts_name_code_'.$row_index}}" type="text" onkeyup="searchInvoiceparts(this)" value="{{!empty($invoice_part_code_name) ? $invoice_part_code_name : ''}}" required>

                                                <ul class="dropdown-menu invoice-expenses--dropdown" id="{{'invoice_item_list_'.$row_index}}" >
                                                    <li class="add-new--btn">
                                                        <a href="#" data-toggle="modal" data-target="#newItemModal" onclick="openPopUpModel('{{$row_index}}')"><i class="fa-solid fa-circle-plus"></i>New Item</a>
                                                    </li>
                                                    @if (!empty($invoice_items))
                                                        @php $counter = 0; @endphp
                                                        @foreach ($invoice_items as $item)
                                                            @php $counter ++; @endphp
                                                            <li>
                                                                <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getInvoiceItemsById('{{ $item['id'] }}', '{{$row_index}}')">
                                                                    <span id="data_name_{{ $counter }}">{{ $item['invoice_item_code'] }} : {{ $item['invoice_item_name'] }}</span>
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
                                                <input id="{{'invoice_parts_quantity_'.$row_index}}" name="{{'invoice_parts_quantity_'.$row_index}}" type="number" onchange="InvoicepartsQuantity('{{$row_index}}')" value="{{!empty($parts['invoice_parts_quantity']) ? $parts['invoice_parts_quantity'] : ''}}" required>
                                                @error('invoice_parts_quantity_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <textarea id="{{'invoice_parts_description_'.$row_index}}" name="{{'invoice_parts_description_'.$row_index}}" class="autoresizing" required>{{!empty($parts['invoice_parts_description']) ? $parts['invoice_parts_description'] : ''}}</textarea>
                                                @error('invoice_parts_description_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input id="{{'invoice_parts_unit_price_'.$row_index}}" name="{{'invoice_parts_unit_price_'.$row_index}}" type="float" value="{{!empty($parts['invoice_parts_unit_price']) ? number_format($parts['invoice_parts_unit_price'], 2)  : ''}}" onchange="InvoicepartsQuantity('{{$row_index}}')" step=".01" required>
                                                @error('invoice_parts_unit_price_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <input type="hidden" id="{{'invoice_parts_gst_'.$row_index}}" name="{{'invoice_parts_gst_'.$row_index}}" value="">
                                            </td>
                                            <td>
                                                <input data-toggle="dropdown" type="text" id="{{'invoice_parts_chart_accounts_'.$row_index}}" name="{{'invoice_parts_chart_accounts_'.$row_index}}"  value="{{!empty($parts['invoice_chart_accounts_particulars']) && $parts['invoice_chart_accounts_particulars']['id'] ? $parts['invoice_chart_accounts_particulars']['chart_accounts_particulars_code'] .' - '. $parts['invoice_chart_accounts_particulars']['chart_accounts_particulars_name'] : '' }}" required>
                                               
                                                <input type="hidden" id="{{'invoice_parts_chart_accounts_code_'.$row_index}}" name="{{'invoice_parts_chart_accounts_code_'.$row_index}}" value="">
                                                <input type="hidden" id="{{'invoice_parts_chart_accounts_name_'.$row_index}}" name="{{'invoice_parts_chart_accounts_name_'.$row_index}}" value="">
                                                <input type="hidden" id="{{'invoice_parts_chart_accounts_parts_id_'.$row_index}}" name="{{'invoice_parts_chart_accounts_parts_id_'.$row_index}}" value="{{!empty($parts['invoice_chart_accounts_particulars']) && $parts['invoice_chart_accounts_particulars']['id'] ? $parts['invoice_chart_accounts_particulars']['id'] : $parts['invoice_chart_accounts_parts_id']}}">
                                                
                                                <ul class="dropdown-menu invoice-expenses--dropdown" id="{{'invoice_chart_account_list_'.$row_index}}" >
                                                    <li id="{{'add_new_invoice_chart_account_'.$row_index}}" class="add-new--btn">
                                                        <a href="" data-toggle="modal" data-target="#newAddAccountModal" onclick="openNewAddAccountPopUpModel(0)"><i class="fa-solid fa-circle-plus"></i>New Account</a>
                                                    </li>
                                                    @if (!empty($chart_account))
                                                        @php $counter = 0; @endphp
                                                        @foreach ($chart_account as $item)
                                                                <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>

                                                                @foreach ($item['chart_accounts_particulars'] as $particulars)
                                                                <?php $user = array_search($particulars['chart_accounts_type_id'], array_column($item['chart_accounts_types'], 'id')); ?>
                                                                <li>
                                                                    <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addInvoiceChartAccount('{{ $particulars['id'] }}', '{{$row_index}}')">
                                                                        <span id="data_name_{{ $counter }}">{{ $particulars['chart_accounts_particulars_code'] }} - {{ $particulars['chart_accounts_particulars_name'] }} </span>
                                                                        <input type="hidden" value="{{$item['chart_accounts_types'][$user]['chart_accounts_type']}}">
                                                                        <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $particulars['id'] }}">
                                                                    </button>
                                                                </li>
                                                                @endforeach
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </td>

                                            <td>
                                                @if(!empty($tax_rates))
                                                    <input type="hidden" name="{{'invoice_parts_tax_rate_id_'.$row_index}}" id="{{'invoice_parts_tax_rate_id_'.$row_index}}" value="{{!empty($parts['invoice_parts_tax_rate_id']) ? $parts['invoice_parts_tax_rate_id'] : ''}}">
                                                    <input type="hidden" name="{{'invoice_parts_tax_rate_name_'.$row_index}}" id="{{'invoice_parts_tax_rate_name_'.$row_index}}" value="">
                                                    <div class="form-input--wrap">
                                                        <div class="row">
                                                            <div class="col-12 for--tables">
                                                                <select class="form-input--dropdown" id="{{'invoice_parts_tax_rate_'.$row_index}}" name="{{'invoice_parts_tax_rate_'.$row_index}}" value="" onchange="InvoicepartsQuantity('{{$row_index}}'); getTaxRates('{{$row_index}}');" value="{{$parts['invoice_parts_tax_rate']}}">
                                                                    <option selected value="">Tax Rate Option</option>    
                                                                    @foreach($tax_rates as $tax_rate)
                                                                        <option hidden="hidden" id="{{'tax_rate_id_'.$tax_rate['id'].'_'.$row_index}}" value="{{ !empty($tax_rate['id']) ? $tax_rate['id'] : ''}}" ></option>
                                                                        <option id="{{$tax_rate['id'].'_'.$row_index}}" value="{{$tax_rate['tax_rates']}}" {{ $parts['invoice_parts_tax_rate_id']==$tax_rate['id'] ? 'selected' : '' }}>{{$tax_rate['tax_rates_name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                            </td>

                                            <td>
                                                <input class="input--readonly" readonly id="{{'invoice_parts_amount_'.$row_index}}" name="{{'invoice_parts_amount_'.$row_index}}" type="number" value="{{!empty($parts['invoice_parts_amount']) ? number_format($parts['invoice_parts_amount'], 2) : ''}}">
                                                @error('invoice_parts_amount_'.$row_index)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="tableOptions">
                                                <button class="btn sumb--btn delepart" type="button" onclick="deleteInvoiceParts(<?php echo $row_index ?>)" ><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                        @php  $row_index++ @endphp
                                        @endforeach
                                        @else
                                            <tr id="invoice_parts_row_id_0" class="invoice_parts_form_cls">
                                                <td>
                                                    <input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_name_code_0" name="invoice_parts_name_code_0" onkeyup="searchInvoiceparts(this)" value="">
                                                    <input type="hidden" id="invoice_parts_code_0" name="invoice_parts_code_0" value="">
                                                    <input type="hidden" id="invoice_parts_name_0" name="invoice_parts_name_0" value="">

                                                    <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_item_list_0">
                                                        
                                                        <li id="add_new_invoice_item_0" class="add-new--btn">
                                                            <a href="#" data-toggle="modal" data-target="#newItemModal" onclick="openPopUpModel(0)"><i class="fa-solid fa-circle-plus"></i>New Item</a>
                                                        </li>

                                                        @if (!empty($invoice_items))
                                                            @php $counter = 0; @endphp
                                                            @foreach ($invoice_items as $item)
                                                                @php $counter ++; @endphp
                                                                <li>
                                                                    <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="getInvoiceItemsById('{{ $item['id'] }}', 0)">
                                                                        <span id="data_name_{{ $counter }}">{{ $item['invoice_item_code'] }} : {{ $item['invoice_item_name'] }}</span>
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
                                                    <input id="invoice_parts_unit_price_0" name="invoice_parts_unit_price_0" type="float" value="" onchange="InvoicepartsQuantity(0)">
                                                    <input type="hidden" id="invoice_parts_gst_0" name="invoice_parts_gst_0" value="">
                                                </td>
                                                <td>
                                                    <input autocomplete="off" data-toggle="dropdown" type="text" id="invoice_parts_chart_accounts_0" name="invoice_parts_chart_accounts_0"  value="">
                                                    <input type="hidden" id="invoice_parts_chart_accounts_code_0" name="invoice_parts_chart_accounts_code_0" value="">
                                                    <input type="hidden" id="invoice_parts_chart_accounts_name_0" name="invoice_parts_chart_accounts_name_0" value="">

                                                    <input type="hidden" id="invoice_parts_chart_accounts_parts_id_0" name="invoice_parts_chart_accounts_parts_id_0" value="">

                                                    <ul class="dropdown-menu invoice-expenses--dropdown" id="invoice_chart_account_list_0">
                                                        <li id="add_new_invoice_chart_account_0" class="add-new--btn">
                                                            <a href="#" class="pop-model" data-toggle="modal" data-target="#newAddAccountModal" onclick="openNewAddAccountPopUpModel(0)"><i class="fa-solid fa-circle-plus"></i>New Account</a>
                                                        </li>
                                                        @if (!empty($chart_account))
                                                        @php $counter = 0; @endphp
                                                        @foreach ($chart_account as $item)
                                                            <li class="accounts-group--label">{{$item['chart_accounts_name']}}</li>
                                                                
                                                            @foreach ($item['chart_accounts_particulars'] as $particulars)
                                                            <?php
                                                                $user = array_search($particulars['chart_accounts_type_id'], array_column($item['chart_accounts_types'], 'id'));
                                                            ?>
                                                            <li>
                                                                <button type="button" class="invoice_item" data-myid="{{ $counter }}" onclick="addInvoiceChartAccount('{{ $particulars['id'] }}', 0)">
                                                                    <span id="data_name_{{ $counter }}">{{ $particulars['chart_accounts_particulars_code'] }} : {{ $particulars['chart_accounts_particulars_name'] }} </span>
                                                                    <input type="hidden" id="invoice_parts_chart_accounts_type_id_0" name="invoice_parts_chart_accounts_type_id_0" value="{{$item['chart_accounts_types'][$user]['chart_accounts_type']}}">
                                                                    <input type="hidden" id="invoice_item_id_{{ $counter }}" name="invoice_item_id" value="{{ $particulars['id'] }}">
                                                                </button>
                                                            </li>
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                    </ul>
                                                </td>
                                                <td id="invoice_parts_tax_rate_td_0">

                                                    @if(!empty($tax_rates))
                                                        <input type="hidden" name="invoice_parts_tax_rate_id_0" id="invoice_parts_tax_rate_id_0" value="">
                                                        <input type="hidden" name="invoice_parts_tax_rate_name_0" id="invoice_parts_tax_rate_name_0" value="">
                                                        <div class="form-input--wrap">
                                                            <div class="row">
                                                                <div class="col-12 for--tables">
                                                                    <select class="form-input--dropdown" id="invoice_parts_tax_rate_0" name="invoice_parts_tax_rate_0" onchange="InvoicepartsQuantity(0); getTaxRates(0);" value="">
                                                                        <option selected value="">Tax Rate Option</option>    
                                                                        @foreach($tax_rates as $tax_rate)
                                                                            <option hidden="hidden" id="{{'tax_rate_id_'.$tax_rate['id'].'_0'}}" value="{{$tax_rate['id']}}"></option>
                                                                            <option id="{{$tax_rate['id'].'_0'}}" value="{{$tax_rate['tax_rates']}}">{{$tax_rate['tax_rates_name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input class="input--readonly" readonly id="invoice_parts_amount_0" name="invoice_parts_amount_0" type="number" value="">
                                                </td>
                                                <td class="tableOptions">
                                                    <button class="btn sumb--btn delepart" type="button" onclick="deleteInvoiceParts(0)" ><i class="fas fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        @endif
                                    
                                        <tr class="add--new-line">
                                            <td colspan="8">
                                                <button class="btn sumb--btn" type="button" id="addnewline" onclick="addInvoiceParts()" ><i class="fa-solid fa-circle-plus"></i>Add New Line</button> 
                                            </td>
                                        </tr>
                                        
                                        <tr class="invoice-separator">
                                            <td colspan="8">hs</td>
                                        </tr>

                                        <tr class="invoice-total--subamount">
                                            <td colspan="4" rowspan="3"></td>
                                            <td colspan="2">Subtotal (excl GST)</td>
                                            <td colspan="2">
                                                <input type="text" id="invoice_sub_total" name="invoice_sub_total" readonly value="{{!empty($invoice_details) ? number_format($invoice_details['invoice_sub_total'], 2) : 0 }}">
                                            </td>
                                        </tr>

                                        <tr class="invoice-total--gst">
                                            <td colspan="2" id="invoice_total_gst_text" >Total GST {{!empty($invoice_details)}}</td>
                                            <td colspan="2">
                                                <input type="text" id="invoice_total_gst" name="invoice_total_gst" readonly value="{{!empty($invoice_details) ? number_format($invoice_details['invoice_total_gst'], 2) : 0 }}">
                                            </td>
                                        </tr>

                                        <tr class="invoice-total--amountdue">
                                            <td colspan="2"><strong>Amount Due</strong></td>
                                            <td colspan="2">
                                                <input class="grandtotal" type="text" id="invoice_total_amount" name="invoice_total_amount" readonly value="{{!empty($invoice_details) ? number_format($invoice_details['invoice_total_amount'], 2) : 0 }}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        
                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-2 col-md-3 col-sm-12 col-xs-12 col-12">
                                    <a href="/invoice" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i>Back</a>
                                </div> 
                                <div class="col-xl-8 col-lg-10 col-md-9 col-sm-12 col-xs-12 col-12">
                                    <input type="hidden" id="invoice_part_ids" name="invoice_part_total_count" value="{{!empty($invoice_details) ? $invoice_details['invoice_part_total_count'] : '[0]' }}" />
                                    <input type="hidden" name="invoice_status" value="{{!empty($invoice_details && $invoice_details['invoice_status']) ? $invoice_details['invoice_status'] : ''}}">
                                    <!-- <input type="sumbit" id="" name="send_invoice_to_client" class="btn sumb--btn" value="Send Invoice" /> -->
                                    <?php if($type=='edit' && $invoice_details['invoice_status'] == 'Unpaid'){?>
                                    <button type="button" name="" class="btn sumb--btn" onclick="sendInvoice()"><i class="fa-solid fa-paper-plane"></i>Send Invoice</button>
                                    <?php }?>
                                    <button type="reset" class="btn sumb--btn reset--btn"><i class="fa fa-ban"></i>Clear Invioce</button>
                                    <button type="button" class="btn sumb--btn preview--btn" onclick="previewInvoice()"><i class="fa-solid fa-eye" ></i>Preview</button>
                                    <button type="submit" name="save_invoice"  class="btn sumb--btn" value="Save Invoice"><i class="fa-solid fa-floppy-disk"></i>Save</button>
                                
                                    <!-- <div class="btn-group">
                                        <button type="submit" name="save_invoice"  class="btn sumb--btn" value="Save Invoice"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                                        <button type="button" class="btn sumb--btn dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button type="submit" name="send_invoice" class="btn" value="Send Invoice"> Send Invoice</button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
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

    function sendInvoice(){
        <?php if(!empty($invoice_details) && $type == 'edit') {?>
            $('#send_invoice_modal').modal({
                backdrop: 'static',
                keyboard: true, 
                show: true
            });
            var total = parseFloat('{{ $invoice_details['invoice_total_amount'] }}').toFixed(2);
            var due_date = $.datepicker.formatDate( "D dd-M-yy", new Date());
            console.log(new Date());
            $("#send_invoice_to_emails").val('{{$invoice_details['client_email']}}');
            $("#send_invoice_from").val('{{$userinfo[1]}}');
            $("#send_invoice_subject").val("Invoice INV-00000"+'{{$invoice_details['invoice_number'] }}' + ' from '+ '{{$userinfo[1]}}'+ ' for '+ '{{$invoice_details['client_email']}}');
            $("#send_invoice_message").val("Hi,"+"\n\n" + "Here's invoice INV- 00000 {{ $invoice_details['invoice_number'] }} for $ "+total+"."+"\n\n" +"The amount outstanding of $ "+total+" is due on {{ $invoice_details['invoice_due_date'] }}."+"\n\n" + "Thanks, "+"\n\n" + "{{$userinfo[1]}}");
        <?php }?>
    }
    $(function() {
        
        <?php if(!empty($invoice_details) && (isset($invoice_details['invoice_sent']) && $invoice_details['invoice_sent'] || $invoice_details['invoice_status'] == 'Voided' || $invoice_details['invoice_status'] == 'Paid') ){ ?>
            $("#invoice_form :input").prop('disabled', true);
        <?php }?>
        $('#invoice_issue_date').datepicker().datepicker('setDate', 'today');
        
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
                                var myhtml = '<tr id="part_'+rparse.id+'" data-type="'+rparse.type+'"><td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'" style="text-align: left;">'+rparse.desc+'</td><td id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td><button class="btn sumb--btn editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn sumb--btn delepart" type="button" data-partid="'+rparse.id+'"><i class="fas fa-trash-alt"></i></button></td></tr>';
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
                                var myhtml = '<td scope="row" id="part_qty_'+rparse.id+'">'+rparse.qty+'</td><td id="part_desc_'+rparse.id+'" style="text-align: left;">'+rparse.desc+'</td><td id="part_uprice_'+rparse.id+'" data-amt="'+rparse.upriceno+'">'+rparse.uprice+'</td><td id="part_amount_'+rparse.id+'" data-amt="'+rparse.amountno+'">'+rparse.amount+'</td><td ><button class="btn sumb--btn editpart" type="button" data-partid="'+rparse.id+'" data-toggle="modal" data-target="#particulars"><i class="fa-regular fa-pen-to-square"></i></button> <button class="btn sumb--btn delepart" type="button" data-partid="'+rparse.id+'"><i class="fas fa-trash-alt"></i></button></td>';
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

        $('#partstable').on('focus', '.autoresizing', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    

    
</script>
</body>

</html>
<!-- end document-->