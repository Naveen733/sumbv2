@include('includes.head')
@include('includes.user-header')

<!--  Add new account pop-up modal starts -->
<div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <!-- <form action="/edit-chart-account" method="post" enctype="multipart/form-data" class="form-horizontal" id="">     -->
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Account Type </label>
                            @if(!empty($chart_accounts_types))
                                <select class="form-control" id="chart_accounts_type_id" name="chart_accounts_type_id">
                                    <option value="">select</option>
                                    @foreach($chart_accounts_types as $chart_accounts)
                                        @if(!empty($chart_accounts))
                                            <optgroup label="{{$chart_accounts['chart_accounts_name']}}">
                                                @foreach($chart_accounts['chart_accounts_types'] as $types)
                                                    <option id="chart_accounts_id_{{$types['id']}}" name="chart_accounts_id" value="{{!empty($chart_accounts) ? $chart_accounts['id'] : ''}}"  hidden></option>
                                                    <option value="{{$types['id']}}">{{!empty($types['chart_accounts_type']) ? $types['chart_accounts_type'] : ''}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                            <div class="" role="alert" id="chart_accounts_type_error"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for=""> Code </label>
                            <div class="form--inputbox">
                                <input type="text" required  class="form-control" id="chart_accounts_parts_code" name="chart_accounts_parts_code" placeholder=""  value="">
                            </div>
                            <div class="" role="alert" id="chart_accounts_parts_code_error"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="">Name</label>
                            <div class="form--inputbox">
                                <input type="text" required  class="form-control" id="chart_accounts_parts_name" name="chart_accounts_parts_name" placeholder=""  value="">
                            </div>
                            <div class="" role="alert" id="chart_accounts_parts_name_error"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="form-input--wrap">
                            <label class="form-input--question" for="" >Description</label>
                            <textarea class="form-control" id="chart_accounts_description" name="chart_accounts_description"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <label class="form-input--question" for="">Tax Rate</label>
                        <div class="input-group mb-3">
                            @if(!empty($tax_rates))
                                <select class="custom-select form-control" id="chart_accounts_tax_rate" name="chart_accounts_tax_rate" value="" required>
                                    <option selected value="">Choose...</option>
                                    @foreach($tax_rates as $tax_rate)
                                        <option hidden="hidden" id="{{$tax_rate['id']}}" value="{{$tax_rate['tax_rates']}}"></option>
                                        <option value="{{$tax_rate['id']}}">{{$tax_rate['tax_rates_name']}}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="" role="alert" id="invoice_chart_accounts_tax_rate_error"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="chart_accounts_part_id" name="chart_accounts_part_id" value="">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" name="edit_chart_accounts_parts" onclick="editAccountPartsDetails()">Save</button>
            </div>
        </div>
        <!-- </form> -->
    </div>
</div>
<!-- Add new account modal ends -->

<!-- PAGE CONTAINER-->
<div class="page-container">
    @include('includes.user-top')
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">

                <section>
                    <h3 class="sumb--title">Chart of Accounts</h3>
                </section>
                <br/><br/>
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
                            <form action="/chart-accounts"  method="GET" enctype="multipart/form-data" id="search_form">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    @if(!empty($chart_account))
                                            <li class="nav-item">
                                                <a class="nav-link {{$tab == 'all_accounts' ? 'active' : '' }}" id="{{$tab}}-tab" data-toggle="tab" onclick="window.location='/chart-accounts?tab=all_accounts&id='" href="#" role="tab" aria-controls="all_accounts" aria-selected="{{$tab == 'all_accounts' ? true : false }}">All Accounts</a>
                                            </li>
                                        @foreach($chart_account as $account_heading)
                                            <li class="nav-item">
                                                <a class="{{$tab == $account_heading['chart_accounts_name'] ? 'nav-link active' : 'nav-link' }}" id="{{$tab}}-tab" data-toggle="tab" onclick="window.location='/chart-accounts?tab={{$account_heading['chart_accounts_name']}}&id={{$account_heading['id']}}'" href="#" role="tab" aria-controls="{{$account_heading['chart_accounts_name']}}" aria-selected="{{$tab == $account_heading['chart_accounts_name'] ? true : false }}">{{$account_heading['chart_accounts_name']}}</a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <br>

                                <input id="id" type="hidden" name="id" value="{{!empty($accounts_id) ? $accounts_id: ''}}">
                                <input id="tab" type="hidden" name="tab" value="{{!empty($tab) ? $tab: ''}}">

                                <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <div class="form-input--wrap">
                                            <div class="form--inputbox ">
                                                <div class="col-12">
                                                    <input type="text" class="form-control" id="search_code_name_desc" name="search_code_name_desc" placeholder=""  value="{{!empty($search_code_name_desc) ? $search_code_name_desc : ''}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-input--wrap" style="margin-top:0px">
                                            <button type="button" name="search_invoice" class="btn sumb--btn" value="Search" onclick="searchItems(null, null)">Search</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-content" id="myTabContent">
                                    <div class="sumb--recentlogdements sumb--putShadowbox">
                                        <div class="table-responsive">
                                            <table class="invoice_list">
                                                <thead>
                                                    <tr>
                                                        <th style="border-top-left-radius: 7px;" id="chart_accounts_particulars_code" onclick="searchItems('chart_accounts_particulars_code', '{{!empty($orderBy) && $orderBy == 'chart_accounts_particulars_code' ? $direction  : 'ASC'}}', '{{$tab}}', '{{$accounts_id}}')" > Code </th>
                                                        <th  id="chart_accounts_particulars_name" onclick="searchItems('chart_accounts_particulars_name', '{{!empty($orderBy) && $orderBy == 'chart_accounts_particulars_name' ? $direction  : 'ASC'}}', '{{$tab}}','{{$accounts_id}}')">Name</th>
                                                        <th id="chart_accounts_particulars_type" >Type</th>
                                                        <th  id="chart_accounts_particulars_tax_rate" >Tax Rate</th>
                                                        <th >YTD</th>
                                                    </tr>
                                                </thead>
                                                @foreach($chart_account_particulars as $particular)
                                                    <div class="tab-pane fade show active" id="{{$tab}}" role="tabpanel" aria-labelledby="{{$tab}}-tab">
                                                        <tbody>
                                                            <tr onclick="getAccountPartsDetails('{{$particular['id']}}')">
                                                                <td>{{$particular['chart_accounts_particulars_code']}}</td>
                                                                <td>{{$particular['chart_accounts_particulars_name']}}</td>
                                                                <td>{{$particular['chart_accounts_types']['chart_accounts_type']}}</td>
                                                                <td>{{$particular['invoice_tax_rates']['tax_rates_name']}}</td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </div>
                                                @endforeach
                                            </table>
                                        </div>
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
    <?php if(!empty($orderBy)){?>
        <?php if($direction == 'ASC'){?> 
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-down"></i>');    
        <?php } if($direction == 'DESC'){?>
            $("#"+ '{{$orderBy}}').append('&nbsp;<i class="fas fa-sort-up"></i>');    
        <?php }?> 
    <?php }?>

    function searchItems(orderBy, direction, tab, accounts_id){
        var orderBy = orderBy ? orderBy : 'chart_accounts_particulars_code';
        var direction = direction ? direction : 'ASC';
        var tab = tab ? tab : 'all_accounts';
        var accounts_id = accounts_id ? accounts_id : '';

        $("#search_form").append('<input id="orderBy" type="hidden" name="orderBy" value='+orderBy+' >');
        $("#search_form").append('<input id="direction" type="hidden" name="direction" value='+direction+'>');
        $("#search_form").submit();
    }

    function getAccountPartsDetails(chart_accounts_parts_id)
    {        
        $("#chart_accounts_type_id").val('');
        $("#chart_accounts_part_id").val('');
        $("#chart_accounts_parts_code").val('');
        $("#chart_accounts_parts_name").val('');
        $("#chart_accounts_description").val('');
        $("#chart_accounts_tax_rate").val('');
        
        $('#editAccountModal').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
            method: "GET",
            url: "/chart-accounts-parts/" + chart_accounts_parts_id,
            success:function(response){
                try{
                    response = JSON.parse(response);
                    if(response && response.status == "success"){
                        
                        $("#chart_accounts_type_id").val(response.data['chart_accounts_type_id']);
                        $("#chart_accounts_part_id").val(response.data['id']);
                        $("#chart_accounts_parts_code").val(response.data['chart_accounts_particulars_code']);
                        $("#chart_accounts_parts_name").val(response.data['chart_accounts_particulars_name']);
                        $("#chart_accounts_description").val(response.data['chart_accounts_particulars_description']);
                        $("#chart_accounts_tax_rate").val(response.data['invoice_tax_rates']['id']);
                    }else if(response.status == "error"){
                        alert(esponse.err);
                    }
                }catch(error){
                }
            },
            error:function(error){ 
            }
        });
    }

    function editAccountPartsDetails()
    {
        const account_id = $("#chart_accounts_type_id").val();
        var post_data = {
            chart_accounts_id: $("#chart_accounts_id_"+account_id).val(),
            chart_accounts_type_id: $("#chart_accounts_type_id").val(),
            chart_accounts_part_id: $("#chart_accounts_part_id").val(),
            chart_accounts_parts_code: $("#chart_accounts_parts_code").val(),
            chart_accounts_parts_name: $("#chart_accounts_parts_name").val(),
            chart_accounts_description: $("#chart_accounts_description").val(),
            chart_accounts_tax_rate: $("#chart_accounts_tax_rate").val(),
        }

        $('#editAccountModal').modal({
            backdrop: 'static',
            keyboard: true, 
            show: true
        });

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
        method: "POST",
        url: "/edit-chart-account/"+post_data.chart_accounts_part_id,
        data: post_data,
        success:function(response){
            try{
                response = JSON.parse(response);
                if(response && response.status == "success"){

                    $("#chart_accounts_id").val('');
                    $("#chart_accounts_type_id").val('');
                    $("#chart_accounts_part_id").val('');
                    $("#chart_accounts_parts_code").val('');
                    $("#chart_accounts_parts_name").val('');
                    $("#chart_accounts_description").val('');
                    $("#chart_accounts_tax_rate").val('');
                    $(".close").click();
                    location.reload();
                }else if(response.status == "error"){
                    alert(esponse.err);
                   
                }
            }catch(error){
            }
        },
            error:function(error){ 
            }
        });
    }

</script>
<!-- end document-->