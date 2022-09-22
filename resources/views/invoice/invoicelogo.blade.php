@include('includes.headwhite')

<div class="container-fluid">


        <form action="/invoice-logo-process" method="post" enctype="multipart/form-data" class="form-horizontal">
            @csrf
            <div class="row form-group">
                <div class="col-3">
                    <label for="logo_file" class=" form-control-label">File input</label>
                </div>
                <div class="col-9">
                    <input type="file" name="logo_file" id="logo_file" name="logo_file" class="form-control-file">
                </div>
            </div>
            <div class="row form-group">
                <div class="col-3">
                    &nbsp;
                </div>
                <div class="col-9">
                    <button type="submit" class="btn btn-primary">Save and process</button>
                </div>
            </div>
        </form>


</div>

@include('includes.footer')

</body>

</html>
<!-- end document-->