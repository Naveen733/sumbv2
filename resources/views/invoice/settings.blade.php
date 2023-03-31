@include('includes.head')
@include('includes.user-header')

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body" id="invoice_pdf">
            </div>
        </div>
    </div>
</div>

<div class="page-container">

    @include('includes.user-top')
    
    <!-- MAIN CONTENT-->
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <section>
                    <h3 class="sumb--title">Invoice Settings</h3>
                </section>
                <section>
                    <form action="/invoice/settings/{{$type}}" method="post" enctype="multipart/form-data" class="form-horizontal" id="invoice_form">
                        @if (\Session::has('success'))
                                <div class="alert alert-success">
                                    <ul>
                                        <li>{!! \Session::get('success') !!}</li>
                                    </ul>
                                </div>
                            @endif
                        @csrf

                        <hr class="form-cutter">
                        
                        <h4 class="form-header--title">Business Details</h4>
                        
                        <div class="row form-group">
                            <div class="col-2">
                                <label for="logo_file" class=" form-control-label">File input</label>
                            </div>
                            <div class="col-4">
                                <input type="file" name="logo_file" id="logo_file" class="form-control-file" onchange="logoUpload()" value=""><br>
                                <input type="hidden" name="logo_path" id="logo_path" value="{{!empty($invoice_settings) ? $invoice_settings['business_logo'] : '' }}">
                            </div>
                            @if (empty($invoice_settings['business_logo']))
                            <div class="col-md-12 mb-2">
                                <img id="preview-image-before-upload" src="{{env('APP_PUBLIC_DIRECTORY').'no-inage-found.png'}}"
                                    alt="preview image" style="max-height: 250px;">
                            </div>
                            @else
                            <div class="col-md-12 mb-2">
                                <img id="preview-image-before-upload" src="{{ !empty($invoice_settings && $invoice_settings['business_logo']) ? env('APP_PUBLIC_DIRECTORY').$userinfo[0].'/'.$invoice_settings['business_logo'] : '' }}"
                                    alt="preview image" style="max-height: 250px;">
                            </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business ABN </label>
                                    <div class="form--inputbox">
                                        <!-- <div class="col-12"> -->
                                            <input type="text"  class="form-control @error('business_abn') is-invalid @enderror" id="business_abn" name="business_abn" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_abn'] : old('business_abn') }}">
                                        <!-- </div> -->
                                    </div>
                                    @error('business_abn')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Name </label>
                                    <div class="form--inputbox">
                                        <!-- <div class="col-12"> -->
                                            <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_name'] : old('business_name')}}">
                                        <!-- </div> -->
                                    </div>
                                    @error('business_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Email </label>
                                    <div class="form--inputbox">
                                        <!-- <div class="col-12"> -->
                                            <input type="text"  class="form-control @error('business_email') is-invalid @enderror" id="business_email" name="business_email" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_email'] : old('business_email') }}">
                                        <!-- </div> -->
                                    </div>
                                    @error('business_email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Phone Number </label>
                                    <div class="form--inputbox">
                                        <!-- <div class="col-12"> -->
                                            <input type="text"  class="form-control @error('business_phone') is-invalid @enderror" id="business_phone" name="business_phone" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_phone'] : old('business_phone')}}">
                                        <!-- </div> -->
                                    </div>
                                    @error('business_phone')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="">Business Address </label>
                                    <div class="form--inputbox">
                                        <!-- <div class="col-12"> -->
                                            <input type="text"  class="form-control @error('business_address') is-invalid @enderror" id="business_address" name="business_address" placeholder=""  value="{{!empty($invoice_settings) ? $invoice_settings['business_address'] : old('business_address')}}">
                                        <!-- </div> -->
                                    </div>
                                    @error('business_address')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-input--wrap">
                                    <label class="form-input--question" for="" >Terms & Payment Advice (Invoice and Statement)</label>
                                    <textarea class="form-control" id="business_terms_conditions" name="business_terms_conditions">{{!empty($invoice_settings) ? $invoice_settings['business_terms_conditions'] : ''}}</textarea>
                                </div>
                            </div>
                        </div>
                                
                        <hr class="form-cutter">
                        <div class="form-check">

                        <h4 class="form-header--title">Invoice Templates</h4>
                            <p><i>Please select one of the following templates as your invoice template </i> </p>
                        <div class="row">
                            <div class="column">
                            <input type="radio" class="form-check-input" id="radio1" name="business_invoice_format" value="format001" {{!empty($invoice_settings) || (empty($invoice_settings['business_invoice_format']) || $invoice_settings['business_invoice_format'] == 'format001') ? 'checked' : ''}}>
                            <img src="{{env('APP_PUBLIC_DIRECTORY').'format001.jpg'}}" alt="Nature" style="width:100%; border: solid 1px" class="btn btn-lg" data-toggle="modal" data-target="#myModal" onclick="openPopUpModel('format001.pdf')">
                            </div>
                            <div class="column">
                                <input type="radio" class="form-check-input" id="radio2" name="business_invoice_format" value="format002" {{!empty($invoice_settings) && $invoice_settings['business_invoice_format'] == 'format002' ? 'checked' : ''}}>
                                <img src="{{env('APP_PUBLIC_DIRECTORY').'format002.jpg'}}" alt="Snow" style="width:100%; border: solid 1px" class="btn btn-lg" data-toggle="modal" data-target="#myModal" onclick="openPopUpModel('format002.pdf')">
                            </div>
                            <div class="column">
                                <input type="radio" class="form-check-input" id="radio3" name="business_invoice_format" value="format003" {{!empty($invoice_settings) && $invoice_settings['business_invoice_format'] == 'format003' ? 'checked' : ''}}>
                                <img src="{{env('APP_PUBLIC_DIRECTORY').'format003.jpg'}}" alt="Mountains" style="width:100%; border: solid 1px" class="btn btn-lg" data-toggle="modal" data-target="#myModal"  onclick="openPopUpModel('format003.pdf')">
                            </div>
                        </div>
                       
                        <div class="form-navigation">
                            <div class="form-navigation--btns row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 col-12">
                                    <a href="/invoice" class="btn sumb--btn"><i class="fa-solid fa-circle-left"></i> Back</a>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-xs-12 col-12">
                                    <input type="hidden" name="invoice_settings_id" value="{{!empty($invoice_settings) ? $invoice_settings['id'] : ''}}">
                                    <button type="submit" name="save_invoice_settings"  class="btn sumb--btn" value="Save Settings"><i class="fa-solid fa-floppy-disk"></i> Save</button>
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

function openPopUpModel(format){
    $("#invoice_pdf").html('');

    $("#invoice_pdf").append('<embed src="{{env('APP_PUBLIC_DIRECTORY')}}'+format+'" frameborder="0" width="100%" height="400px">\
                <div class="modal-footer">\
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\
                </div>');
    $('#myModal').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });
}
    $(document).ready(function (e) {
        $('#logo_file').change(function(){
                
        let reader = new FileReader();

        reader.onload = (e) => { 

            $('#preview-image-before-upload').attr('src', e.target.result); 
        }

        reader.readAsDataURL(this.files[0]); 
        
        });
    });

    function logoUpload(){
        var fd = new FormData();
        fd.append( "fileInput", $("#logo_file")[0].files[0]);

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        $.ajax({
            method: "POST",
            url: "{{ url('/invoice-logo-upload') }}",
            data: fd,
            processData: false,
            contentType: false,
            success:function(response){
                try{
                    response = JSON.parse(response);
                    if(response && response.status == "success"){
                        $("#logo_path").val(response.logo);
                    }
                }catch(error){ }
            },
        });
    }


</script>
</body>

</html>
<!-- end document-->