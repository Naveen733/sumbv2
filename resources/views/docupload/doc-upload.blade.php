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
                    <br>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive table--no-card m-b-40">
                            <table class="table table-borderless table-striped table-earning">
                                <thead class="table-dark">
                                    <tr>
                                        <td class="text-center">ID</td>
                                        <td class="text-center">Name</td>
                                        <td class="text-center">File type</td>
                                        <td class="text-center">Filesize</td>
                                        <td class="text-center">Date Created</td>
                                        <td class="text-center">Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($doclist as $doclists)
                                    <tr>
                                        <td>{{$doclists->id}}</td>
                                        <td>{{$doclists->name}}</td>
                                        <td>{{$doclists->extensionname}}</td>
                                        <td>{{ number_format($doclists->filesize)." "."kb" }}</td>                                                            
                                        <td>{{$doclists->created_at->format('Y-m-d')}}</td>
                                        <td class="text-center">
                                            @csrf
                                            <a href="/docview/?id={{$doclists->id}}" class="btn btn-secondary btn-sm">View</a>                                                                                        
                                            <a href="javascript:void(0)" onclick="viewDocument('<?php echo '/docview/?id='.$doclists->id;?>')" class="btn btn-info btn-sm" >modal</a>
                                            <a href="/doc-edit/?id={{$doclists->id}}" class="btn btn-primary btn-sm">Edit</a>
                                           
                                            <form action="/destroy/?id={{$doclists->id}}" method="post" style="display: inline-block">
                                                @csrf
                                                @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this item?')" type="submit" >Delete</button>
                                            </form>

                                            <a href="/downloadfile/?id={{$doclists->id}}" class="btn btn-success btn-sm">Download</a>                                          
                                        </td>                                        
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>


                        </div>
                        <div class="d-flex">                            
                            {{ $doclist->links() }}
                        </div>                          
                    </div>
                </div>                      
                     

                </div>
            </div>
        </div>
    <!-- END MAIN CONTENT-->
</div>

<!-- END PAGE CONTAINER-->
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <iframe id="exampleModaldata" height="100%" width="100%"></iframe>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

            </div>
        </div>
        </div>
    </div>

    <script language="JavaScript" type="text/javascript">    
        function viewDocument(fileurl) {
            $('#exampleModaldata').attr("src", fileurl);
            $('#exampleModal').modal('show');
        }
    </script>

@include('includes.footer')

</body>

</html>
<!-- end document-->
