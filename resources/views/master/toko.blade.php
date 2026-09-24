@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Toko</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Toko</a></div>
    </div>
</div>

<div class="section-body">
    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#create-modal"><i
            class="fas fa-plus"></i>
        Tambah Toko</button>
    <h2 class="section-title">Toko</h2>
    <p class="section-lead">
        Berikut adalah data toko melindastore</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Toko</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Nama Toko</th>
                                <th>Alamat Toko</th>
                                <th>Logo Toko</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('modal')
<!-- Modal -->
<div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="create-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modalLabel">Tambah Toko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate="" enctype="multipart/form-data" method="post">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Toko</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control text-data" required="" id="nama_toko" name="nama_toko" placeholder="Nama Toko">
                            <div class="invalid-feedback">
                                Nama toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alamat Toko</label>
                        <div class="col-sm-9">
                            <textarea style="height: 100px" name="alamat_toko" class="form-control text-data" id="alamat_toko" required="" placeholder="Alamat Toko"></textarea>
                            <div class="invalid-feedback">
                                Alamat toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Logo Toko</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="logo_toko" required="">
                            <div class="invalid-feedback">
                                Logo Toko harus diisi.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Edit Toko</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation-edit" novalidate="">
                    <input type="hidden" name="id" id="edit_id" class="form-control text-data-edit">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Toko</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control text-data-edit" required="" id="edit_nama_toko" name="edit_nama_toko" placeholder="Nama Toko">
                            <div class="invalid-feedback">
                                Nama toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Alamat Toko</label>
                        <div class="col-sm-9">
                            <textarea name="edit_alamat_toko" class="form-control text-data-edit" id="edit_alamat_toko" style="height: 100px;" required="" placeholder="Alamat Toko"></textarea>
                            <div class="invalid-feedback">
                                Alamat toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Logo Toko</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="edit_logo_toko">
                            <div class="invalid-feedback">
                                Logo Toko harus diisi.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="gallery gallery-fw" data-item-height="100" id="image-div">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="update" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Content-Type': "application/json"
        }
    });
    
    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('toko/data') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nama_toko', name: 'nama_toko' },
            { data: 'alamat_toko', name: 'alamat_toko' },
            { data: 'image', name: 'image',
            render: function( data, type, full, meta ) {
                if(data == ''){
                    return "<img src=\"/public/assets/img/news/img01.jpg\" height=\"100\" width =\"150\" alt='No Image'/>";
                }else{
                    return "<img src=\"/public/uploads/" + data + "\" height=\"100\" width =\"200\" alt='No Image'/>";
                }
            }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    function readFile(files,data) {
              
      if (files[0]) {
        
        var FR= new FileReader();
        
        FR.addEventListener("load", function(e) {
            let datas = {}
            datas.nama_toko = data.nama_toko;
            datas.alamat_toko = data.alamat_toko;
            datas.image = e.target.result;
            $.ajax({
                url: "{{ url('toko') }}",
                type: "post",
                data: JSON.stringify(datas),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    $('#create-modal').modal('hide');
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been saved.',
                    }).then((result) => {
                        $('#datatable').DataTable().ajax.reload();
                        $('.needs-validation').find('.form-control').each(function(){
                            $("#"+$(this).attr('id')).val('');
                        });
                    });
                }
            });
        }); 
        
        FR.readAsDataURL( files[0] );
      }
      
    }

    function readFileEdit(files,data) {
              
      if (files[0]) {
        var FR= new FileReader();
        
        FR.addEventListener("load", function(e) {
            data.image = e.target.result;
            $.ajax({
                url: "{{ url('toko/update') }}",
                type: "post",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    $('#edit-modal').modal('hide');
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been updated.',
                    }).then((result) => {
                        $('#datatable').DataTable().ajax.reload();
                    });
                }
            });
        }); 
        
        FR.readAsDataURL( files[0] );
      }else{
        $.ajax({
            url: "{{ url('toko/update') }}",
            type: "post",
            data: JSON.stringify(data),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                $('#edit-modal').modal('hide');
                Swal.fire({
                    title: 'Success',
                    icon: 'success',
                    text: 'Data has been updated.',
                }).then((result) => {
                    $('#datatable').DataTable().ajax.reload();
                });
            }
        });
      }
      
    }

    $(document).on('click','#save', function(e) {
        e.stopPropagation();
        $('.needs-validation').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            $('.needs-validation').find('.text-data').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });

            readFile($('#logo_toko')[0].files, data);
        }
    });

    $(document).on('click','.btn-edit', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let alamat = $(this).data('alamat');
        let image = $(this).data('image');
        $('#edit_id').val(id);
        $('#edit_nama_toko').val(name);
        $('#edit_alamat_toko').val(alamat);
        if(image != ''){
            $('#image-div').html('<div class="gallery-item" data-image="/uploads/'+image+'" data-title="Image 1" href="/uploads/'+image+'" title="Image 1" style="height: 200px; background-image: url(&quot;public/uploads/'+image+'&quot;);"></div>');
        }else{
            $('#image-div').html('');
        }
        $('#update').attr('data-id', id);
        $('#edit-modal').modal('show');
    });

    $(document).on('click','.btn-delete', function(e) {
        e.stopPropagation();
        let data = {}
        data.id = $(this).data('id');
        Swal.fire({
          title: 'Anda Yakin?',
          text: "Seluruh data barang yang berhubungan dengan toko ini akan dihapus",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.value) {
            $.ajax({
                url: "{{ url('toko/delete') }}",
                type: "post",
                data: JSON.stringify(data),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response){
                    Swal.fire({
                        title: 'Success',
                        icon: 'success',
                        text: 'Data has been deleted.',
                    }).then((result) => {
                        $('#datatable').DataTable().ajax.reload();
                    });
                }
            });
          }
      })
    });

    $(document).on('click','#update', function(e) {
        e.stopPropagation();
        $('.needs-validation-edit').addClass('was-validated');
        if($('.invalid-feedback:visible').length == 0){
            Swal.fire({
                title: 'Checking...',
                text: "Please wait",
                imageUrl: "{{ asset('waiting.gif') }}",
                showConfirmButton: false,
                allowOutsideClick: false
            });
            let data = {}
            $('.needs-validation-edit').find('.text-data-edit').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            readFileEdit($('#edit_logo_toko')[0].files, data);
        }
    });
});
</script>
@endsection
@endsection