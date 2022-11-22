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
                            <section>
                                <h3 class="sumb--title">Files</h3>
                            </section>
                
                            @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <strong>{{ $message }}</strong>
                            </div>
                          @endif
                          @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                          @endif

                            <div class="container mt-12">
                                <h3 class="text-center mb-12">Upload File</h3>
                                <form action="{{route('store')}}" method="POST" enctype="multipart/form-data">                                  
                                    @csrf


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="form-control" type="file" name="file" placeholder="Choose file" id="file">                                        
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                    <button type="submit" name="submit" class="btn btn-primary">
                                        Upload Files
                                    </button>
                                </div>
                                </form>
                            </div>

                        </div> 
                    </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr class="table-warning">
                                    <td>ID</td>
                                    <td>Name</td>
                                    <td class="text-center">Action</td>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($doclist as $doclisting)
                                <tr>
                                    <td>{{$doclisting->id}}</td>
                                    <td>{{$doclisting->name}}</td>
                                    <td class="text-center">
                                    <a href="/doc-edit/?id={{$doclisting->id}}" class="btn btn-primary btn-sm"">Edit</a>
                                    {{-- <form action="{{ route('DocumentUploadController.destroy', $doclisting->id) }}"  method="post" style="display: inline-block"> --}}
                                        <form action="/destroy/?id={{$doclisting->id}}" method="post" style="display: inline-block">
                                        @csrf
                                        @method('DELETE')
                                            <button class="btn btn-danger btn-sm"" type="submit">Delete</button>
                                        </form>
                                        {{-- <form action="/downloadfile/?id={{$doclisting->id}}" method="get" style="display: inline-block">
                                            <button class="btn btn-success btn-sm"" type="submit">Download</button>
                                        </form>    --}}
                                        <a href="/downloadfile/?id={{$doclisting->id}}" class="btn btn-success btn-sm">Download</a>
                                        {{-- <a href="{{ route('DocumentUploadController.downloadfile', $doclisting->id) }}" class="btn btn-success btn-sm" enctype="multipart/form-data">Download</a> --}}
                                    </td>
                                    
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div> 
                </div>                      
                      

                </div>
            </div>
        </div>
    <!-- END MAIN CONTENT-->
</div>
<!-- END PAGE CONTAINER-->


@include('includes.footer')
</body>

</html>
<!-- end document-->