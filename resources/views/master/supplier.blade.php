@extends('layouts.app')
@section('content')
<div class="section-header">
    <h1>Supplier</h1>
    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Supplier</a></div>
    </div>
</div>

<div class="section-body">
    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#create-modal"><i
            class="fas fa-plus"></i>
        Tambah Supplier</button>
    <h2 class="section-title">Supplier</h2>
    <p class="section-lead">
        Berikut adalah data supplier</a>.
    </p>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>List Supplier</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="datatable">
                            <thead>
                                <th>Id</th>
                                <th>Nama Supplier</th>
                                <th>Sales</th>
                                <th>Alamat</th>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="create-modalLabel">Tambah Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" novalidate="">
                        <div class="form-group">
                            <label>Nama Supplier</label>
                            <input type="text" class="form-control" required="" id="nama_supplier" name="nama_supplier" placeholder="Nama Supplier">
                            <div class="invalid-feedback">
                                Nama supplier harus diisi.
                            </div>
                        </div>
                       <div class="form-group">
                            <label>Nama Sales</label>
                            @php $all_signatures = \App\Models\Signature::all(); @endphp
                            <select class="form-control select2" id="nama_sales" name="nama_sales">
                                <option value="">Pilih Sales</option>
                                @foreach($all_signatures as $signature)
                                    <option value="{{ $signature->name }}">{{ $signature->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Nama Sales harus diisi.
                            </div>
                        </div>
                    <div class="form-group">
                        <label>Alamat Supplier</label>
                        <textarea name="alamat" id="alamat" class="form-control"
                            style="height: 100px;" placeholder="Alamat"></textarea>
                        <div class="invalid-feedback">
                            Alamat harus diisi.
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit-modalLabel">Edit Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation-edit" novalidate="">
                    <input type="hidden" name="id" id="id_supplier_edit" class="form-control">
                      <div class="form-group">
                            <label>Nama Supplier</label>
                            <input type="text" class="form-control" required="" id="nama_supplier_edit" name="nama_supplier_edit">
                            <div class="invalid-feedback">
                                Nama supplier harus diisi.
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label>Nama Sales</label>
                            <select class="form-control select2" id="nama_sales_edit" name="nama_sales_edit">
                                <option value="">Pilih Sales</option>
                                @foreach($all_signatures as $signature)
                                    <option value="{{ $signature->name }}">{{ $signature->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Nama Sales harus diisi.
                            </div>
                        </div>
                     
                    <div class="form-group">
                        <label>Alamat Supplier</label>
                        <textarea name="alamat_edit" id="alamat_edit" class="form-control"
                            style="height: 100px;"></textarea>
                        <div class="invalid-feedback">
                            Alamat harus diisi.
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
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};

  
    var table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('supplier/data') }}",
        columns: [
            { data: 'id', name: 'id'},
            { data: 'nama_supplier', name: 'nama_supplier'},
            { data: 'sales', name: 'sales'},
            { data: 'alamat', name: 'alamat'},
            { data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

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
            $('.needs-validation').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $.ajax({
                url: "{{ url('supplier') }}",
                type: "post",
                data: JSON.stringify(data),
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
        }
    });

    $(document).on('click','.btn-edit', function(e) {
        e.stopPropagation();
        let id = $(this).data('id');
        $.ajax({
            url: "{{url('supplier/get')}}/"+id,
            type: "get",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function(response){
                
                $('#id_supplier_edit').val(response.id);
                $('#nama_supplier_edit').val(response.nama_supplier);
                $('#nama_sales_edit').val(response.sales);
                $('#alamat_edit').val(response.alamat);
                $('#update').attr('data-id', id);
                $('#edit-modal').modal('show');
            }
        });
        
    });

    $(document).on('click','.btn-delete', function(e) {
        e.stopPropagation();
        let data = {}
        data.id = $(this).data('id');
        Swal.fire({
          title: 'Anda Yakin?',
          text: "Data Supplier ini akan dihapus!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.value) {
            $.ajax({
                url: "{{ url('supplier/delete') }}",
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
            $('.needs-validation-edit').find('.form-control').each(function(){
                data[$("#"+$(this).attr('id')).attr("name")] = $("#"+$(this).attr('id')).val();
            });
            $.ajax({
                url: "{{ url('supplier/update') }}",
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
    });
});
</script>
@endsection
@endsection
